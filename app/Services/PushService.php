<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Push notifications to the Android app through Firebase Cloud Messaging (HTTP v1 API).
 *
 * Off until the Firebase service-account file exists (see config/push.php).
 * Sending runs after the HTTP response is sent, so notifying many users never slows a page down.
 */
class PushService
{
    public static function enabled(): bool
    {
        return (bool) config('push.enabled', true) && is_readable((string) config('push.credentials'));
    }

    /**
     * Queue a push to the given users' phones, sent after the current response.
     *
     * @param int[] $userIds
     */
    public static function toUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        if (empty($userIds) || !self::enabled()) {
            return;
        }

        dispatch(function () use ($userIds, $title, $body, $data) {
            self::sendNow($userIds, $title, $body, $data);
        })->afterResponse();
    }

    /** @return int number of phones the message was delivered to */
    public static function sendNow(array $userIds, string $title, string $body, array $data = []): int
    {
        try {
            $credentials = self::credentials();
            $accessToken = self::accessToken($credentials);
        } catch (\Throwable $e) {
            Log::error('Push: cannot authenticate with Firebase: ' . $e->getMessage());
            return 0;
        }

        $projectId = config('push.project_id') ?: ($credentials['project_id'] ?? null);
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // FCM data values must be strings
        $data = array_map(fn ($v) => is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE), $data);

        $delivered = 0;
        DeviceToken::whereIn('user_id', $userIds)
            ->select(['id', 'token'])
            ->chunkById((int) config('push.concurrency', 20), function ($tokens) use ($url, $accessToken, $title, $body, $data, &$delivered) {
                $responses = Http::pool(function (Pool $pool) use ($tokens, $url, $accessToken, $title, $body, $data) {
                    foreach ($tokens as $t) {
                        $pool->as((string) $t->id)->withToken($accessToken)->timeout(15)->post($url, [
                            'message' => [
                                'token' => $t->token,
                                'notification' => ['title' => $title, 'body' => mb_substr($body, 0, 240)],
                                'data' => (object) $data,
                                'android' => [
                                    'priority' => 'HIGH',
                                    'notification' => ['sound' => 'default', 'channel_id' => 'general'],
                                ],
                            ],
                        ]);
                    }
                });

                foreach ($tokens as $t) {
                    $response = $responses[(string) $t->id] ?? null;
                    if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                        $delivered++;
                        continue;
                    }
                    // The app was uninstalled or the token expired: forget this phone
                    $status = $response instanceof \Illuminate\Http\Client\Response ? $response->status() : null;
                    $error = $response instanceof \Illuminate\Http\Client\Response ? (string) $response->body() : '';
                    if ($status === 404 || str_contains($error, 'UNREGISTERED') || str_contains($error, 'INVALID_ARGUMENT')) {
                        DeviceToken::whereKey($t->id)->delete();
                    } elseif ($status) {
                        Log::warning("Push to device #{$t->id} failed ({$status}): " . mb_substr($error, 0, 300));
                    }
                }
            });

        return $delivered;
    }

    protected static function credentials(): array
    {
        $json = json_decode((string) @file_get_contents((string) config('push.credentials')), true);
        if (!is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
            throw new \RuntimeException('invalid Firebase service-account file');
        }

        return $json;
    }

    /** OAuth access token for FCM, from a JWT signed with the service account key (cached ~50 minutes). */
    protected static function accessToken(array $credentials): string
    {
        return Cache::remember('push:fcm-token:' . sha1($credentials['client_email']), now()->addMinutes(50), function () use ($credentials) {
            $now = time();
            $b64 = fn (string $s) => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
            $header = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $b64(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ]));

            $signature = '';
            if (!openssl_sign("{$header}.{$claims}", $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new \RuntimeException('could not sign the Firebase token request');
            }
            $jwt = "{$header}.{$claims}." . $b64($signature);

            $response = Http::asForm()->timeout(15)->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);
            if (!$response->successful() || !$response->json('access_token')) {
                throw new \RuntimeException('token request failed (' . $response->status() . ')');
            }

            return $response->json('access_token');
        });
    }
}
