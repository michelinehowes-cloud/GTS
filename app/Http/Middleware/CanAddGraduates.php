<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanAddGraduates
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ التحقق أولاً من وجود مستخدم مسجل
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ✅ ثم التحقق من الصلاحية
        if (!in_array(auth()->user()->role, ['admin', 'career_guidance_officer'])) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}