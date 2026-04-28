<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Constants\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private const ALLOWED_ROLES = [Roles::CHEF];

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'invalid_credentials', 'message' => 'Email yoki parol noto\'g\'ri'], 401);
        }
        if (!in_array((int) $user->role_id, self::ALLOWED_ROLES, true)) {
            return response()->json(['error' => 'role_not_allowed', 'message' => 'Bu rol mobil ilovaga kirita olmaydi'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => (int) $user->role_id,
            ],
        ]);
    }
}
