<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\AuditLog;

class AdminReportController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $totalTrainings = Training::count();
        $recentAuditLogs = AuditLog::with('user')->orderByDesc('timestamp')->take(10)->get();

        return view('admin.reports.index', compact('totalUsers', 'totalCompanies', 'totalTrainings', 'recentAuditLogs'));
    }

    public function usersReport()
    {
        $users = User::all();
        // Logic to generate user-specific reports, charts, etc.
        return view('admin.reports.users', compact('users'));
    }

    public function companiesReport()
    {
        $companies = Company::all();
        // Logic to generate company-specific reports, charts, etc.
        return view('admin.reports.companies', compact('companies'));
    }

    public function trainingsReport()
    {
        $trainings = Training::all();
        // Logic to generate training-specific reports, charts, etc.
        return view('admin.reports.trainings', compact('trainings'));
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('entity', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $auditLogs */
        $auditLogs = $query->orderByDesc('timestamp')->paginate(20);
        $auditLogs->withQueryString();

        return view('admin.reports.audit-logs', compact('auditLogs'));
    }
}
