<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GraduateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'graduate') {
            return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        if (!auth()->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'حسابك في انتظار الموافقة من قبل الإدارة.');
        }

        return $next($request);
    }
}