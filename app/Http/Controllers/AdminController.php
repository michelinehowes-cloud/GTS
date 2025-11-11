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
        $trainingsCount = Schema::hasTable('trainings') ? Training::count() : 0;
        
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


        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'companiesCount' => $companiesCount,
            'trainingsCount' => $trainingsCount,
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
        ]);
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
    public function approveApplication($id)
    {
        if (!Schema::hasTable('training_applications')) {
            return redirect()->route('admin.applications.index')
                ->with('error', 'جدول طلبات التدريب غير متوفر حالياً');
        }
        
        $application = TrainingApplication::findOrFail($id);
        $application->update(['status' => 'approved']);
        
        return redirect()->route('admin.applications.index')
            ->with('success', 'تم الموافقة على طلب التدريب بنجاح');
    }
    /**
 * إعادة الطلب إلى قيد المراجعة
 */
public function pendingApplication($id)
{
    $application = TrainingApplication::findOrFail($id);
    $application->update(['status' => 'pending']);
    
    return redirect()->back()->with('success', 'تم إعادة الطلب إلى قيد المراجعة');
}

    /**
     * رفض طلب التدريب
     */
    public function rejectApplication($id)
    {
        if (!Schema::hasTable('training_applications')) {
            return redirect()->route('admin.applications.index')
                ->with('error', 'جدول طلبات التدريب غير متوفر حالياً');
        }
        
        $application = TrainingApplication::findOrFail($id);
        $application->update(['status' => 'rejected']);
        
        return redirect()->route('admin.applications.index')
            ->with('success', 'تم رفض طلب التدريب بنجاح');
    }
}
