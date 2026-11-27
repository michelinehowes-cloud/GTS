<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SecurityService
{
    /**
     * التحقق التكيّفي (Adaptive challenge):
     * لا يُطلب من المستخدم العادي أي تحقق من Cloudflare، ويظهر الكاشف فقط عند الاشتباه:
     * بعد عدد من المحاولات الفاشلة أو الإرسالات المتكررة من نفس الجهاز/الحساب،
     * أو إذا وصل الطلب بدون رمز الوقت الخاص بمصيدة الروبوتات.
     */
    public function challengeRequired(string $action, Request $request, ?string $identifier = null): bool
    {
        if (!$this->turnstileActive()) {
            return false;
        }

        if ($request->hasSession() && $request->session()->get($this->challengeSessionKey($action))) {
            return true;
        }

        $threshold = (int) config("security.challenge.actions.{$action}.threshold", 3);
        foreach ($this->challengeKeys($action, $request, $identifier) as $key) {
            if (RateLimiter::attempts($key) >= $threshold) {
                return true;
            }
        }

        return $request->isMethod('post') && $this->timeTokenMissingOrInvalid($request);
    }

    /**
     * يتحقق من Cloudflare فقط إذا كان التحقق مطلوباً لهذا الطلب، وإلا يمرّ فوراً بدون أي اتصال خارجي.
     */
    public function verifyChallenge(string $action, Request $request, ?string $identifier = null): array
    {
        if (!$this->challengeRequired($action, $request, $identifier)) {
            return ['success' => true, 'challenged' => false];
        }

        // من الآن فصاعداً يظهر الكاشف في النموذج حتى ينجح الطلب
        if ($request->hasSession()) {
            $request->session()->put($this->challengeSessionKey($action), true);
        }

        $result = $this->verifyTurnstile($request->input('cf-turnstile-response'), $request->ip());
        if (!$result['success'] && empty($request->input('cf-turnstile-response'))) {
            $result['message'] = 'لأسباب أمنية، يرجى إكمال التحقق (Cloudflare) الظاهر في النموذج ثم المحاولة مرة أخرى.';
        }

        return $result + ['challenged' => true];
    }

    /**
     * تسجيل محاولة مشبوهة (دخول فاشل، أو إرسال نموذج تسجيل/استعادة) لحساب عتبة ظهور الكاشف.
     */
    public function recordChallengeHit(string $action, Request $request, ?string $identifier = null): void
    {
        $decay = (int) config("security.challenge.actions.{$action}.decay_minutes", 15) * 60;
        foreach ($this->challengeKeys($action, $request, $identifier) as $key) {
            RateLimiter::hit($key, $decay);
        }
    }

    /**
     * بعد نجاح العملية: لا حاجة لإظهار الكاشف لهذا الحساب/الجلسة بعد الآن.
     */
    public function clearChallenge(string $action, Request $request, ?string $identifier = null): void
    {
        if ($identifier !== null && trim($identifier) !== '') {
            RateLimiter::clear($this->identifierKey($action, $identifier));
        }
        if ($request->hasSession()) {
            $request->session()->forget($this->challengeSessionKey($action));
        }
    }

    protected function turnstileActive(): bool
    {
        if (app()->environment('testing') && !config('security.turnstile.force_testing', false)) {
            return false;
        }

        return config('security.turnstile.enabled', false)
            && !empty(config('security.turnstile.site_key'))
            && !empty(config('security.turnstile.secret_key'));
    }

    protected function challengeKeys(string $action, Request $request, ?string $identifier): array
    {
        $keys = ["security-challenge:{$action}:ip:" . sha1((string) $request->ip())];
        if ($identifier !== null && trim($identifier) !== '') {
            $keys[] = $this->identifierKey($action, $identifier);
        }

        return $keys;
    }

    protected function identifierKey(string $action, string $identifier): string
    {
        return "security-challenge:{$action}:id:" . sha1(mb_strtolower(trim($identifier)));
    }

    protected function challengeSessionKey(string $action): string
    {
        return "security_challenge.{$action}";
    }

    protected function timeTokenMissingOrInvalid(Request $request): bool
    {
        if (!config('security.honeypot.enabled', true)) {
            return false;
        }

        $token = $request->input(config('security.honeypot.time_field', '_hp_time_token'));
        if (empty($token)) {
            return true;
        }

        try {
            decrypt($token);
            return false;
        } catch (\Throwable $e) {
            return true;
        }
    }

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
                    Log::critical('Cloudflare Turnstile secret key is misconfigured or rejected by Cloudflare (invalid-input-secret). Bypassing check to prevent total system lockout.');
                    return [
                        'success' => true,
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
