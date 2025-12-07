<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordChanged
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
        // التحقق من أن المستخدم مسجل دخول
        if (auth()->check()) {
            $user = auth()->user();

            // التحقق من أن المستخدم يجب عليه تغيير كلمة المرور
            if ($user->must_change_password) {
                // السماح بالوصول لصفحات تغيير كلمة المرور وتسجيل الخروج فقط
                $allowedRoutes = [
                    'password.change.force',
                    'password.update.force',
                    'logout'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('password.change.force')
                        ->with('warning', 'يجب عليك تغيير كلمة المرور قبل المتابعة');
                }
            }
        }

        return $next($request);
    }
}
