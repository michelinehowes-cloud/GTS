<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * لوحة التقارير الرئيسية
     */
    public function index()
    {
        // إحصائيات سريعة
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'active_users' => User::count(), // نستخدم count كبديل مؤقت
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // إحصائيات المستخدمين حسب الدور
        $usersByRole = [
            'admin' => User::where('role', 'admin')->count(),
            'training_coordinator' => User::where('role', 'training_coordinator')->count(),
            'placement_coordinator' => User::where('role', 'placement_coordinator')->count(),
            'graduate' => User::where('role', 'graduate')->count(),
        ];

        // أحدث المستخدمين
        $recentUsers = User::latest()->take(5)->get();

        // أحدث الشركات
        $recentCompanies = Company::latest()->take(5)->get();

        return view('admin.reports.index', compact('stats', 'usersByRole', 'recentUsers', 'recentCompanies'));
    }

    /**
     * تقرير المستخدمين التفصيلي
     */
    public function usersReport()
    {
        $users = User::latest()->get();
        
        $reportStats = [
            'total' => User::count(),
            'active' => User::count(), // نستخدم count كبديل مؤقت
            'admins' => User::where('role', 'admin')->count(),
            'coordinators' => User::whereIn('role', ['training_coordinator', 'placement_coordinator'])->count(),
            'graduates' => User::where('role', 'graduate')->count(),
        ];

        return view('admin.reports.users', compact('users', 'reportStats'));
    }

    /**
     * تقرير الشركات التفصيلي
     */
    public function companiesReport()
    {
        $companies = Company::latest()->get();
        
        $reportStats = [
            'total' => Company::count(),
            'recent' => Company::where('created_at', '>=', now()->subDays(30))->count(),
            'by_industry' => Company::select('industry')->selectRaw('COUNT(*) as count')->groupBy('industry')->get(),
        ];

        return view('admin.reports.companies', compact('companies', 'reportStats'));
    }
}