<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 1. مدير النظام العام له كامل الصلاحيات
        if ($user->isAdmin()) {
            return $next($request);
        }

        // 2. السماح للموظفين بالوصول بحسب الصلاحيات الممنوحة
        if ($request->is('admin/users*') && $user->hasAnyPermission(['users.view', 'users.manage', 'users.create', 'users.edit'])) {
            return $next($request);
        }

        if ($request->is('admin/companies*') && $user->hasAnyPermission(['companies.view', 'companies.create', 'companies.edit'])) {
            return $next($request);
        }

        if ($request->is('admin/trainings*') && $user->hasAnyPermission(['trainings.view', 'trainings.create', 'trainings.edit', 'trainings.attendance', 'trainings.trainers'])) {
            return $next($request);
        }

        if ($request->is('admin/applications*') && $user->hasPermission('trainings.applications')) {
            return $next($request);
        }

        if ($request->is('admin/career-guidance*') && $user->hasAnyPermission(['graduates.view', 'graduates.create', 'graduates.edit', 'graduates.approve', 'graduates.import_export', 'nominations.manage'])) {
            return $next($request);
        }

        if ($request->is('admin/reports*') && $user->hasPermission('reports.view')) {
            return $next($request);
        }

        if ($request->is('admin/job-fair*') && ($user->canManageJobFair() || $user->hasAnyPermission([
            'job_fair.view', 'job_fair.create', 'job_fair.edit', 'job_fair.delete',
            'job_fair.manage', 'job_fair.events', 'job_fair.projects', 'job_fair.sponsors',
            'job_fair.registrations', 'job_fair.attendance', 'job_fair.live', 'job_fair.visitors',
            'partnerships.manage', 'companies.view'
        ]) || in_array($user->role, ['partnership_officer']))) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'ليس لديك صلاحية للوصول إلى هذا القسم في لوحة الإدارة');
    }
}