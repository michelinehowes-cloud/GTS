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

        $user = auth()->user();

        if (
            $user->isCareerGuidanceOfficer() ||
            $user->isAdmin() ||
            $user->hasAnyPermission([
                'graduates.view',
                'graduates.create',
                'graduates.edit',
                'graduates.approve',
                'graduates.import_export',
                'nominations.manage'
            ])
        ) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذا القسم');
    }
}