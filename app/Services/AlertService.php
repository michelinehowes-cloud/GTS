<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * E-mail alerts to the system administrator when something breaks (server errors, AI service down),
 * so problems are noticed before users report them.
 *
 * Throttled: the same problem is e-mailed at most once per `alerts.repeat_minutes`,
 * and no more than `alerts.max_per_hour` alerts are sent in total per hour.
 */
class AlertService
{
    public static function notify(string $key, string $subject, string $body): bool
    {
        if (!config('alerts.enabled', true) || app()->runningUnitTests() && !config('alerts.force_in_tests', false)) {
            return false;
        }

        $recipients = self::recipients();
        if (empty($recipients)) {
            return false;
        }

        $repeatKey = 'alerts:sent:' . sha1($key);
        $hourKey = 'alerts:hour:' . now()->format('YmdH');

        if (Cache::has($repeatKey)) {
            return false;
        }
        if ((int) Cache::get($hourKey, 0) >= (int) config('alerts.max_per_hour', 10)) {
            return false;
        }

        Cache::put($repeatKey, true, now()->addMinutes((int) config('alerts.repeat_minutes', 30)));
        Cache::put($hourKey, (int) Cache::get($hourKey, 0) + 1, now()->addHour());

        $appName = config('app.name', 'GTO');
        $fullBody = $body . "\n\n—\n" .
            'الموقع: ' . config('app.url') . "\n" .
            'الوقت: ' . now()->format('Y-m-d H:i:s') . "\n" .
            'لن يُرسل تنبيه آخر عن نفس المشكلة قبل ' . config('alerts.repeat_minutes', 30) . " دقيقة.\n";

        try {
            Mail::raw($fullBody, function ($message) use ($recipients, $subject, $appName) {
                $message->to($recipients)->subject("⚠️ [{$appName}] {$subject}");
            });
            return true;
        } catch (\Throwable $e) {
            // Never let an alert failure break the request that triggered it
            Log::error('Alert e-mail failed: ' . $e->getMessage());
            return false;
        }
    }

    /** Server error (HTTP 500) alert with enough context to start investigating. */
    public static function exception(\Throwable $e): void
    {
        $request = request();
        $where = $e->getFile() ? Str::after($e->getFile(), base_path() . DIRECTORY_SEPARATOR) . ':' . $e->getLine() : '-';

        $body = "حدث خطأ في الخادم (500).\n\n" .
            'الخطأ: ' . class_basename($e) . ' — ' . Str::limit($e->getMessage(), 400) . "\n" .
            "المكان: {$where}\n" .
            ($request ? 'الصفحة: ' . $request->method() . ' ' . $request->fullUrl() . "\n" : '') .
            ($request && $request->user() ? 'المستخدم: #' . $request->user()->id . ' (' . $request->user()->role . ")\n" : '') .
            "\nالتفاصيل الكاملة في storage/logs على الخادم.";

        self::notify('exception:' . get_class($e) . ':' . $where, 'خطأ في الخادم: ' . class_basename($e), $body);
    }

    /** @return string[] */
    protected static function recipients(): array
    {
        $configured = array_filter(array_map('trim', explode(',', (string) config('alerts.email'))));
        if ($configured) {
            return array_values($configured);
        }

        // Fallback: the main administrator account
        $admin = User::where('role', 'admin')->where('is_active', true)->orderBy('id')->first();

        return $admin && $admin->email ? [$admin->email] : [];
    }
}
