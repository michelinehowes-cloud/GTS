<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EvaluationFollowupMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (
            $user->role === 'evaluation_followup' ||
            $user->isAdmin() ||
            $user->hasAnyPermission([
                'surveys.manage',
                'evaluations.manage',
                'reports.view'
            ])
        ) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذا القسم');
    }
}
