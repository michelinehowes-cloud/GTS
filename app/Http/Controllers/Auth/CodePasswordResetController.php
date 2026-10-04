<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CodePasswordResetController extends Controller
{
    // تحويل طلب استعادة كلمة المرور إلى النافذة المنبثقة بالصفحة الرئيسية
    public function create()
    {
        return redirect('/?open_forgot=1');
    }

    // إرسال الرمز
    public function store(Request $request)
    {
        // 1. فحص مصيدة الروبوتات (Honeypot)
        $securityService = app(\App\Services\SecurityService::class);
        $honeypot = $securityService->verifyHoneypot($request);
        if (!$honeypot['success']) {
            return redirect('/?open_forgot=1')->withErrors(['email' => $honeypot['message']])->withInput();
        }

        // 2. التحقق من كاشف الروبوتات (Cloudflare Turnstile)
        $turnstile = $securityService->verifyTurnstile($request->input('cf-turnstile-response'), $request->ip());
        if (!$turnstile['success']) {
            return redirect('/?open_forgot=1')->withErrors(['cf-turnstile-response' => $turnstile['message'], 'email' => $turnstile['message']])->withInput();
        }

        // 3. تقييد معدل طلبات استعادة كلمة المرور
        $throttleKey = 'pwd-reset|' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return redirect('/?open_forgot=1')->withErrors([
                'email' => "تم تجاوز عدد محاولات طلب رمز التحقق. يرجى الانتظار {$seconds} ثانية.",
            ])->withInput();
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 180);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.exists' => 'البريد الإلكتروني المدخل غير مسجل في النظام.'
        ]);

        if ($validator->fails()) {
            return redirect('/?open_forgot=1')->withErrors($validator)->withInput();
        }

        // حذف الرموز القديمة لهذا الإيميل
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        // إنشاء رمز جديد (6 أرقام) باستخدام دالة تشفيرية آمنة
        $code = random_int(100000, 999999);

        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'created_at' => Carbon::now()
        ]);

        // إرسال الإيميل
        try {
            Mail::send('emails.reset-code', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('رمز استعادة كلمة المرور - جامعة طرابلس');
            });
        } catch (\Exception $e) {
            return redirect('/?open_forgot=1')->withErrors(['email' => 'فشل إرسال البريد الإلكتروني. يرجى المحاولة لاحقاً.'])->withInput();
        }

        // تحويل المستخدم للنافذة المنبثقة لإدخال الرمز مباشرة بالصفحة الرئيسية
        return redirect('/?open_verify=1&email=' . urlencode($request->email))->with('status_code_sent', 'تم إرسال رمز التحقق بنجاح إلى بريدك الإلكتروني.');
    }

    // تحويل صفحة التحقق إلى النافذة المنبثقة بالصفحة الرئيسية
    public function verify(Request $request)
    {
        return redirect('/?open_verify=1&email=' . urlencode($request->email));
    }

    // تغيير كلمة المرور عبر النافذة المنبثقة مع حماية ضد الهجمات التخمينية (Brute Force)
    public function update(Request $request)
    {
        $normalizedEmail = strtolower(trim($request->input('email', '')));
        // مفتاح مخصص للبريد الإلكتروني عالمياً (يحمي من التخمين الموزع عبر Proxy/VPN)
        $emailThrottleKey = 'verify-otp-email|' . $normalizedEmail;
        // مفتاح مخصص لعنوان الـ IP (يحمي من إغراق الحسابات المختلفة من نفس المصدر)
        $ipThrottleKey = 'verify-otp-ip|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($emailThrottleKey, 5) ||
            \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($ipThrottleKey, 20)) {
            $seconds = max(
                \Illuminate\Support\Facades\RateLimiter::availableIn($emailThrottleKey),
                \Illuminate\Support\Facades\RateLimiter::availableIn($ipThrottleKey)
            );
            // إتلاف الرمز فورياً عند الاشتباه بمحاولة هجوم تخميني لحماية الحساب
            DB::table('password_reset_codes')->where('email', $normalizedEmail)->delete();
            return redirect('/?open_forgot=1')->withErrors([
                'email' => "تم استنفاد عدد المحاولات المسموح بها لهذا الرمز لحماية أمان الحساب. تم إلغاء صلاحية الرمز، يرجى الانتظار {$seconds} ثانية ثم طلب رمز جديد.",
            ]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.exists' => 'البريد الإلكتروني غير مسجل في النظام.',
            'code.required' => 'يرجى إدخال رمز التحقق.',
            'code.numeric' => 'يجب أن يتكون رمز التحقق من أرقام فقط.',
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        if ($validator->fails()) {
            return redirect('/?open_verify=1&email=' . urlencode($request->email))->withErrors($validator)->withInput();
        }

        // التحقق الذري من الرمز وحمايته من هجمات السباق والتكرار (Race Condition / Replay Attack)
        return DB::transaction(function () use ($request, $normalizedEmail, $emailThrottleKey, $ipThrottleKey) {
            // جلب وقفل سجل الرمز لمنع هجمات التكرار المتزامنة
            $record = DB::table('password_reset_codes')
                ->where('email', $normalizedEmail)
                ->where('code', $request->code)
                ->lockForUpdate()
                ->first();

            if (!$record) {
                \Illuminate\Support\Facades\RateLimiter::hit($emailThrottleKey, 900); // 15 دقيقة
                \Illuminate\Support\Facades\RateLimiter::hit($ipThrottleKey, 900);
                $attempts = \Illuminate\Support\Facades\RateLimiter::attempts($emailThrottleKey);
                if ($attempts >= 5) {
                    DB::table('password_reset_codes')->where('email', $normalizedEmail)->delete();
                    $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($emailThrottleKey);
                    return redirect('/?open_forgot=1')->withErrors([
                        'code' => "تم استنفاد عدد المحاولات المسموح بها لهذا الرمز لحماية أمان الحساب. تم إلغاء صلاحية الرمز، يرجى الانتظار {$seconds} ثانية ثم طلب رمز جديد.",
                    ]);
                }
                $remaining = 5 - $attempts;
                $errMsg = 'رمز التحقق غير صحيح. يرجى التحقق من بريدك.';
                if ($remaining > 0) {
                    $errMsg .= " (المحاولات المتبقية: {$remaining})";
                }
                return redirect('/?open_verify=1&email=' . urlencode($request->email))->withErrors(['code' => $errMsg])->withInput();
            }

            // التحقق من صلاحية الرمز (15 دقيقة)
            if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
                DB::table('password_reset_codes')->where('email', $normalizedEmail)->delete();
                return redirect('/?open_forgot=1')->withErrors(['email' => 'انتهت صلاحية الرمز (15 دقيقة). يرجى طلب رمز جديد.'])->withInput();
            }

            // تغيير كلمة المرور للمستخدم المقفول لمنع Race Conditions
            $user = User::where('email', $normalizedEmail)->lockForUpdate()->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // إتلاف الرمز فوراً ومسح العدادات في نفس المعاملة (منع إعادة الاستخدام نهائياً)
            DB::table('password_reset_codes')->where('email', $normalizedEmail)->delete();
            \Illuminate\Support\Facades\RateLimiter::clear($emailThrottleKey);
            \Illuminate\Support\Facades\RateLimiter::clear($ipThrottleKey);

            return redirect('/?open_login=1')->with('status', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.');
        });
    }
}
