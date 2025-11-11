<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CareerGuidanceOfficerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Allow both career_guidance_officer and admin users
        if (!auth()->user()->isCareerGuidanceOfficer() && !auth()->user()->isAdmin()) {
            return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}