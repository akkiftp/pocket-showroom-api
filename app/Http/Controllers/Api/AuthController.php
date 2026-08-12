<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Services\FirebaseTokenVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function firebaseLogin(Request $request)
    {
        $data = $request->validate([
            'firebase_token' => ['required', 'string'],
        ]);

        $payload = FirebaseTokenVerifier::verify($data['firebase_token']);
        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Firebase ID token.',
            ], 401);
        }

        // Extract phone number from Firebase payload
        $rawPhone = $payload['phone_number'] ?? $payload['sub'] ?? null;
        if (!$rawPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number missing in Firebase token.',
            ], 401);
        }

        // Canonical phone format (+91XXXXXXXXXX)
        $cleanPhone = preg_replace('/[^0-9+]/', '', $rawPhone);
        if (!str_starts_with($cleanPhone, '+')) {
            $cleanPhone = '+91' . ltrim($cleanPhone, '0');
        }

        // Standard 10-digit number for matching
        $digitsOnly = preg_replace('/[^0-9]/', '', $cleanPhone);
        $shortPhone = (strlen($digitsOnly) >= 10) ? substr($digitsOnly, -10) : $digitsOnly;

        try {
            $user = User::where('phone', $cleanPhone)
                ->orWhere('phone', $shortPhone)
                ->orWhere('phone', '+' . $digitsOnly)
                ->first();

            $isAdminPhone = ($shortPhone === '9026350101');

            if (!$user) {
                $user = User::create([
                    'phone' => $cleanPhone,
                    'name' => 'Shop Owner',
                    'subscription_status' => $isAdminPhone ? 'active' : 'trial',
                    'trial_expires_at' => now()->addDays(7),
                    'is_admin' => $isAdminPhone,
                ]);
            } else if ($isAdminPhone && !$user->is_admin) {
                $user->update(['is_admin' => true, 'subscription_status' => 'active']);
            }

            $token = $user->createToken('flutter-owner-app')->plainTextToken;
            $business = $user->business;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'subscription_status' => $user->subscription_status ?? 'trial',
                    'trial_expires_at' => $user->trial_expires_at?->toIso8601String(),
                    'is_expired' => $user->is_expired ?? false,
                    'is_admin' => (bool) $user->is_admin,
                ],
                'business' => $business,
                'needs_business_setup' => ($business === null),
            ]);
        } catch (\Throwable $e) {
            Log::error('Firebase login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('business');
        return response()->json([
            'success' => true,
            'user' => $user,
            'business' => $user->business,
            'needs_business_setup' => ($user->business === null),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}

