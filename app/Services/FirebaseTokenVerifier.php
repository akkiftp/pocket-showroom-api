<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseTokenVerifier
{
    private static function base64UrlDecode(string $input): string|false
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($input, '-_', '+/'), true);
    }

    public static function verify(string $token, string $projectId): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$headB64, $bodyB64, $sigB64] = $parts;
        $headJson = self::base64UrlDecode($headB64);
        $bodyJson = self::base64UrlDecode($bodyB64);
        $signature = self::base64UrlDecode($sigB64);

        if ($headJson === false || $bodyJson === false || $signature === false) return null;

        $header = json_decode($headJson, true);
        $payload = json_decode($bodyJson, true);
        if (!is_array($header) || !is_array($payload)) return null;

        if (($header['alg'] ?? null) !== 'RS256' || empty($header['kid'])) return null;
        if (($payload['aud'] ?? null) !== $projectId) return null;
        if (($payload['iss'] ?? null) !== "https://securetoken.google.com/{$projectId}") return null;

        $now = time();
        if (!isset($payload['exp']) || (int) $payload['exp'] <= $now) return null;
        if (isset($payload['iat']) && (int) $payload['iat'] > $now + 60) return null;
        if (empty($payload['sub']) || strlen((string) $payload['sub']) > 128) return null;

        $keys = self::publicKeys();
        $kid = (string) $header['kid'];
        if (!isset($keys[$kid])) {
            Cache::forget('firebase_google_public_keys');
            $keys = self::publicKeys();
        }
        if (!isset($keys[$kid])) return null;

        $verified = openssl_verify(
            "{$headB64}.{$bodyB64}",
            $signature,
            $keys[$kid],
            OPENSSL_ALGO_SHA256
        );

        return $verified === 1 ? $payload : null;
    }

    private static function publicKeys(): array
    {
        return Cache::remember('firebase_google_public_keys', 3600, function () {
            try {
                $response = Http::timeout(10)->get(
                    'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com'
                );
                return $response->successful() && is_array($response->json()) ? $response->json() : [];
            } catch (\Throwable $e) {
                Log::warning('Unable to fetch Firebase public keys', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }
}
