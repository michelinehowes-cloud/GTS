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

        return $next($request);
    }
}