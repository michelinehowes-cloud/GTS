<?php
// إظهار جميع الأخطاء للتشخيص
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use App\Http\Controllers\GraduateController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\CareerGuidanceController;
use App\Http\Controllers\JobOpportunityController;
use App\Http\Controllers\EvaluationFollowupController; // Add this line
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Graduate Training System - Web Routes
|--------------------------------------------------------------------------
| نظام تدريب الخريجين - جامعة طرابلس
| جميع مسارات النظام مع التعليقات التوضيحية
*/

// ==================== 🏠 الصفحة الرئيسية ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// أضف هذا السطر لحل المشكلة
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

// ==================== 🔐 نظام المصادقة ====================
Route::middleware('guest')->group(function () {
    // صفحة تسجيل الدخول
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ==================== 👥 المسارات للمستخدمين المسجلين ====================
Route::middleware('auth')->group(function () {
    
    // 🔄 التوجيه التلقائي بعد التسجيل حسب الدور
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'training_coordinator') {
            return redirect()->route('training-coordinator.dashboard');
        } elseif ($user->role === 'graduate') {
            return redirect()->route('graduate.dashboard');
        }
        elseif ($user->role === 'partnership_officer') {
            return redirect()->route('partnership.dashboard');
        }
        elseif ($user->role === 'company') {
            return redirect()->route('company.dashboard');
        } elseif ($user->role === 'evaluation_followup') {
            return redirect()->route('evaluation-followup.dashboard');
        } elseif ($user->role === 'career_guidance_officer') {
            return redirect()->route('career-guidance.dashboard');
        }
        // إذا لم يكن هناك توجيه، ارجع للصفحة الرئيسية
        return redirect('/');
    })->name('dashboard');

  // ==================== 👑 مسارات المدير (Admin) ====================
Route::prefix('admin')->middleware('admin')->group(function () {
    
    // 📊 لوحة التحكم والإحصائيات
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/users', [AdminController::class, 'usersReport'])->name('admin.reports.users');
    Route::get('/reports/companies', [AdminController::class, 'companiesReport'])->name('admin.reports.companies');
    Route::get('/reports/trainings', [AdminController::class, 'trainingsReport'])->name('admin.reports.trainings');
    
    // 👥 إدارة المستخدمين
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    
    // 🏢 إدارة الشركات
    Route::get('/companies', [CompanyController::class, 'index'])->name('admin.companies');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('admin.companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('admin.companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('admin.companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('admin.companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
    Route::patch('/companies/{id}/approve', [AdminController::class, 'approveCompany'])->name('admin.companies.approve');
    
    // 🎯 إدارة برامج التدريب
    Route::resource('trainings', TrainingController::class)->names([
        'index' => 'admin.trainings',
        'create' => 'admin.trainings.create',
        'store' => 'admin.trainings.store',
        'show' => 'admin.trainings.show',
        'edit' => 'admin.trainings.edit',
        'update' => 'admin.trainings.update',
        'destroy' => 'admin.trainings.destroy'
    ]);
    
    // 📝 إدارة طلبات التدريب
    Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications.index');
    Route::post('/applications/{id}/approve', [AdminController::class, 'approveApplication'])->name('admin.applications.approve');
    Route::post('/applications/{id}/reject', [AdminController::class, 'rejectApplication'])->name('admin.applications.reject');
    Route::post('/applications/{id}/pending', [AdminController::class, 'pendingApplication'])->name('admin.applications.pending');

    // ==================== 🎓 إدارة الإرشاد المهني للمدير ====================
    Route::prefix('career-guidance')->group(function () {
        // 📊 لوحة التحكم والإحصائيات للإرشاد المهني
        Route::get('/dashboard', [CareerGuidanceController::class, 'dashboard'])->name('admin.career-guidance.dashboard');
        //  إدارة الخريجين
        Route::get('/graduates', [CareerGuidanceController::class, 'graduates'])->name('admin.career-guidance.graduates');
        Route::get('/graduates/create', [CareerGuidanceController::class, 'createGraduate'])->name('admin.career-guidance.graduates.create');
        Route::post('/graduates', [CareerGuidanceController::class, 'storeGraduate'])->name('admin.career-guidance.graduates.store');
        Route::get('/graduates/{id}', [CareerGuidanceController::class, 'showGraduate'])->name('admin.career-guidance.graduates.show');
        Route::get('/graduates/{id}/edit', [CareerGuidanceController::class, 'editGraduate'])->name('admin.career-guidance.graduates.edit');
        Route::put('/graduates/{id}', [CareerGuidanceController::class, 'updateGraduate'])->name('admin.career-guidance.graduates.update');

        // 📨 إدارة الترشيحات
        Route::get('/nominations', [CareerGuidanceController::class, 'nominations'])->name('admin.career-guidance.nominations');
        Route::get('/nominations/create', [CareerGuidanceController::class, 'createNomination'])->name('admin.career-guidance.nominations.create');
        Route::post('/nominations', [CareerGuidanceController::class, 'nominateGraduate'])->name('admin.career-guidance.nominations.store');
        Route::get('/nominations/{id}', [CareerGuidanceController::class, 'showNomination'])->name('admin.career-guidance.nominations.show');
        Route::post('/nominations/{id}/status', [CareerGuidanceController::class, 'updateNominationStatus'])->name('admin.career-guidance.nominations.update-status');

        // 📈 التقارير المتقدمة
        Route::get('/advanced-reports', [CareerGuidanceController::class, 'advancedReports'])->name('admin.career-guidance.advanced-reports');
        Route::get('/export-reports/pdf', [CareerGuidanceController::class, 'exportReportsPDF'])->name('admin.career-guidance.export-reports.pdf');
        Route::get('/export-reports/excel', [CareerGuidanceController::class, 'exportReportsExcel'])->name('admin.career-guidance.export-reports.excel');
        
        // استيراد البيانات
        Route::get('/download-template', [CareerGuidanceController::class, 'downloadTemplate'])->name('admin.career-guidance.download.template');
        Route::get('/import-graduates/create', [CareerGuidanceController::class, 'showImportForm'])->name('admin.career-guidance.import.graduates.create');
        Route::post('/import-graduates', [CareerGuidanceController::class, 'importGraduates'])->name('admin.career-guidance.import.graduates');
    });

}); // نهاية مجموعة مسارات المدير
     // نهاية مجموعة مسارات المدير

    // ==================== 📚 مسارات منسق التدريب ====================
    Route::prefix('coordinator')->middleware('training_coordinator')->group(function () {
        
        // 📊 لوحة تحكم منسق التدريب
        Route::get('/dashboard', [TrainingController::class, 'coordinatorDashboard'])->name('training-coordinator.dashboard');
        
        // 📅 التقويم
        Route::get('/calendar', [TrainingController::class, 'calendar'])->name('training-coordinator.calendar');
        
        // 📋 طلبات التدريب الخاصة بالمنسق
        Route::get('/applications', [TrainingController::class, 'coordinatorApplications'])->name('training-coordinator.applications');
        Route::post('/applications/{id}/approve', [TrainingController::class, 'approveApplication'])->name('training-coordinator.applications.approve');
        Route::post('/applications/{id}/reject', [TrainingController::class, 'rejectApplication'])->name('training-coordinator.applications.reject');
        Route::post('/applications/{id}/pending', [TrainingController::class, 'pendingApplication'])->name('training-coordinator.applications.pending');
        Route::get('/trainings', [TrainingController::class, 'coordinatorTrainings'])->name('training-coordinator.trainings');
        Route::get('/trainings/create', [TrainingController::class, 'create'])->name('training-coordinator.trainings.create');

        // 🎯 إدارة التدريبات لمنسق التدريب
        Route::resource('trainings', TrainingController::class)->names([
            'index' => 'training-coordinator.trainings',
            'create' => 'training-coordinator.trainings.create',
            'store' => 'training-coordinator.trainings.store',
            'show' => 'training-coordinator.trainings.show',
            'edit' => 'training-coordinator.trainings.edit',
            'update' => 'training-coordinator.trainings.update',
            'destroy' => 'training-coordinator.trainings.destroy'
        ]);
        
        // التقارير
        Route::get('/reports', [TrainingController::class, 'reports'])->name('training-coordinator.reports');
        Route::post('/reports/upload', [TrainingController::class, 'uploadReport'])->name('training-coordinator.reports.upload');
        Route::get('/reports/template/{type}', [TrainingController::class, 'downloadTemplate'])->name('training-coordinator.reports.download-template');
        Route::get('/reports/download/{id}', [TrainingController::class, 'downloadReport'])->name('training-coordinator.reports.download');
        Route::delete('/reports/delete/{id}', [TrainingController::class, 'deleteReport'])->name('training-coordinator.reports.delete'); 
        // معاينة وتحليل التقارير
        Route::get('/reports/preview/{id}', [TrainingController::class, 'previewReport'])->name('training-coordinator.reports.preview');
        Route::get('/reports/analyze/{id}', [TrainingController::class, 'analyzeReport'])->name('training-coordinator.reports.analyze');
        Route::delete('/reports/delete/{id}', [TrainingController::class, 'deleteReport'])->name('training-coordinator.reports.delete');

        // تصدير نتائج التحليل
        Route::get('/reports/export/{id}/{format}', [TrainingController::class, 'exportAnalysis'])->name('training-coordinator.reports.export');

    }); // نهاية مجموعة مسارات منسق التدريب

    // ==================== 🎓 مسارات الخريج ====================
    Route::prefix('graduate')->middleware('graduate')->group(function () {
        
        // 📊 لوحة تحكم الخريج
        Route::get('/dashboard', [GraduateController::class, 'dashboard'])->name('graduate.dashboard');
        
        // 👤 الملف الشخصي للخريج
        Route::get('/profile', [GraduateController::class, 'profile'])->name('graduate.profile');
        Route::put('/profile', [GraduateController::class, 'updateProfile'])->name('graduate.profile.update');
        
        // 🎯 برامج التدريب المتاحة
        Route::get('/trainings', [TrainingController::class, 'availableTrainings'])->name('graduate.trainings');
        Route::get('/trainings/{training}', [TrainingController::class, 'showTraining'])->name('graduate.trainings.show');
        
        // 📝 تقديم طلبات التدريب
        Route::post('/trainings/{training}/apply', [TrainingController::class, 'submitApplication'])->name('graduate.trainings.apply');
        
    }); // نهاية مجموعة مسارات الخريج

    // ==================== 🤝 مسارات مسؤول الشراكات والتوظيف ====================
    Route::prefix('partnership')->middleware(['auth', 'partnership_officer'])->group(function () {
        
        // 📊 لوحة التحكم
        Route::get('/dashboard', [PartnershipController::class, 'dashboard'])->name('partnership.dashboard');
        
        // 🏢 إدارة الشركات - كاملة
        Route::get('/companies', [PartnershipController::class, 'companies'])->name('partnership.companies');
        Route::get('/companies/create', [PartnershipController::class, 'createCompany'])->name('partnership.companies.create');
        Route::post('/companies', [PartnershipController::class, 'storeCompany'])->name('partnership.companies.store');
        Route::get('/companies/{company}', [PartnershipController::class, 'showCompany'])->name('partnership.companies.show');
        Route::get('/companies/{company}/edit', [PartnershipController::class, 'editCompany'])->name('partnership.companies.edit');
        Route::put('/companies/{company}', [PartnershipController::class, 'updateCompany'])->name('partnership.companies.update');
        Route::delete('/companies/{company}', [PartnershipController::class, 'destroyCompany'])->name('partnership.companies.destroy');
        Route::put('/companies/{id}/partnership', [PartnershipController::class, 'updateCompanyPartnership'])->name('partnership.companies.update-partnership');
        
        // 📎 إدارة الوثائق
        Route::get('/documents', [PartnershipController::class, 'documents'])->name('partnership.documents');
        Route::get('/documents/create', [PartnershipController::class, 'createDocument'])->name('partnership.documents.create');
        Route::post('/documents', [PartnershipController::class, 'storeDocument'])->name('partnership.documents.store');
        Route::get('/documents/{document}/edit', [PartnershipController::class, 'editDocument'])->name('partnership.documents.edit');
        Route::put('/documents/{document}', [PartnershipController::class, 'updateDocument'])->name('partnership.documents.update');
        Route::post('/companies/{companyId}/documents', [PartnershipController::class, 'uploadDocument'])->name('partnership.documents.upload');
        Route::delete('/documents/{id}', [PartnershipController::class, 'deleteDocument'])->name('partnership.documents.delete');
        Route::get('/job-opportunities/template', [JobOpportunityController::class, 'downloadTemplate'])->name('job-opportunities.template');

        // 👥 استيراد بيانات الخريجين
        Route::get('/import-graduates', [PartnershipController::class, 'importGraduatesForm'])->name('partnership.import-graduates');
        Route::post('/import-graduates', [PartnershipController::class, 'importGraduates'])->name('partnership.import-graduates.store');
        Route::post('/graduates/import', [PartnershipController::class, 'importGraduates'])->name('partnership.graduates.import');
        // رابط تحميل نموذج Excel
        Route::get('/templates/job_opportunities_template', function () {
            return response()->download(storage_path('app/templates/job_opportunities_template.xlsx'));
        })->name('templates.job-opportunities');
        // 📈 التقارير
        Route::get('/reports', [PartnershipController::class, 'reports'])->name('partnership.reports');
         Route::get('/export-reports/pdf', [CareerGuidanceController::class, 'exportReportsPDF'])
         ->name('export.reports.pdf');
    
    Route::get('/export-reports/excel', [CareerGuidanceController::class, 'exportReportsExcel'])
         ->name('export.reports.excel');

        // Temporary route for document path testing
        Route::get('/documents/test-path/{path}', [PartnershipController::class, 'testDocumentPath'])->name('partnership.documents.test-path');
    }); // نهاية مجموعة مسارات مسؤول الشراكات

    // ==================== 📈 مسارات التقييم والمتابعة ====================
    Route::prefix('evaluation-followup')->middleware(['auth', 'evaluation_followup'])->group(function () {
        Route::get('/dashboard', [EvaluationFollowupController::class, 'dashboard'])->name('evaluation-followup.dashboard');
        Route::get('/training-calendar', [EvaluationFollowupController::class, 'trainingCalendarIndex'])->name('evaluation-followup.training-calendar');
        Route::get('/training-programs', [EvaluationFollowupController::class, 'trainingProgramsIndex'])->name('evaluation-followup.training-programs.index');
        Route::get('/training-programs/create', [EvaluationFollowupController::class, 'trainingProgramsCreate'])->name('evaluation-followup.training-programs.create');
        Route::get('/training-applications', [EvaluationFollowupController::class, 'trainingApplicationsIndex'])->name('evaluation-followup.training-applications.index');
        Route::get('/training-statistics', [EvaluationFollowupController::class, 'trainingStatistics'])->name('evaluation-followup.training-statistics');
        Route::get('/training-reports', [EvaluationFollowupController::class, 'trainingReportsIndex'])->name('evaluation-followup.training-reports');
        Route::get('/partnership-employment-reports', [EvaluationFollowupController::class, 'partnershipEmploymentReports'])->name('evaluation-followup.partnership-employment-reports');
        Route::get('/career-guidance-advanced-reports', [EvaluationFollowupController::class, 'careerGuidanceAdvancedReportsIndex'])->name('evaluation-followup.career-guidance-advanced-reports');
        Route::get('/export-reports/pdf', [EvaluationFollowupController::class, 'exportReportsPDF'])->name('evaluation-followup.export-reports.pdf');
        Route::get('/export-reports/excel', [EvaluationFollowupController::class, 'exportReportsExcel'])->name('evaluation-followup.export-reports.excel');
    });

    // ==================== 🎓 مسارات الإرشاد المهني (للمستخدمين غير المدراء) ====================
    Route::prefix('career-guidance')->middleware(['auth', 'career_guidance_officer'])->group(function () {
        Route::get('/dashboard', [CareerGuidanceController::class, 'dashboard'])->name('career-guidance.dashboard');
        Route::get('/nominations', [CareerGuidanceController::class, 'nominations'])->name('career-guidance.nominations');
        Route::get('/nominations/create', [CareerGuidanceController::class, 'createNomination'])->name('career-guidance.nominations.create');
        Route::post('/nominations', [CareerGuidanceController::class, 'nominateGraduate'])->name('career-guidance.nominations.store');
        Route::get('/nominations/{id}', [CareerGuidanceController::class, 'showNomination'])->name('career-guidance.nominations.show');
        Route::post('/nominations/{id}/status', [CareerGuidanceController::class, 'updateNominationStatus'])->name('career-guidance.nominations.update-status');
        Route::get('/graduates', [CareerGuidanceController::class, 'graduates'])->name('career-guidance.graduates');
        Route::get('/graduates/create', [CareerGuidanceController::class, 'createGraduate'])->name('career-guidance.graduates.create');
        Route::post('/graduates', [CareerGuidanceController::class, 'storeGraduate'])->name('career-guidance.graduates.store');
        Route::get('/graduates/{id}', [CareerGuidanceController::class, 'showGraduate'])->name('career-guidance.graduates.show');
        Route::get('/graduates/{id}/edit', [CareerGuidanceController::class, 'editGraduate'])->name('career-guidance.graduates.edit');
        Route::put('/graduates/{id}', [CareerGuidanceController::class, 'updateGraduate'])->name('career-guidance.graduates.update');
        Route::get('/advanced-reports', [CareerGuidanceController::class, 'advancedReports'])->name('career-guidance.advanced-reports');
        Route::get('/export-reports/pdf', [CareerGuidanceController::class, 'exportReportsPDF'])->name('career-guidance.export-reports.pdf');
        Route::get('/export-reports/excel', [CareerGuidanceController::class, 'exportReportsExcel'])->name('career-guidance.export-reports.excel');
        Route::get('/download-template', [CareerGuidanceController::class, 'downloadTemplate'])->name('career-guidance.download.template');
        Route::get('/import-graduates/create', [CareerGuidanceController::class, 'showImportForm'])->name('career-guidance.import.graduates.create');
        Route::post('/import-graduates', [CareerGuidanceController::class, 'importGraduates'])->name('career-guidance.import.graduates');

        // إدارة الشركات لمسؤول الإرشاد المهني
        Route::get('/companies', [CompanyController::class, 'index'])->name('career-guidance.companies');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('career-guidance.companies.show');
    });

    // ==================== 💼 مسارات فرص العمل والتدريب ====================
    Route::prefix('job-opportunities')->middleware(['auth'])->group(function () {
        Route::get('/create', [JobOpportunityController::class, 'create'])->name('job-opportunities.create');
        Route::post('/', [JobOpportunityController::class, 'store'])->name('job-opportunities.store');
        Route::get('/', [JobOpportunityController::class, 'index'])->name('job-opportunities.index');

        // مسارات متاحة لمسؤول الشراكات والإرشاد
        Route::get('/search', [JobOpportunityController::class, 'search'])->name('job-opportunities.search');
        Route::get('/{id}', [JobOpportunityController::class, 'show'])->name('job-opportunities.show');
        Route::get('/{id}/nominations', [JobOpportunityController::class, 'nominations'])->name('job-opportunities.nominations');
        Route::get('/statistics', [JobOpportunityController::class, 'statistics'])->name('job-opportunities.statistics');
        
        // مسارات خاصة بمسؤول الشراكات فقط
        Route::middleware('partnership_officer')->group(function () {
            Route::get('/{id}/edit', [JobOpportunityController::class, 'edit'])->name('job-opportunities.edit');
            Route::put('/{id}', [JobOpportunityController::class, 'update'])->name('job-opportunities.update');
            Route::delete('/{id}', [JobOpportunityController::class, 'destroy'])->name('job-opportunities.destroy');
            Route::post('/{id}/status', [JobOpportunityController::class, 'updateStatus'])->name('job-opportunities.update-status');
            Route::post('/import', [JobOpportunityController::class, 'importFromExcel'])->name('job-opportunities.import');
            Route::post('/{id}/duplicate', [JobOpportunityController::class, 'duplicate'])->name('job-opportunities.duplicate');
        });
    });

    // ==================== 📝 مسارات طلبات التدريب العامة ====================
    Route::prefix('applications')->group(function () {
        Route::post('/store', [TrainingController::class, 'apply'])->name('applications.store');
        Route::patch('/{id}/approve', [TrainingController::class, 'approveApplication'])->name('applications.approve');
        Route::patch('/{id}/reject', [TrainingController::class, 'rejectApplication'])->name('applications.reject');
        Route::delete('/{id}', [TrainingController::class, 'destroyApplication'])->name('applications.destroy');
    });

    // ==================== 👤 المسارات العامة ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 🚪 تسجيل الخروج
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
    
}); // نهاية مجموعة المسارات للمستخدمين المسجلين

// ==================== 🔧 مسارات التطوير والاختبار ====================
if (app()->environment('local')) {
    Route::get('/test', function () {
        return view('test');
    });
}

// ✅ Routes للاختبار - احتفظ بها خارج مجموعة auth
Route::get('/test-graduate-create', [CareerGuidanceController::class, 'createGraduate']);

require __DIR__.'/auth.php';
