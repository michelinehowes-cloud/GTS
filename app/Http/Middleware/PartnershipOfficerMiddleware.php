<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnershipOfficerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (!$user->isPartnershipOfficer() && !$user->isAdmin()) {
            abort(403, 'غير مصرح بالوصول. يجب أن تكون مسؤول الشراكات والتوظيف.');
        }

        return $next($request);
    }
}