<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    /**
     * التحقق من استجابة Cloudflare Turnstile
     */
    public function verifyTurnstile(?string $token, ?string $ip = null): array
    {
        if (app()->environment('testing') || !config('security.turnstile.enabled', true)) {
            return ['success' => true];
        }

        // 1. التوكن إلزامي في جميع الحالات - لا يمكن تسجيل الدخول بدون إكمال كاشف الروبوتات
        if (empty($token) || trim($token) === '' || str_starts_with($token, 'BYPASS_')) {
            return [
                'success' => false,
                'message' => 'يرجى إكمال التحقق الأمني (كاشف الروبوتات Cloudflare) قبل المتابعة.',
            ];
        }

        $host = request()->getHost();
        $isDevelopmentOrTunnel = app()->environment('local') 
            || in_array($host, ['localhost', '127.0.0.1']) 
            || str_ends_with($host, '.trycloudflare.com')
            || str_ends_with($host, '.railway.app')
            || str_ends_with($host, '.up.railway.app');

        $secret = config('security.turnstile.secret_key');
        $isTestKey = empty($secret) || $secret === '1x0000000000000000000000000000000AA';

        // إذا كان المفتاح هو مفتاح الاختبار وتم إرسال توكن صالح
        if ($isTestKey) {
            return ['success' => true];
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['success'])) {
                    return ['success' => true];
                }

                // في بيئة التطوير المحلي، إذا كان الخطأ بسبب عدم إضافة localhost في لوحة كلاودفير
                // نسمح بالمرور فقط لأن المستخدم أكمل التحقق وحصل على التوكن
                $errorCodes = $data['error-codes'] ?? [];
                if ($isDevelopmentOrTunnel && (in_array('invalid-input-secret', $errorCodes) || in_array('bad-request', $errorCodes) || in_array('invalid-widget-id', $errorCodes))) {
                    Log::warning('Turnstile allowed for local development with provided token', ['host' => $host, 'data' => $data]);
                    return ['success' => true];
                }

                Log::warning('Turnstile verification failed', ['data' => $data, 'ip' => $ip]);
                return [
                    'success' => false,
                    'message' => 'فشل التحقق الأمني من كاشف الروبوتات. يرجى إعادة المحاولة.',
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile connection error: ' . $e->getMessage());
            // في حال انقطاع الاتصال بالسيرفر، إذا تم إرسال توكن نسمح بالمرور
            return ['success' => true];
        }

        if ($isDevelopmentOrTunnel) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'تعذر التحقق الأمني حالياً. يرجى إعادة المحاولة.',
        ];
    }

    /**
     * التحقق من مصيدة الروبوتات (Honeypot)
     */
    public function verifyHoneypot(Request $request): array
    {
        if (!config('security.honeypot.enabled', true)) {
            return ['success' => true];
        }

        $field = config('security.honeypot.field_name', '_hp_security_check');
        $timeField = config('security.honeypot.time_field', '_hp_time_token');

        // 1. فحص الحقل الخفي (يجب أن يكون فارغاً تماماً)
        if ($request->filled($field)) {
            Log::warning('Honeypot field filled by bot', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'value' => $request->input($field),
            ]);
            return [
                'success' => false,
                'message' => 'تم اكتشاف نشاط آلي غير مصرح به.',
            ];
        }

        // 2. فحص سرعة الإرسال (الروبوتات ترسل النماذج في أجزاء من الثانية)
        if ($request->filled($timeField)) {
            try {
                $timestamp = decrypt($request->input($timeField));
                $elapsed = microtime(true) - (float)$timestamp;
                $minSeconds = (float)config('security.honeypot.min_seconds', 1.0);

                if ($elapsed < $minSeconds) {
                    Log::warning('Honeypot form submitted too fast', [
                        'ip' => $request->ip(),
                        'elapsed' => $elapsed,
                    ]);
                    return [
                        'success' => false,
                        'message' => 'تم إرسال الطلب بسرعة غير طبيعية، يرجى المحاولة كإنسان.',
                    ];
                }
            } catch (\Throwable $e) {
                // تجاهل خطأ فك التشفير إذا تم العبث بالحقل
            }
        }

        return ['success' => true];
    }
}
