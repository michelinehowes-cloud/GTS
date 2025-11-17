<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\AuditLog; // Assuming you have an AuditLog model

class AdminReportController extends Controller
{
    public function index()
    {
        // This will be the main reports and statistics dashboard
        // You can fetch summary data here for display
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $totalTrainings = Training::count();
        $recentAuditLogs = AuditLog::latest()->take(10)->get(); // Fetch recent audit logs

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

    public function auditLogs()
    {
        $auditLogs = AuditLog::latest()->paginate(20); // Paginate audit logs
        // Logic to display all system activities
        return view('admin.reports.audit-logs', compact('auditLogs'));
    }
}
