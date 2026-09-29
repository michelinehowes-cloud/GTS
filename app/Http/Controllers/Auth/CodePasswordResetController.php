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

        // إنشاء رمز جديد (6 أرقام)
        $code = rand(100000, 999999);

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

    // تغيير كلمة المرور عبر النافذة المنبثقة
    public function update(Request $request)
    {
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

        // التحقق من الرمز
        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return redirect('/?open_verify=1&email=' . urlencode($request->email))->withErrors(['code' => 'رمز التحقق غير صحيح. يرجى التحقق من بريدك.'])->withInput();
        }

        // التحقق من صلاحية الرمز (15 دقيقة)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return redirect('/?open_verify=1&email=' . urlencode($request->email))->withErrors(['code' => 'انتهت صلاحية الرمز. يرجى طلب رمز جديد.'])->withInput();
        }

        // تغيير كلمة المرور
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // حذف الرمز المستخدم
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return redirect('/?open_login=1')->with('status', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.');
    }
}
