<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller; 
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\LoginAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if (User::count() === 1) {
            $user->is_admin = true;
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Dispatch Welcome Email Notification
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send registration welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return response()->json(['message' => 'Your account has been disabled by an administrator.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Dispatch Login Alert Email Notification
        try {
            $ipAddress = $request->ip() ?? 'Unknown IP';
            $userAgent = $request->header('User-Agent') ?? 'Unknown Browser/Device';
            $loginTime = now()->format('Y-m-d H:i:s T');

            Mail::to($user->email)->send(new LoginAlertMail($user, $ipAddress, $userAgent, $loginTime));
        } catch (\Throwable $e) {
            Log::error('Failed to send login alert email: ' . $e->getMessage());
        }

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}