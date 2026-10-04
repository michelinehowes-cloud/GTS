<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        // إذا كان المستخدم مسجل دخول بالفعل، احوله للdashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        // فتح نافذة تسجيل الدخول مباشرة في الصفحة الرئيسية
        return redirect('/?open_login=1');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // 1. فحص مصيدة الروبوتات (Honeypot)
        $securityService = app(\App\Services\SecurityService::class);
        $honeypot = $securityService->verifyHoneypot($request);
        if (!$honeypot['success']) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => $honeypot['message'],
            ]);
        }

        // 2. التحقق من كاشف الروبوتات (Cloudflare Turnstile)
        $turnstile = $securityService->verifyTurnstile($request->input('cf-turnstile-response'), $request->ip());
        if (!$turnstile['success']) {
            return back()->withInput($request->only('email'))->withErrors([
                'cf-turnstile-response' => $turnstile['message'],
                'email' => $turnstile['message'],
            ]);
        }

        // 3. التحقق من حد محاولات تسجيل الدخول (Rate Limiting)
        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($request->input('email')) . '|' . $request->ip());
        $maxAttempts = config('security.rate_limits.login_max_attempts', 5);
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            \App\Models\AuditLog::logAction(
                'security_rate_limit_blocked',
                'تم حظر محاولة تخمين بسبب تجاوز الحد الأقصى للمعرف: (' . $request->input('email') . ')',
                'Security',
                null,
                null,
                ['identifier' => $request->input('email'), 'available_in_seconds' => $seconds]
            );
            return back()->withInput($request->only('email'))->withErrors([
                'email' => "تم تجاوز عدد محاولات الدخول المسموحة. يرجى الانتظار {$seconds} ثانية قبل إعادة المحاولة.",
            ]);
        }

        // التحقق من صحة المدخلات (يقبل البريد الإلكتروني أو اسم المستخدم أو الرقم الوطني)
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني أو اسم المستخدم',
            'password.required' => 'يرجى إدخال كلمة المرور',
        ]);

        $loginInput = trim($request->input('email'));
        $password = (string) $request->input('password');

        // البحث الذكي عن المستخدم:
        // 1. بالبريد الإلكتروني المباشر
        // 2. باسم المستخدم / الاسم الكامل
        // 3. بالرقم الوطني
        $user = \App\Models\User::where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->orWhere('national_id', $loginInput)
            ->first();

        // 4. مطابقة الحسابات بالأسماء الشائعة أو المختصرة
        if (!$user) {
            $lower = strtolower($loginInput);
            if ($lower === 'admin' || $lower === 'مدير النظام' || $lower === 'administrator') {
                $user = \App\Models\User::where('email', 'admin@admin.com')
                    ->orWhere('email', 'admin@tripoliuniversity.edu.ly')
                    ->first();
            } elseif ($lower === 'training' || $lower === 'training_coordinator' || $loginInput === 'training@tripoliuniversity.edu.ly' || $lower === 'منسق التدريب') {
                $user = \App\Models\User::where('role', 'training_coordinator')->first();
            } elseif ($lower === 'moneeb' || $lower === 'munib' || $lower === 'المنيب' || $lower === 'moneeb20mohamed') {
                $user = \App\Models\User::where('email', 'like', 'moneeb20mohamed%')->first();
            } elseif ($lower === 'media' || $lower === 'مسؤول الإعلام') {
                $user = \App\Models\User::where('role', 'media_officer')->first();
            } elseif ($lower === 'guidance' || $lower === 'مسؤول الإرشاد') {
                $user = \App\Models\User::where('role', 'career_guidance_officer')->first();
            } elseif ($lower === 'partnership' || $lower === 'مسؤول الشراكات') {
                $user = \App\Models\User::where('role', 'partnership_officer')->first();
            }
        }

        // التحقق من صحة كلمة المرور
        $isPasswordValid = false;
        if ($user) {
            if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                $isPasswordValid = true;
            } elseif (app()->environment('local') && in_array($password, ['password123', 'password', 'admin123', '12345678'])) {
                // تسهيل تسجيل الدخول لبيئة التطوير المحلية
                $isPasswordValid = true;
            }
        }

        if ($user && $isPasswordValid) {
            // التحقق من حالة الحساب
            if (!$user->is_active) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'تم تجميد هذا الحساب. يرجى التواصل مع الإدارة.',
                ]);
            }

            // التحقق من اعتماد حساب الخريج
            if ($user->role === 'graduate' && !$user->is_approved) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'حسابك في انتظار الموافقة والاعتماد من قبل الإدارة.',
                ]);
            }

            // التحقق من اعتماد وتفعيل حساب الشركة (في حالة الاعتماد النشط فقط)
            if ($user->role === 'company') {
                $company = $user->company ?? \App\Models\Company::where('email', $user->email)->first();
                $isApproved = $company && $company->is_approved && $company->partnership_status === 'active';

                if (!$isApproved) {
                    \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                    return back()->withInput($request->only('email'))->withErrors([
                        'email' => 'عذراً، حساب شركتكم قيد المراجعة أو غير مفعل. يُسمح بالدخول في حالة الاعتماد النشط فقط.',
                    ]);
                }
            }

            // إتمام تسجيل الدخول بنجاح وتصفير عداد المحاولات
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // تسجيل محاولة فاشلة في عداد الحماية
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        // إذا فشل تسجيل الدخول
        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'بيانات الدخول غير صحيحة. يرجى التأكد من البريد الإلكتروني أو اسم المستخدم وكلمة المرور.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        if (Auth::guard('web')->check() || Auth::check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'تم تسجيل الخروج بنجاح.');
    }
}
