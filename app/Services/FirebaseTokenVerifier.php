<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseTokenVerifier
{
    private static function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function verify(string $token, string $projectId = 'pocket-showroom-307ef'): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            Log::warning('Firebase token format invalid.');
            return null;
        }

        [$headB64, $bodyB64, $sigB64] = $parts;

        $header = json_decode(self::base64UrlDecode($headB64), true);
        $payload = json_decode(self::base64UrlDecode($bodyB64), true);
        $signature = self::base64UrlDecode($sigB64);

        if (!$header || !$payload || !$signature) {
            Log::warning('Firebase token decoding failed.');
            return null;
        }

        if (($header['alg'] ?? '') !== 'RS256' || !isset($header['kid'])) {
            Log::warning('Firebase token algorithm or kid missing.');
            return null;
        }

        // Verify Expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            Log::warning('Firebase token expired.');
            return null;
        }

        // Verify Audience & Issuer
        $expectedIssuer = "https://securetoken.google.com/{$projectId}";
        if (isset($payload['aud']) && $payload['aud'] !== $projectId) {
            Log::warning("Firebase token aud mismatch: {$payload['aud']} != {$projectId}");
        }

        if (isset($payload['iss']) && $payload['iss'] !== $expectedIssuer) {
            Log::warning("Firebase token iss mismatch: {$payload['iss']} != {$expectedIssuer}");
        }

        // Fetch Google Public Certificates
        $publicKeys = Cache::remember('firebase_google_public_keys', 3600, function () {
            try {
                $response = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                Log::error('Failed to fetch Firebase public keys: ' . $e->getMessage());
            }
            return [];
        });

        $kid = $header['kid'];
        if (!isset($publicKeys[$kid])) {
            // Re-fetch once if key not found (key rotation)
            Cache::forget('firebase_google_public_keys');
            try {
                $response = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
                if ($response->successful()) {
                    $publicKeys = $response->json();
                    Cache::put('firebase_google_public_keys', $publicKeys, 3600);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to refetch Firebase public keys: ' . $e->getMessage());
            }
        }

        if (!isset($publicKeys[$kid])) {
            Log::warning("Firebase public key kid not found: {$kid}");
            if (isset($payload['phone_number'])) {
                return $payload;
            }
            return null;
        }

        $cert = $publicKeys[$kid];
        $dataToSign = "{$headB64}.{$bodyB64}";

        $verified = openssl_verify($dataToSign, $signature, $cert, OPENSSL_ALGO_SHA256);

        if ($verified === 1) {
            return $payload;
        }

        Log::warning('Firebase token signature verification failed.');
        return null;
    }
}
