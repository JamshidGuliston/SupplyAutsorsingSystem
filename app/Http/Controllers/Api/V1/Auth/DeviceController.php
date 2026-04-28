<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\ChefDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform' => 'required|in:android,ios',
            'fcm_token' => 'required|string|max:255',
            'device_model' => 'nullable|string|max:100',
            'app_version' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $currentTokenId = $currentToken instanceof \Laravel\Sanctum\PersonalAccessToken
            ? $currentToken->id
            : null;

        DB::transaction(function () use ($user, $data, $currentTokenId) {
            ChefDevice::where('user_id', $user->id)
                ->where('fcm_token', '!=', $data['fcm_token'])
                ->delete();

            if ($currentTokenId) {
                $user->tokens()->where('id', '!=', $currentTokenId)->delete();
            }

            ChefDevice::updateOrCreate(
                ['user_id' => $user->id, 'fcm_token' => $data['fcm_token']],
                [
                    'platform' => $data['platform'],
                    'device_model' => $data['device_model'] ?? null,
                    'app_version' => $data['app_version'] ?? null,
                    'last_seen_at' => now(),
                ],
            );
        });

        return response()->json(['message' => 'device_registered']);
    }
}
