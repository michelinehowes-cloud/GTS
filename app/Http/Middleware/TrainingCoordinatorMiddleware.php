<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainingCoordinatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (
            $user->role === 'training_coordinator' ||
            $user->isAdmin() ||
            $user->hasAnyPermission([
                'trainings.view',
                'trainings.create',
                'trainings.edit',
                'trainings.applications',
                'trainings.attendance',
                'trainings.trainers'
            ])
        ) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى قسم التدريب');
    }
}