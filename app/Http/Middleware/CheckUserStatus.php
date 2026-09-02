<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'تم تجميد حسابك. يرجى التواصل مع الإدارة.',
                ]);
            }

            // التحقق من حساب الشركة (الاعتماد النشط فقط)
            if ($user->role === 'company') {
                $company = $user->company ?? \App\Models\Company::where('email', $user->email)->first();
                $isApproved = $company && $company->is_approved && $company->partnership_status === 'active';

                if (!$isApproved) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'email' => 'عذراً، حساب شركتكم قيد المراجعة أو غير نشط. يُسمح بالدخول في حالة الاعتماد النشط فقط.',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
