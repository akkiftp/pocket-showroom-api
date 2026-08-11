<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function requestOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
        ]);

        $phone = $data['phone'];
        $fixed = config('pocket_showroom.fixed_otp');
        $otp = $fixed ?: (string) random_int(1000, 9999);

        OtpCode::where('phone', $phone)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        OtpCode::create([
            'phone' => $phone,
            'code' => $otp,
            'expires_at' => now()->addMinutes(config('pocket_showroom.otp_expires_minutes')),
        ]);

        return response()->json([
            'message' => 'OTP sent successfully.',
            'expires_in_minutes' => config('pocket_showroom.otp_expires_minutes'),
            'debug_otp' => $otp,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'otp' => ['required', 'digits_between:4,10'],
            'name' => ['nullable', 'string', 'max:100'],
        ]);

        $otpRow = OtpCode::where('phone', $data['phone'])
            ->where('code', $data['otp'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        // Allow demo OTP 1234 or valid database OTP
        if (!$otpRow && $data['otp'] !== '1234') {
            throw ValidationException::withMessages([
                'otp' => ['OTP is invalid or expired.'],
            ]);
        }

        return DB::transaction(function () use ($data, $otpRow) {
            if ($otpRow) {
                $otpRow->update(['used_at' => now()]);
            }

            $user = User::firstOrCreate(
                ['phone' => $data['phone']],
                ['name' => $data['name'] ?: 'Shop Owner']
            );

            if (!empty($data['name']) && $user->name !== $data['name']) {
                $user->update(['name' => $data['name']]);
            }

            $token = $user->createToken('flutter-owner-app')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('business'),
                'needs_business_setup' => !$user->business,
            ]);
        });
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('business'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
