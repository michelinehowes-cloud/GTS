<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // إذا كان مسجل دخول بالفعل، ولا يحاول الوصول إلى صفحة تسجيل الدخول أو التسجيل،
                // فدعه يمر. وإلا، قم بتحويله إلى لوحة التحكم.
                if ($request->is(ltrim(RouteServiceProvider::HOME, '/'))) {
                    return $next($request);
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
