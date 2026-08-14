<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseTokenVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Exchange a verified Firebase Email/Password or Google identity for
     * a first-party Laravel Sanctum API token.
     */
    public function firebaseLogin(Request $request)
    {
        $data = $request->validate([
            'firebase_token' => ['required', 'string', 'min:100'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $payload = FirebaseTokenVerifier::verify(
            $data['firebase_token'],
            (string) config('pocket_showroom.firebase_project_id')
        );

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Your sign-in session is invalid or expired. Please sign in again.',
            ], 401);
        }

        $provider = (string) data_get($payload, 'firebase.sign_in_provider', '');
        if (!in_array($provider, ['password', 'google.com'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only Email/Password and Google Sign-In are enabled for Pocket Showroom.',
            ], 403);
        }

        $firebaseUid = trim((string) ($payload['sub'] ?? ''));
        $email = Str::lower(trim((string) ($payload['email'] ?? '')));
        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOL);

        if ($firebaseUid === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'A valid email address was not available from the sign-in provider.',
            ], 422);
        }

        try {
            $user = User::query()->where('firebase_uid', $firebaseUid)->first();

            // Seamlessly link an older Pocket Showroom account by its email address.
            if (!$user && $email !== '') {
                // Fetch all users with this email to handle any duplicates created accidentally
                $users = User::with('business')->whereRaw('LOWER(email) = ?', [$email])->get();
                if ($users->isNotEmpty()) {
                    // Pick the user account that actually has a business, prioritizing the one with the most products
                    $user = $users->sortByDesc(function ($u) {
                        return $u->business ? $u->business->products()->count() : -1;
                    })->first();
                }
            }

            $nameFromToken = trim((string) ($payload['name'] ?? ''));
            $requestedName = trim((string) ($data['name'] ?? ''));
            $displayName = $requestedName !== ''
                ? $requestedName
                : ($nameFromToken !== '' ? $nameFromToken : Str::before($email, '@'));

            $avatar = trim((string) ($payload['picture'] ?? ''));
            $freeMode = (bool) config('pocket_showroom.free_mode', true);
            $masterAdminEmail = Str::lower(trim((string) config('pocket_showroom.master_admin_email', '')));
            $isConfiguredAdmin = $masterAdminEmail !== '' && hash_equals($masterAdminEmail, $email);

            if (!$user) {
                $user = User::create([
                    'firebase_uid' => $firebaseUid,
                    'auth_provider' => $provider,
                    'name' => $displayName ?: 'Shop Owner',
                    'email' => $email,
                    'email_verified_at' => $emailVerified ? now() : null,
                    'avatar_url' => $avatar !== '' ? $avatar : null,
                    'subscription_status' => $freeMode || $isConfiguredAdmin ? 'active' : 'trial',
                    'trial_expires_at' => $freeMode || $isConfiguredAdmin ? null : now()->addDays(7),
                    'subscription_expires_at' => null,
                    'is_admin' => $isConfiguredAdmin,
                ]);
            } else {
                $updates = [
                    'firebase_uid' => $firebaseUid,
                    'auth_provider' => $provider,
                    'email' => $email,
                ];

                if (($user->name === null || trim($user->name) === '' || $user->name === 'Shop Owner') && $displayName !== '') {
                    $updates['name'] = $displayName;
                }
                if ($avatar !== '') {
                    $updates['avatar_url'] = $avatar;
                }
                if ($emailVerified && !$user->email_verified_at) {
                    $updates['email_verified_at'] = now();
                }
                if ($isConfiguredAdmin && !$user->is_admin) {
                    $updates['is_admin'] = true;
                }
                if ($freeMode && $user->subscription_status !== 'blocked') {
                    $updates['subscription_status'] = 'active';
                    $updates['trial_expires_at'] = null;
                    $updates['subscription_expires_at'] = null;
                }

                $user->update($updates);
            }

            // Keep only the latest owner-app session token for this account.
            $user->tokens()->where('name', 'pocket-showroom-app')->delete();
            $token = $user->createToken('pocket-showroom-app')->plainTextToken;
            $user = $user->fresh()->load('business');

            return response()->json([
                'success' => true,
                'message' => 'Signed in successfully.',
                'token' => $token,
                'token_type' => 'Bearer',
                'free_mode' => $freeMode,
                'email_verified' => (bool) $user->email_verified_at,
                'user' => $user,
                'business' => $user->business,
                'needs_business_setup' => $user->business === null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Firebase email/google login failed', [
                'email' => $email,
                'provider' => $provider,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to complete sign in right now. Please try again.',
            ], 500);
        }
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('business');

        return response()->json([
            'success' => true,
            'free_mode' => (bool) config('pocket_showroom.free_mode', true),
            'email_verified' => (bool) $user->email_verified_at,
            'user' => $user,
            'business' => $user->business,
            'needs_business_setup' => $user->business === null,
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
