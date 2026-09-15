<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Admin issues an invitation email to a new team member
     * 
     */

    public function sendInvite(Request $request)
    {
        //Ensure only admins can issue invites

        $this->authorize('create', User::class);
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email|unique:invites,email',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $invite = Invite::create([
            'email' => $validated['email'],
            'department_id' => $validated['department_id'] ?? null,
            'token' => Str::random(40),
            'expires_at' => now()->addDays(7),
        ]);

        // Send email with token
        Notification::route('mail', $invite->email)
            ->notify(new UserInvitedNotification($invite));

        return response()->json([
            'message' => 'Invitation sent successfully.',
        ], 201);
    }

    /**
     * Invited user accepts the invite, sets their password, and creates their account.
     */
    public function acceptInvite(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $invite = Invite::where('token', $validated['token'])->first();

        if (! $invite || ! $invite->isValid()) {
            return response()->json([
                'message' => 'This invitation link is invalid or has expired.',
            ], 422);
        }

        // Create the user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $invite->email,
            'password' => Hash::make($validated['password']),
            'department_id' => $invite->department_id,
        ]);

        // Mark invitation as used
        $invite->update(['accepted_at' => now()]);

        // Issue Sanctum token for immediate login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }
}
