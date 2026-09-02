<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'company') {
            return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        $user = auth()->user();
        $company = $user->company ?? \App\Models\Company::where('email', $user->email)->first();
        $isApproved = $company && $company->is_approved && $company->partnership_status === 'active';

        if (!$isApproved) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'عذراً، حساب شركتكم غير معتمد أو غير نشط. يُسمح بالدخول في حالة الاعتماد النشط فقط.',
            ]);
        }

        return $next($request);
    }
}
