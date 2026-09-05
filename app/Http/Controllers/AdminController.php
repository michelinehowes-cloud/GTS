<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobOpportunity;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\PartnershipDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function dashboard()
    {
        // التحقق من وجود الجداول قبل استخدامها
        $usersCount = Schema::hasTable('users') ? User::count() : 0;
        $companiesCount = Schema::hasTable('companies') ? Company::count() : 0;
        $approvedTrainingsCount = Schema::hasTable('trainings') ? Training::where('status', 'active')->count() : 0;

        if (Schema::hasTable('training_applications')) {
            $applicationsCount = TrainingApplication::count();
            $pendingApplicationsCount = TrainingApplication::where('status', 'pending')->count();
        } else {
            $applicationsCount = 0;
            $pendingApplicationsCount = 0;
        }

        // Fetch additional counts
        $jobOpportunitiesCount = Schema::hasTable('job_opportunities') ? JobOpportunity::count() : 0;
        $activeJobOpportunitiesCount = Schema::hasTable('job_opportunities') ? JobOpportunity::where('status', 'active')->count() : 0;

        $graduatesCount = Schema::hasTable('graduates_data') ? GraduateData::count() : 0;
        $employedGraduatesCount = Schema::hasTable('graduates_data') ? GraduateData::where('employment_status', 'employed')->count() : 0;

        $nominationsCount = Schema::hasTable('nominations') ? Nomination::count() : 0;
        $pendingNominationsCount = Schema::hasTable('nominations') ? Nomination::where('status', 'pending')->count() : 0;

        $partnershipDocumentsCount = Schema::hasTable('partnership_documents') ? PartnershipDocument::count() : 0;
        $pendingPartnershipDocumentsCount = Schema::hasTable('partnership_documents') ? PartnershipDocument::where('document_status', 'draft')->count() : 0;

        $approvedCompaniesCount = Schema::hasTable('companies') ? Company::where('is_approved', true)->count() : 0;
        $pendingCompaniesCount = Schema::hasTable('companies') ? Company::where('is_approved', false)->count() : 0;

        // Fetch recent activities
        $recentUsers = Schema::hasTable('users') ? User::latest()->take(5)->get() : collect();
        $recentCompanies = Schema::hasTable('companies') ? Company::latest()->take(5)->get() : collect();
        $recentTrainingApplications = Schema::hasTable('training_applications') ? TrainingApplication::with('user', 'training')->latest()->take(5)->get() : collect();
        $recentJobOpportunities = Schema::hasTable('job_opportunities') ? JobOpportunity::latest()->take(5)->get() : collect();
        $recentNominations = Schema::hasTable('nominations') ? Nomination::with('graduate', 'jobOpportunity')->latest()->take(5)->get() : collect();

        // بيانات المخططات للوحة التحكم التفاعلية الحقيقية
        $chartData = $this->getDashboardChartData();

        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'companiesCount' => $companiesCount,
            'approvedTrainingsCount' => $approvedTrainingsCount,
            'applicationsCount' => $applicationsCount,
            'pendingApplicationsCount' => $pendingApplicationsCount,
            'jobOpportunitiesCount' => $jobOpportunitiesCount,
            'activeJobOpportunitiesCount' => $activeJobOpportunitiesCount,
            'graduatesCount' => $graduatesCount,
            'employedGraduatesCount' => $employedGraduatesCount,
            'nominationsCount' => $nominationsCount,
            'pendingNominationsCount' => $pendingNominationsCount,
            'partnershipDocumentsCount' => $partnershipDocumentsCount,
            'pendingPartnershipDocumentsCount' => $pendingPartnershipDocumentsCount,
            'approvedCompaniesCount' => $approvedCompaniesCount,
            'pendingCompaniesCount' => $pendingCompaniesCount,
            'recentUsers' => $recentUsers,
            'recentCompanies' => $recentCompanies,
            'recentTrainingApplications' => $recentTrainingApplications,
            'recentJobOpportunities' => $recentJobOpportunities,
            'recentNominations' => $recentNominations,
            'chartsData' => $chartData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * API endpoint for live dashboard stats
     */
    public function liveStats()
    {
        $usersCount = Schema::hasTable('users') ? User::count() : 0;
        $companiesCount = Schema::hasTable('companies') ? Company::count() : 0;
        $approvedTrainingsCount = Schema::hasTable('trainings') ? Training::where('status', 'active')->count() : 0;
        $applicationsCount = Schema::hasTable('training_applications') ? TrainingApplication::count() : 0;

        $chartData = $this->getDashboardChartData();

        return response()->json([
            'usersCount' => $usersCount,
            'companiesCount' => $companiesCount,
            'approvedTrainingsCount' => $approvedTrainingsCount,
            'applicationsCount' => $applicationsCount,
            'chartData' => $chartData
        ]);
    }

    /**
     * إرجاع بيانات المخططات الحقيقية من قاعدة البيانات
     */
    private function getDashboardChartData()
    {
        // 1. مخطط توزيع المستخدمين حسب الدور من قاعدة البيانات الفعلية
        $roleMap = [
            'admin' => 'مدير النظام',
            'graduate' => 'الخريجين',
            'company' => 'الشركات',
            'training_coordinator' => 'منسق التدريب',
            'career_guidance_officer' => 'مسؤول الإرشاد المهني',
            'partnership_officer' => 'مسؤول الشراكات والتوظيف',
            'evaluation_followup' => 'مسؤول التقييم والمتابعة',
            'media_officer' => 'المسؤول الإعلامي',
        ];

        $usersByRoleRaw = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $userRoleLabels = [];
        $userRoleData = [];
        foreach ($usersByRoleRaw as $role => $count) {
            $userRoleLabels[] = $roleMap[$role] ?? $role;
            $userRoleData[] = (int) $count;
        }

        // 2. مخطط حالة الشركات الفعلي
        $approvedCompanies = Company::where('is_approved', true)->count();
        $pendingCompanies = Company::where('is_approved', false)->count();

        $companyStatusLabels = ['معتمدة', 'قيد الانتظار'];
        $companyStatusData = [(int) $approvedCompanies, (int) $pendingCompanies];

        // 3. مخطط حالة التوظيف للخريجين الفعلي
        $employmentStatusLabelsMap = [
            'employed' => 'تم التوظيف',
            'seeking_opportunities' => 'يبحث عن فرصة عمل',
            'seeking' => 'باحث عن عمل',
            'searching' => 'يبحث عن عمل',
            'training' => 'تحت التدريب',
            'internship' => 'تدريب داخلي',
            'student' => 'طالب / دراسات عليا',
            'further_study' => 'مستكمل للدراسة',
            'unemployed' => 'غير موظف',
        ];

        $employmentRaw = GraduateData::selectRaw('employment_status, COUNT(*) as count')
            ->whereNotNull('employment_status')
            ->groupBy('employment_status')
            ->pluck('count', 'employment_status')
            ->toArray();

        $employmentLabels = [];
        $employmentData = [];
        foreach ($employmentRaw as $status => $count) {
            $employmentLabels[] = $employmentStatusLabelsMap[$status] ?? $status;
            $employmentData[] = (int) $count;
        }

        if (empty($employmentLabels)) {
            $employmentLabels = ['لا توجد بيانات مسجلة'];
            $employmentData = [0];
        }

        // 4. مخطط النشاط الشهري لطلبات التدريب (آخر 6 أشهر من قاعدة البيانات)
        $monthlyLabels = [];
        $monthlyData = [];
        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            $monthName = $arabicMonths[$month] . ' ' . $year;

            $count = TrainingApplication::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $monthlyLabels[] = $monthName;
            $monthlyData[] = (int) $count;
        }

        return [
            'usersByRole' => [
                'labels' => $userRoleLabels,
                'data' => $userRoleData,
            ],
            'companiesByStatus' => [
                'labels' => $companyStatusLabels,
                'data' => $companyStatusData,
            ],
            'employmentStatus' => [
                'labels' => $employmentLabels,
                'data' => $employmentData,
            ],
            'monthlyActivity' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
            ],
        ];
    }

    /**
     * الحصول على تسمية حالة التوظيف
     */
    private function getEmploymentStatusLabel($status)
    {
        $labels = [
            'employed' => 'موظف',
            'unemployed' => 'غير موظف',
            'seeking' => 'باحث عن عمل',
            'student' => 'طالب',
            'other' => 'أخرى',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    public function users()
    {
        if (!Schema::hasTable('users')) {
            $users = collect();
        } else {
            $users = User::with('company')->get();
        }
        return view('admin.users', compact('users'));
    }

    public function companies()
    {
        if (!Schema::hasTable('companies')) {
            $companies = collect();
        } else {
            $companies = Company::with('user')->get();
        }
        return view('admin.companies', compact('companies'));
    }
public function reports()
{
    try {
        // الإحصائيات الأساسية
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'total_trainings' => Training::count(),
            'active_users' => User::where('is_active', true)->count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // توزيع المستخدمين حسب الدور
        $usersByRole = [
            'admin' => User::where('role', 'admin')->count(),
            'training_coordinator' => User::where('role', 'training_coordinator')->count(),
            'placement_coordinator' => User::where('role', 'placement_coordinator')->count(),
            'graduate' => User::where('role', 'graduate')->count(),
            'company' => User::where('role', 'company')->count(),
        ];

        // أحدث المستخدمين
        $recentUsers = User::with('company')->latest()->take(5)->get();

        // أحدث الشركات
        $recentCompanies = Company::with('user')->latest()->take(5)->get();

        return view('admin.reports.index', compact(
            'stats', 
            'usersByRole', 
            'recentUsers', 
            'recentCompanies'
        ));

    } catch (\Exception $e) {
        // في حالة حدوث خطأ، إرجاع قيم افتراضية
        $stats = [
            'total_users' => 0,
            'total_companies' => 0,
            'total_trainings' => 0,
            'active_users' => 0,
            'recent_users' => 0,
        ];

        $usersByRole = [
            'admin' => 0,
            'training_coordinator' => 0,
            'placement_coordinator' => 0,
            'graduate' => 0,
            'company' => 0,
        ];

        $recentUsers = collect();
        $recentCompanies = collect();

        return view('admin.reports.index', compact(
            'stats', 
            'usersByRole', 
            'recentUsers', 
            'recentCompanies'
        ));
    
}

    return view('admin.reports.index', compact('stats'));
}
    public function approveCompany($id)
    {
        if (!Schema::hasTable('companies')) {
            return redirect()->back()->with('error', 'جدول الشركات غير متوفر حالياً');
        }
        
        $company = Company::findOrFail($id);
        $company->update(['is_approved' => true]);
        
        return redirect()->back()->with('success', 'تمت الموافقة على الشركة بنجاح');
    }

    /**
     * عرض طلبات التدريب
     */
    public function applications()
    {
        if (!Schema::hasTable('training_applications')) {
            $applications = collect();
        } else {
            $applications = TrainingApplication::with(['user', 'training.coordinator'])
                ->latest()
                ->get();
        }
            
        return view('admin.applications.index', compact('applications'));
    }

    /**
     * الموافقة على طلب التدريب
     */
    public function approveApplication(Request $request, $id)
    {
        if (!Schema::hasTable('training_applications')) {
            return redirect()->back()
                ->with('error', 'جدول طلبات التدريب غير موجود في قاعدة البيانات');
        }

        $application = TrainingApplication::with('training')->findOrFail($id);
        $application->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'تم الموافقة على طلب التدريب بنجاح');
    }

    /**
     * الموافقة على طلبات التدريب دفعة واحدة
     */
    public function bulkApproveApplications(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        TrainingApplication::whereIn('id', $ids)
            ->update(['status' => 'approved']);

        return redirect()->back()
            ->with('success', 'تم الموافقة على ' . count($ids) . ' طلب(ات) بنجاح');
    }

    /**
     * رفض طلبات التدريب دفعة واحدة
     */
    public function bulkRejectApplications(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        TrainingApplication::whereIn('id', $ids)
            ->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', 'تم رفض ' . count($ids) . ' طلب(ات) بنجاح');
    }

    /**
     * حذف طلبات التدريب دفعة واحدة
     */
    public function bulkDeleteApplications(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        TrainingApplication::whereIn('id', $ids)->delete();

        return redirect()->back()
            ->with('success', 'تم حذف ' . count($ids) . ' طلب(ات) بنجاح');
    }
    /**
     * إعادة الطلب إلى قيد المراجعة
     */
    public function pendingApplication(Request $request, $id)
    {
        $application = TrainingApplication::with('training')->findOrFail($id);
        $application->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'تم إعادة الطلب إلى قيد المراجعة');
    }

    /**
     * رفض طلب التدريب
     */
    public function rejectApplication(Request $request, $id)
    {
        if (!Schema::hasTable('training_applications')) {
            return redirect()->back()
                ->with('error', 'جدول طلبات التدريب غير متوفر حالياً');
        }

        $application = TrainingApplication::with('training')->findOrFail($id);
        $application->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'تم رفض طلب التدريب بنجاح');
    }

    /**
     * حذف أو إلغاء طلب التدريب
     */
    public function destroyApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
        $application->delete();

        return redirect()->back()->with('success', 'تم حذف وإلغاء طلب التدريب بنجاح');
    }
}
