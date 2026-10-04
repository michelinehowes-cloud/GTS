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
        // 1. التجاوز في بيئة الاختبارات الآلية (إلا إذا تم طلب اختبارها عمداً) أو إذا لم يتم ضبط المفاتيح
        if ((app()->environment('testing') && !config('security.turnstile.force_testing', false))
            || !config('security.turnstile.enabled', false)
            || empty(config('security.turnstile.site_key'))
            || empty(config('security.turnstile.secret_key'))) {
            return ['success' => true];
        }

        // 2. إذا لم يتم إرسال التوكن إطلاقاً أو كان فارغاً أو رمز تجاوز
        if (empty($token) || trim($token) === '' || str_starts_with($token, 'BYPASS_')) {
            return [
                'success' => false,
                'message' => 'يرجى إكمال التحقق الأمني (كاشف الروبوتات Cloudflare) قبل المتابعة.',
            ];
        }

        $secret = config('security.turnstile.secret_key');
        $isTestKey = ($secret === '1x0000000000000000000000000000000AA');

        // إذا كان المفتاح هو مفتاح الاختبار المعتمد من كلاودفير
        if ($isTestKey) {
            return ['success' => true];
        }

        try {
            $response = Http::asForm()->timeout(6)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['success'])) {
                    return ['success' => true];
                }

                $errorCodes = $data['error-codes'] ?? [];
                Log::warning('Turnstile verification failed by Cloudflare API', [
                    'ip' => $ip,
                    'error_codes' => $errorCodes,
                    'data' => $data,
                ]);

                if (in_array('timeout-or-duplicate', $errorCodes)) {
                    return [
                        'success' => false,
                        'message' => 'انتهت صلاحية رمز التحقق الأمني، يرجى النقر على الكاشف مجدداً.',
                    ];
                }

                if (in_array('invalid-input-secret', $errorCodes)) {
                    return [
                        'success' => false,
                        'message' => 'مفتاح التحقق السري غير صحيح (Invalid Secret Key) في إعدادات المنظومة.',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'فشل التحقق الأمني من كاشف الروبوتات. يرجى إعادة المحاولة.',
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Turnstile connection error to Cloudflare: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'تعذر الاتصال بخادم التحقق الأمني (Cloudflare). يرجى التحقق من اتصال الإنترنت وإعادة المحاولة.',
            ];
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
