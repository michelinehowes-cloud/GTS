<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\JobFair;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;

class StaffDashboardController extends Controller
{
    /**
     * لوحة التحكم الديناميكية للموظف مخصص الصلاحيات (Custom Staff Dashboard)
     * تعرض بطاقات الأداء والوصول السريع فقط للوحدات الممنوحة للموظف
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isStaff() && !$user->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة تحكم الموظف.');
        }

        $stats = [];
        $modules = [];

        // 1. برامج التدريب والتأهيل
        if ($user->isAdmin() || $user->hasAnyPermission([
            'trainings.view', 'trainings.create', 'trainings.edit', 
            'trainings.applications', 'trainings.attendance', 'trainings.trainers'
        ])) {
            $modules['trainings'] = true;
            $stats['trainings_total'] = Training::count();
            $stats['trainings_active'] = Training::where('status', 'active')->count();
            $stats['training_applications_pending'] = TrainingApplication::where('status', 'pending')->count();
            $stats['recent_trainings'] = Training::latest()->take(5)->get();
        }

        // 2. فرص التوظيف والعمل
        if ($user->isAdmin() || $user->hasAnyPermission(['jobs.view', 'jobs.manage', 'nominations.manage'])) {
            $modules['jobs'] = true;
            $stats['jobs_total'] = JobOpportunity::count();
            $stats['jobs_open'] = JobOpportunity::where('status', 'open')->count();
            $stats['recent_jobs'] = JobOpportunity::with('company')->latest()->take(5)->get();
        }

        // 3. الشركات والمؤسسات الشريكة
        if ($user->isAdmin() || $user->hasAnyPermission([
            'companies.view', 'companies.create', 'companies.edit', 
            'partnerships.documents', 'partnerships.manage'
        ])) {
            $modules['companies'] = true;
            $stats['companies_total'] = Company::count();
            $stats['companies_active'] = Company::where('partnership_status', 'active')->count();
            $stats['recent_companies'] = Company::latest()->take(5)->get();
        }

        // 4. معرض التوظيف والفعاليات
        if ($user->isAdmin() || $user->canViewJobFair()) {
            $modules['job_fair'] = true;
            $currentFair = JobFair::whereIn('status', ['published', 'ongoing'])->latest('event_date')->first() 
                ?? JobFair::latest()->first();
            $stats['job_fair'] = $currentFair;
            $stats['job_fair_visitors'] = $currentFair ? $currentFair->registrations()->count() : 0;
            $stats['job_fair_companies'] = $currentFair ? $currentFair->companies()->count() : 0;
        }

        // 5. شؤون وتأهيل الخريجين
        if ($user->isAdmin() || $user->hasAnyPermission([
            'graduates.view', 'graduates.create', 'graduates.edit', 
            'graduates.approve', 'graduates.import_export'
        ])) {
            $modules['graduates'] = true;
            $stats['graduates_total'] = User::where('role', 'graduate')->count();
            $stats['graduates_active'] = User::where('role', 'graduate')->where('is_active', true)->count();
        }

        // 6. الاستبيانات والتقييمات
        if ($user->isAdmin() || $user->hasAnyPermission(['surveys.manage', 'evaluations.manage', 'reports.view'])) {
            $modules['evaluations'] = true;
            $stats['surveys_total'] = Survey::count();
            $stats['surveys_responses'] = SurveyResponse::count();
        }

        // 7. إدارة المستخدمين
        if ($user->isAdmin() || $user->hasAnyPermission(['users.view', 'users.manage', 'users.create', 'users.edit'])) {
            $modules['users'] = true;
            $stats['users_total'] = User::count();
        }

        return view('staff.dashboard', compact('stats', 'modules'));
    }
}
