<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        // Real bearer tokens are PersonalAccessToken instances. Tests using
        // Sanctum::actingAs() get a TransientToken which lacks delete().
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }
        return response()->json(['message' => 'logged_out']);
    }
}
