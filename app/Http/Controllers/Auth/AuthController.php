<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'role' => 'in:admin,employer,candidate'
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            Log::warning('Registration attempt with existing email: ' . $validated['email']);
            return response()->json(['message' => 'User already exists.'], 409);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'candidate',
        ]);

        try {
            $user->sendEmailVerificationNotification();
            Log::info('Verification email sent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Registered. Please verify email.']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            Log::warning('Invalid login attempt: ' . $credentials['email']);
            return response()->json(['message' => 'Invalid login'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Log::notice('Unverified user attempted login: ' . $user->email);
            return response()->json(['message' => 'Email not verified.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('User logged in: ' . $user->email);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        Log::info('User logged out: ' . $user->email);

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
