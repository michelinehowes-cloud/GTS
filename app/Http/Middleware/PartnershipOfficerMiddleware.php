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
        
        if (
            $user->isPartnershipOfficer() ||
            $user->isAdmin() ||
            $user->isCompany() ||
            $user->hasAnyPermission([
                'companies.view',
                'companies.create',
                'companies.edit',
                'jobs.view',
                'jobs.manage',
                'nominations.manage',
                'job_fair.view',
                'job_fair.manage',
                'partnerships.documents'
            ])
        ) {
            return $next($request);
        }

        abort(403, 'غير مصرح بالوصول. يتطلب هذا القسم صلاحيات مسؤول الشراكات أو المعرض.');
    }
}