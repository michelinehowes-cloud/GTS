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

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // محاولة تسجيل الدخول
        $credentials = $request->only('email', 'password');

        // التحقق من صحة البيانات أولاً
        if (Auth::validate($credentials)) {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            // التحقق من حالة الحساب
            if (!$user->is_active) {
                return back()->withErrors([
                    'email' => 'تم تجميد هذا الحساب. يرجى التواصل مع الإدارة.',
                ]);
            }

            // التحقق من اعتماد حساب الخريج
            if ($user->role === 'graduate' && !$user->is_approved) {
                return back()->withErrors([
                    'email' => 'حسابك في انتظار الموافقة والاعتماد من قبل الإدارة.',
                ]);
            }

            // التحقق من اعتماد وتفعيل حساب الشركة (في حالة الاعتماد النشط فقط)
            if ($user->role === 'company') {
                $company = $user->company ?? \App\Models\Company::where('email', $user->email)->first();
                $isApproved = $company && $company->is_approved && $company->partnership_status === 'active';

                if (!$isApproved) {
                    return back()->withErrors([
                        'email' => 'عذراً، حساب شركتكم قيد المراجعة أو غير مفعل. يُسمح بالدخول في حالة الاعتماد النشط فقط.',
                    ]);
                }
            }

            // إتمام تسجيل الدخول
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // إذا فشل تسجيل الدخول
        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
