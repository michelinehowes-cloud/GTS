<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainingCoordinatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'training_coordinator') {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
    }
}