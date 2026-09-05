<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class AuthController extends Controller
{
    /**
     * Issue a sanctum Bearer Token upon successful authentication.
     */

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessage([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $deviceName = $request->device_name ?? 'emerald-chat-client';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Authenticated successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department_id' => $user->department_id
            ],
        ]);
    }

    /**
     * Get the currently authenticated user with department details.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('department:id,name,slug');

        return  response()->json([
            'data' => $user
        ]);
    }

    /**
     * Revoke the token that was used to authenticate the current request.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully and token revoked.'
        ]);
    }
}
