<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // التحقق من تفعيل الحساب
        if (!$user->isVerified()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'حسابك غير مفعل. يرجى التحقق من بريدك الإلكتروني.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'غير مسموح لك بالوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}