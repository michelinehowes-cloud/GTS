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
use App\Http\Controllers\EvaluationFollowupController;
use App\Http\Controllers\AdminReportController; // Add this line
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
    if (request('seed_platform') === 'uot2026') {
        try {
            // ترحيل الجداول أولاً لضمان وجود أحدث الجداول والحقول
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

            $class = request('class');
            if ($class) {
                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => $class,
                    '--force' => true,
                ]);
            } else {
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
            $seedOutput = \Illuminate\Support\Facades\Artisan::output();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تطبيق التحديثات وترحيل الجداول وتعبئة كافة البيانات بنجاح!',
                'migrate_output' => $migrateOutput,
                'seed_output' => $seedOutput,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 200);
        }
    }

    $advertisedTrainings = \App\Models\Training::orderBy('created_at', 'desc')->limit(6)->get();

    $latestNews = \App\Models\News::published()->orderBy('published_at', 'desc')->limit(3)->get();

    $activeAnnouncements = \App\Models\Announcement::where('is_active', true)->where('start_date', '<=', now())->where('end_date', '>=', now())->orderBy('created_at', 'desc')->limit(3)->get();

    // إحصائيات حية حقيقية خاضعة لإدارة وحدة الإعلام
    $statsSettings = \App\Models\MediaPlatformStat::getHomepageStats();
    $stats = [
        'graduates_count' => $statsSettings['cards']['graduates']['value'],
        'companies_count' => $statsSettings['cards']['companies']['value'],
        'trainings_count' => $statsSettings['cards']['trainings']['value'],
        'opportunities_count' => $statsSettings['cards']['opportunities']['value'],
    ];

    // جلب الفعاليات والمعارض النشطة والجارية أو القادمة (غير المنتهية)
    $activeFairs = \App\Models\JobFair::whereIn('status', ['published', 'ongoing'])
        ->get()
        ->filter(fn($fair) => !$fair->is_ended);

    // الفعالية النشطة الأقرب زمنياً
    $activeFair = $activeFairs->sortBy('event_date')->first();

    return view('welcome', compact('advertisedTrainings', 'latestNews', 'activeAnnouncements', 'stats', 'statsSettings', 'activeFair', 'activeFairs'));
})->name('home');

// أضف هذا السطر لحل المشكلة
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home_redirect');

// ==================== 📰 الأخبار والإعلانات العامة للزوار ====================
Route::get('/news/{news}', [App\Http\Controllers\NewsController::class, 'publicShow'])->name('public.news.show');
Route::get('/announcements/{announcement}', [App\Http\Controllers\AnnouncementController::class, 'publicShow'])->name('public.announcements.show');

// ==================== 🔐 نظام المصادقة ====================
Route::middleware('guest')->group(function () {
    // صفحة تسجيل الدخول
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // تسجيل خريج جديد
    Route::get('/register/graduate', [App\Http\Controllers\GraduateRegistrationController::class, 'showRegistrationForm'])->name('graduate.register');
    Route::post('/register/graduate', [App\Http\Controllers\GraduateRegistrationController::class, 'register'])->name('graduate.register.store');

    // تسجيل شركة أو مؤسسة جديدة
    Route::get('/register/company', [App\Http\Controllers\CompanyRegistrationController::class, 'showRegistrationForm'])->name('company.register');
    Route::post('/register/company', [App\Http\Controllers\CompanyRegistrationController::class, 'register'])->name('company.register.store');
    Route::get('/register/company/success', [App\Http\Controllers\CompanyRegistrationController::class, 'success'])->name('company.register.success');
});

// ==================== 🔑 تغيير كلمة المرور الإجباري ====================
Route::middleware('auth')->group(function () {
    Route::get('/password/change/force', [App\Http\Controllers\ForcePasswordChangeController::class, 'show'])->name('password.change.force');
    Route::post('/password/change/force', [App\Http\Controllers\ForcePasswordChangeController::class, 'update'])->name('password.update.force');
});

// ==================== 📊 الاستبيانات العامة ====================
Route::prefix('surveys')->group(function () {
    Route::get('/{slug}', [App\Http\Controllers\PublicSurveyController::class, 'show'])->name('public.survey.show');
    Route::post('/{slug}', [App\Http\Controllers\PublicSurveyController::class, 'store'])->name('public.survey.store');
    Route::get('/{slug}/thankyou', [App\Http\Controllers\PublicSurveyController::class, 'thankyou'])->name('public.survey.thankyou');
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
        } elseif ($user->role === 'partnership_officer') {
            return redirect()->route('partnership.dashboard');
        } elseif ($user->role === 'company') {
            $company = $user->company ?? \App\Models\Company::where('email', $user->email)->first();
            $isApproved = $company && $company->is_approved && $company->partnership_status === 'active';
            if (!$isApproved) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'عذراً، حساب شركتكم قيد المراجعة أو غير نشط. يُسمح بالدخول في حالة الاعتماد النشط فقط.',
                ]);
            }
            return redirect()->route('company.dashboard');
        } elseif ($user->role === 'evaluation_followup') {
            return redirect()->route('evaluation-followup.dashboard');
        } elseif ($user->role === 'career_guidance_officer') {
            return redirect()->route('career-guidance.dashboard');
        } elseif ($user->role === 'media_officer') {
            return redirect()->route('media.dashboard');
        } elseif ($user->role === 'staff') {
            return redirect($user->dashboard_route);
        }
        // إذا لم يكن هناك توجيه، ارجع للصفحة الرئيسية
        return redirect('/');
    })->name('dashboard');

    // ==================== 🤖 المساعد الذكي (AI Assistant / Copilot) ====================
    Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
        Route::post('/chat', [App\Http\Controllers\AiAssistantController::class, 'chat'])->name('chat');
        Route::post('/confirm-action', [App\Http\Controllers\AiAssistantController::class, 'confirmAction'])->name('confirm-action');
        Route::get('/history', [App\Http\Controllers\AiAssistantController::class, 'history'])->name('history');
        Route::delete('/history', [App\Http\Controllers\AiAssistantController::class, 'clearHistory'])->name('clear-history');
    });

    // ==================== 👑 مسارات المدير (Admin) ====================
    Route::prefix('admin')->middleware('admin')->group(function () {

        // 📊 لوحة التحكم والإحصائيات
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard/live-stats', [AdminController::class, 'liveStats'])->name('admin.dashboard.live-stats');

        // 🤖 إعدادات النظام والذكاء الاصطناعي (API Settings)
        Route::prefix('settings')->name('admin.settings.')->group(function () {
            Route::get('/ai', [App\Http\Controllers\AdminAiSettingsController::class, 'index'])->name('ai');
            Route::post('/ai', [App\Http\Controllers\AdminAiSettingsController::class, 'update'])->name('ai.update');
            Route::post('/ai/test', [App\Http\Controllers\AdminAiSettingsController::class, 'testConnection'])->name('ai.test');
        });

        // 📈 التقارير والإحصائيات
        Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::prefix('reports')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/users', [AdminReportController::class, 'usersReport'])->name('admin.reports.users');
            Route::get('/companies', [AdminReportController::class, 'companiesReport'])->name('admin.reports.companies');
            Route::get('/trainings', [AdminReportController::class, 'trainingsReport'])->name('admin.reports.trainings');
            Route::get('/audit-logs', [AdminReportController::class, 'auditLogs'])->name('admin.reports.audit-logs');
        });

        // 👥 إدارة المستخدمين
        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::get('/users-list', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Custom Actions
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::post('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('admin.users.change-password');

        // 🏢 إدارة الشركات
        Route::get('/companies', [CompanyController::class, 'index'])->name('admin.companies');
        Route::get('/companies/index.blade.php', fn() => redirect()->route('admin.companies'));
        Route::get('/companies/index', fn() => redirect()->route('admin.companies'));
        Route::get('/companies/create', [CompanyController::class, 'create'])->name('admin.companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('admin.companies.store');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('admin.companies.show');
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('admin.companies.edit');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('admin.companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
        Route::post('/companies/{id}/toggle-approval', [CompanyController::class, 'toggleApproval'])->name('admin.companies.toggle-approval');
        Route::patch('/companies/{id}/approve', [CompanyController::class, 'toggleApproval'])->name('admin.companies.approve');
        Route::patch('/companies/{id}/reset-password', [CompanyController::class, 'resetPassword'])->name('admin.companies.reset-password');
        Route::post('/companies/{id}/create-user', [CompanyController::class, 'createUserAccount'])->name('admin.companies.create-user');


        // 📊 تصدير تقرير إكسل لبرامج وورش العمل (شهري / مخصص)
        Route::get('trainings/export-report', [TrainingController::class, 'exportMonthlyReport'])->name('admin.trainings.export-report');
        Route::post('trainings/import', [TrainingController::class, 'importTrainings'])->name('admin.trainings.import');

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
        
        Route::get('trainings/{training}/scanner', [TrainingController::class, 'scanner'])->name('admin.trainings.scanner');
        Route::post('trainings/{training}/scan', [TrainingController::class, 'processScan'])->name('admin.trainings.scan');
        Route::get('trainings/{training}/attendance', [TrainingController::class, 'attendance'])->name('admin.trainings.attendance');
        Route::post('trainings/{training}/attendance/toggle', [TrainingController::class, 'toggleAttendance'])->name('admin.trainings.attendance.toggle');
        Route::get('trainings/{training}/attendance/export', [TrainingController::class, 'exportAttendance'])->name('admin.trainings.attendance.export');
        Route::post('trainings/{training}/certificates/issue', [TrainingController::class, 'issueCertificates'])->name('admin.trainings.certificates.issue');
        Route::post('trainings/{training}/bulk-accept', [TrainingController::class, 'bulkAcceptApplications'])->name('admin.trainings.bulk-accept');

        // 📝 إدارة طلبات التدريب
        Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications.index');
        Route::get('/applications-all', [AdminController::class, 'applications'])->name('admin.applications');
        Route::post('/applications/bulk-approve', [AdminController::class, 'bulkApproveApplications'])->name('admin.applications.bulk-approve');
        Route::post('/applications/bulk-reject', [AdminController::class, 'bulkRejectApplications'])->name('admin.applications.bulk-reject');
        Route::post('/applications/bulk-delete', [AdminController::class, 'bulkDeleteApplications'])->name('admin.applications.bulk-delete');
        Route::post('/applications/{id}/approve', [AdminController::class, 'approveApplication'])->name('admin.applications.approve');
        Route::post('/applications/{id}/reject', [AdminController::class, 'rejectApplication'])->name('admin.applications.reject');
        Route::post('/applications/{id}/pending', [AdminController::class, 'pendingApplication'])->name('admin.applications.pending');
        Route::delete('/applications/{id}', [AdminController::class, 'destroyApplication'])->name('admin.applications.destroy');

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
            Route::patch('/graduates/{id}/toggle-status', [CareerGuidanceController::class, 'toggleGraduateStatus'])->name('admin.career-guidance.graduates.toggle-status');
            Route::delete('/graduates/{id}', [CareerGuidanceController::class, 'destroyGraduate'])->name('admin.career-guidance.graduates.destroy');
            Route::patch('/graduates/{id}/reset-password', [CareerGuidanceController::class, 'resetGraduatePassword'])->name('admin.career-guidance.graduates.reset-password');
            Route::post('/graduates/{id}/create-user', [CareerGuidanceController::class, 'createGraduateAccount'])->name('admin.career-guidance.graduates.create-user');


            // 📨 إدارة الترشيحات
            Route::get('/nominations', [CareerGuidanceController::class, 'nominations'])->name('admin.career-guidance.nominations');
            Route::get('/nominations/create', [CareerGuidanceController::class, 'createNomination'])->name('admin.career-guidance.nominations.create');
            Route::post('/nominations', [CareerGuidanceController::class, 'nominateGraduate'])->name('admin.career-guidance.nominations.store');
            Route::get('/nominations/{id}', [CareerGuidanceController::class, 'showNomination'])->name('admin.career-guidance.nominations.show');
            // Route for updating the status - using PUT/PATCH for RESTful consistency
            Route::put('/nominations/{id}/status', [CareerGuidanceController::class, 'updateNominationStatus'])->name('admin.career-guidance.nominations.update-status');
            // Route for displaying the edit form (if any) - assuming it's a GET request
            Route::get('/nominations/{id}/edit-status', [CareerGuidanceController::class, 'editNominationStatusForm'])->name('admin.career-guidance.nominations.edit-status');
            Route::put('/nominations/{id}/status-fullpage', [CareerGuidanceController::class, 'updateNominationStatusFullPage'])->name('admin.career-guidance.nominations.update-status-fullpage');

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
    Route::redirect('/training-coordinator', '/coordinator/dashboard');
    Route::redirect('/training-coordinator/trainings', '/coordinator/trainings');
    Route::redirect('/training-coordinator/dashboard', '/coordinator/dashboard');
    Route::redirect('/training-coordinator/applications', '/coordinator/applications');
    Route::redirect('/training-coordinator/calendar', '/coordinator/calendar');

    // ==================== 📚 مسارات منسق التدريب ====================
    Route::prefix('coordinator')->middleware('training_coordinator')->group(function () {

        // 📊 لوحة تحكم منسق التدريب
        Route::get('/dashboard', [TrainingController::class, 'coordinatorDashboard'])->name('training-coordinator.dashboard');

        // 📅 التقويم
        Route::get('/calendar', [TrainingController::class, 'calendar'])->name('training-coordinator.calendar');

        // 📋 طلبات التدريب الخاصة بالمنسق
        Route::get('/applications', [TrainingController::class, 'coordinatorApplications'])->name('training-coordinator.applications');
        Route::post('/applications/bulk-approve', [TrainingController::class, 'bulkApproveApplications'])->name('training-coordinator.applications.bulk-approve');
        Route::post('/applications/bulk-reject', [TrainingController::class, 'bulkRejectApplications'])->name('training-coordinator.applications.bulk-reject');
        Route::post('/applications/bulk-delete', [TrainingController::class, 'bulkDeleteApplications'])->name('training-coordinator.applications.bulk-delete');
        Route::post('/applications/{id}/approve', [TrainingController::class, 'approveApplication'])->name('training-coordinator.applications.approve');
        Route::post('/applications/{id}/reject', [TrainingController::class, 'rejectApplication'])->name('training-coordinator.applications.reject');
        Route::post('/applications/{id}/pending', [TrainingController::class, 'pendingApplication'])->name('training-coordinator.applications.pending');
        Route::delete('/applications/{id}', [TrainingController::class, 'destroyApplication'])->name('training-coordinator.applications.destroy');
        // 📊 تصدير واستيراد تقارير وبرامج التدريب لمنسق التدريب
        Route::get('/trainings/export-report', [TrainingController::class, 'exportMonthlyReport'])->name('training-coordinator.trainings.export-report');
        Route::post('/trainings/import', [TrainingController::class, 'importTrainings'])->name('training-coordinator.trainings.import');

        // 🎯 إدارة التدريبات لمنسق التدريب
        // تم استبعاد طريقة index من مسار الموارد وتحديدها بشكل منفصل
        // لضمان استخدام طريقة coordinatorTrainings الصحيحة
        Route::resource('trainings', TrainingController::class)->except(['index'])->names([
            'create' => 'training-coordinator.trainings.create',
            'store' => 'training-coordinator.trainings.store',
            'show' => 'training-coordinator.trainings.show',
            'edit' => 'training-coordinator.trainings.edit',
            'update' => 'training-coordinator.trainings.update',
            'destroy' => 'training-coordinator.trainings.destroy'
        ]);
        // مسار index مخصص لمنسق التدريب
        Route::get('/trainings', [TrainingController::class, 'coordinatorTrainings'])->name('training-coordinator.trainings');
        Route::get('/trainings/{training}/scanner', [TrainingController::class, 'scanner'])->name('training-coordinator.trainings.scanner');
        Route::post('/trainings/{training}/scan', [TrainingController::class, 'processScan'])->name('training-coordinator.trainings.scan');
        Route::get('/trainings/{training}/attendance', [TrainingController::class, 'attendance'])->name('training-coordinator.trainings.attendance');
        Route::post('/trainings/{training}/attendance/toggle', [TrainingController::class, 'toggleAttendance'])->name('training-coordinator.trainings.attendance.toggle');
        Route::get('/trainings/{training}/attendance/export', [TrainingController::class, 'exportAttendance'])->name('training-coordinator.trainings.attendance.export');
        Route::post('/trainings/{training}/certificates/issue', [TrainingController::class, 'issueCertificates'])->name('training-coordinator.trainings.certificates.issue');

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

        // 👨‍🏫 إدارة المدربين
        Route::resource('trainers', App\Http\Controllers\TrainerController::class)->names([
            'index' => 'training-coordinator.trainers.index',
            'create' => 'training-coordinator.trainers.create',
            'store' => 'training-coordinator.trainers.store',
            'show' => 'training-coordinator.trainers.show',
            'edit' => 'training-coordinator.trainers.edit',
            'update' => 'training-coordinator.trainers.update',
            'destroy' => 'training-coordinator.trainers.destroy'
        ]);
        // تقييم المدربين
        Route::post('trainers/{trainer}/evaluate', [App\Http\Controllers\TrainerController::class, 'storeEvaluation'])
            ->name('training-coordinator.trainers.evaluate');

    }); // نهاية مجموعة مسارات منسق التدريب

    // ==================== 🎓 مسارات الخريج ====================
    Route::prefix('graduate')->middleware('graduate')->group(function () {

        // 📊 لوحة تحكم الخريج
        Route::get('/dashboard', [GraduateController::class, 'dashboard'])->name('graduate.dashboard');
        Route::get('/id-card', [GraduateController::class, 'idCard'])->name('graduate.id-card');

        // 👤 الملف الشخصي للخريج
        Route::get('/profile', [GraduateController::class, 'profile'])->name('graduate.profile');
        Route::put('/profile', [GraduateController::class, 'updateProfile'])->name('graduate.profile.update');
        Route::put('/profile/password', [GraduateController::class, 'updatePassword'])->name('graduate.password.update');

        // 📄 إدارة السيرة الذاتية
        Route::post('/cv/upload', [GraduateController::class, 'uploadCV'])->name('graduate.cv.upload');
        Route::get('/cv/download', [GraduateController::class, 'downloadCV'])->name('graduate.cv.download');
        Route::get('/cv/view', [GraduateController::class, 'viewCV'])->name('graduate.cv.view');


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
        Route::get('/companies/index.blade.php', fn() => redirect()->route('partnership.companies'));
        Route::get('/companies/index', fn() => redirect()->route('partnership.companies'));
        Route::get('/companies', [PartnershipController::class, 'companies'])->name('partnership.companies');
        Route::get('/companies/create', [PartnershipController::class, 'createCompany'])->name('partnership.companies.create');
        Route::post('/companies', [PartnershipController::class, 'storeCompany'])->name('partnership.companies.store');
        Route::get('/companies/{company}', [PartnershipController::class, 'showCompany'])->name('partnership.companies.show');
        Route::get('/companies/{company}/edit', [PartnershipController::class, 'editCompany'])->name('partnership.companies.edit');
        Route::put('/companies/{company}', [PartnershipController::class, 'updateCompany'])->name('partnership.companies.update');
        Route::delete('/companies/{company}', [PartnershipController::class, 'destroyCompany'])->name('partnership.companies.destroy');
        Route::post('/companies/{id}/toggle-approval', [PartnershipController::class, 'toggleApproval'])->name('partnership.companies.toggle-approval');
        Route::post('/companies/{id}/approve', [PartnershipController::class, 'approveCompany'])->name('partnership.companies.approve');
        Route::post('/companies/{id}/reject', [PartnershipController::class, 'rejectCompany'])->name('partnership.companies.reject');
        Route::put('/companies/{id}/partnership', [PartnershipController::class, 'updateCompanyPartnership'])->name('partnership.companies.update-partnership');

        // 📎 إدارة الوثائق
        Route::get('/documents', [PartnershipController::class, 'documents'])->name('partnership.documents');
        Route::get('/documents/create', [PartnershipController::class, 'createDocument'])->name('partnership.documents.create');
        Route::post('/documents', [PartnershipController::class, 'storeDocument'])->name('partnership.documents.store');
        Route::get('/documents/{document}', [PartnershipController::class, 'showDocument'])->name('partnership.documents.show');
        Route::get('/documents/{document}/download', [PartnershipController::class, 'downloadDocument'])->name('partnership.documents.download');
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

        // 📨 إدارة الترشيحات
        Route::get('/nominations', [CareerGuidanceController::class, 'nominations'])->name('partnership.nominations');
        Route::get('/nominations/create', [CareerGuidanceController::class, 'createNomination'])->name('partnership.nominations.create');
        Route::post('/nominations', [CareerGuidanceController::class, 'nominateGraduate'])->name('partnership.nominations.store');
        Route::get('/nominations/{id}', [CareerGuidanceController::class, 'showNomination'])->name('partnership.nominations.show');
        // Route for displaying the edit form (if any) - assuming it's a GET request
        Route::get('/nominations/{id}/edit-status', [CareerGuidanceController::class, 'editNominationStatusForm'])->name('partnership.nominations.edit-status');
        // Route for updating the status - using PUT/PATCH for RESTful consistency
        Route::put('/nominations/{id}/status', [CareerGuidanceController::class, 'updateNominationStatus'])->name('partnership.nominations.update-status');
        Route::put('/nominations/{id}/status-fullpage', [CareerGuidanceController::class, 'updateNominationStatusFullPage'])->name('partnership.nominations.update-status-fullpage');

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
        Route::get('/training-programs-list', [EvaluationFollowupController::class, 'trainingProgramsIndex'])->name('evaluation-followup.training-programs');
        Route::get('/training-programs/create', [EvaluationFollowupController::class, 'trainingProgramsCreate'])->name('evaluation-followup.training-programs.create');
        Route::get('/training-applications', [EvaluationFollowupController::class, 'trainingApplicationsIndex'])->name('evaluation-followup.training-applications.index');
        Route::get('/training-statistics', [EvaluationFollowupController::class, 'trainingStatistics'])->name('evaluation-followup.training-statistics');
        Route::get('/training-reports', [EvaluationFollowupController::class, 'trainingReportsIndex'])->name('evaluation-followup.training-reports');
        Route::get('/partnership-employment-reports', [EvaluationFollowupController::class, 'partnershipEmploymentReports'])->name('evaluation-followup.partnership-employment-reports');
        Route::get('/career-guidance-advanced-reports', [EvaluationFollowupController::class, 'careerGuidanceAdvancedReportsIndex'])->name('evaluation-followup.career-guidance-advanced-reports');
        Route::get('/export-reports/pdf', [EvaluationFollowupController::class, 'exportReportsPDF'])->name('evaluation-followup.export-reports.pdf');
        Route::get('/export-reports/excel', [EvaluationFollowupController::class, 'exportReportsExcel'])->name('evaluation-followup.export-reports.excel');

        // ==================== 📋 قوالب الاستبيانات ====================
        Route::get('surveys/templates', [App\Http\Controllers\SurveyTemplateController::class, 'index'])->name('evaluation-followup.surveys.templates.index');
        Route::get('surveys/templates/api/list', [App\Http\Controllers\SurveyTemplateController::class, 'apiList'])->name('evaluation-followup.surveys.templates.api-list');
        Route::get('surveys/templates/{template}', [App\Http\Controllers\SurveyTemplateController::class, 'show'])->name('evaluation-followup.surveys.templates.show');
        Route::post('surveys/templates', [App\Http\Controllers\SurveyTemplateController::class, 'store'])->name('evaluation-followup.surveys.templates.store');
        Route::delete('surveys/templates/{template}', [App\Http\Controllers\SurveyTemplateController::class, 'destroy'])->name('evaluation-followup.surveys.templates.destroy');
        Route::post('surveys/{survey}/save-template', [App\Http\Controllers\SurveyTemplateController::class, 'saveFromSurvey'])->name('evaluation-followup.surveys.save-template');

        // ==================== 📊 إدارة الاستبيانات ====================
        Route::get('surveys/{survey}/report', [App\Http\Controllers\SurveyController::class, 'report'])->name('evaluation-followup.surveys.report');
        Route::get('surveys/{survey}/export-responses', [App\Http\Controllers\SurveyController::class, 'exportResponses'])->name('evaluation-followup.surveys.export-responses');
        Route::resource('surveys', App\Http\Controllers\SurveyController::class)->names([
            'index' => 'evaluation-followup.surveys.index',
            'create' => 'evaluation-followup.surveys.create',
            'store' => 'evaluation-followup.surveys.store',
            'show' => 'evaluation-followup.surveys.show',
            'edit' => 'evaluation-followup.surveys.edit',
            'update' => 'evaluation-followup.surveys.update',
            'destroy' => 'evaluation-followup.surveys.destroy'
        ]);

        // ==================== 📈 إدارة التقييمات ====================
        Route::resource('evaluations', App\Http\Controllers\EvaluationController::class)->names([
            'index' => 'evaluation-followup.evaluations.index',
            'create' => 'evaluation-followup.evaluations.create',
            'store' => 'evaluation-followup.evaluations.store',
            'show' => 'evaluation-followup.evaluations.show',
            'edit' => 'evaluation-followup.evaluations.edit',
            'update' => 'evaluation-followup.evaluations.update',
            'destroy' => 'evaluation-followup.evaluations.destroy'
        ]);

        // ==================== 📋 إدارة ردود الاستبيانات ====================
        Route::get('/survey-responses', [App\Http\Controllers\SurveyResponseController::class, 'index'])->name('evaluation-followup.survey-responses.index');
        Route::get('/survey-responses/{response}', [App\Http\Controllers\SurveyResponseController::class, 'show'])->name('evaluation-followup.survey-responses.show');
        Route::delete('/survey-responses/{response}', [App\Http\Controllers\SurveyResponseController::class, 'destroy'])->name('evaluation-followup.survey-responses.destroy');

        // ==================== 📊 تقارير التقييم ====================
        Route::get('/evaluation-reports', [EvaluationFollowupController::class, 'evaluationReports'])->name('evaluation-followup.evaluation-reports');
        Route::get('/survey-reports', [EvaluationFollowupController::class, 'surveyReports'])->name('evaluation-followup.survey-reports');
        Route::get('/performance-reports', [EvaluationFollowupController::class, 'performanceReports'])->name('evaluation-followup.performance-reports');
        Route::get('/monthly-training-report', [EvaluationFollowupController::class, 'monthlyTrainingReport'])->name('evaluation-followup.monthly-training-report');
    });

    // ==================== 📹 مسارات مسؤول الميديا ====================
    Route::prefix('media')->middleware(['auth', 'media_officer'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\MediaController::class, 'dashboard'])->name('media.dashboard');


        // 📅 تقويم وجدول التغطيات الإعلامية
        Route::get('/coverage-calendar', [App\Http\Controllers\MediaController::class, 'coverageCalendar'])->name('media.coverage-calendar');
        Route::post('/coverage-calendar/{training}/update', [App\Http\Controllers\MediaController::class, 'updateCoverageTask'])->name('media.coverage-calendar.update');

        // إدارة التدريبات (فهرس التدريبات)
        Route::get('/trainings', [App\Http\Controllers\TrainingController::class, 'coordinatorTrainings'])->name('media.trainings.index');
        Route::get('/trainings/{training}', [App\Http\Controllers\MediaController::class, 'trainingShow'])->name('media.trainings.show');
        Route::patch('/trainings/{training}/coverage-status', [App\Http\Controllers\MediaController::class, 'updateCoverageStatus'])->name('media.trainings.update-coverage-status');
        Route::get('/training-calendar', [App\Http\Controllers\MediaController::class, 'trainingCalendarIndex'])->name('media.training-calendar');


        // إدارة الأخبار
        Route::resource('news', App\Http\Controllers\NewsController::class)->names([
            'index' => 'media.news.index',
            'create' => 'media.news.create',
            'store' => 'media.news.store',
            'show' => 'media.news.show',
            'edit' => 'media.news.edit',
            'update' => 'media.news.update',
            'destroy' => 'media.news.destroy'
        ]);
        Route::patch('/news/{news}/toggle-status', [App\Http\Controllers\NewsController::class, 'toggleStatus'])->name('media.news.toggle-status');

        // إدارة الإعلانات
        Route::resource('announcements', App\Http\Controllers\AnnouncementController::class)->names([
            'index' => 'media.announcements.index',
            'create' => 'media.announcements.create',
            'store' => 'media.announcements.store',
            'show' => 'media.announcements.show',
            'edit' => 'media.announcements.edit',
            'update' => 'media.announcements.update',
            'destroy' => 'media.announcements.destroy'
        ]);
        Route::patch('/announcements/{announcement}/toggle-status', [App\Http\Controllers\AnnouncementController::class, 'toggleStatus'])->name('media.announcements.toggle-status');

        // تقارير التغطية
        Route::get('/reports/coverage', [App\Http\Controllers\MediaController::class, 'reportsIndex'])->name('media.reports.coverage');
        Route::get('/reports/coverage/create', [App\Http\Controllers\MediaController::class, 'createCoverageReportForm'])->name('media.reports.coverage.create');
        Route::post('/reports/coverage', [App\Http\Controllers\MediaController::class, 'storeCoverageReport'])->name('media.reports.coverage.store');
        Route::get('/reports/coverage/{training}', [App\Http\Controllers\MediaController::class, 'createCoverageReport'])->name('media.reports.coverage.show');
        Route::get('/reports/coverage/{training}/edit', [App\Http\Controllers\MediaController::class, 'editCoverageReport'])->name('media.reports.coverage.edit');
        Route::put('/reports/coverage/{training}', [App\Http\Controllers\MediaController::class, 'updateCoverageReport'])->name('media.reports.coverage.update');

        // 📊 إدارة إحصائيات المنصة والصفحة الرئيسية
        Route::get('/platform-stats', [App\Http\Controllers\MediaController::class, 'platformStats'])->name('media.platform-stats');
        Route::post('/platform-stats', [App\Http\Controllers\MediaController::class, 'updatePlatformStats'])->name('media.platform-stats.update');
    });

    // ==================== 🎓 مسارات الإرشاد المهني (للمستخدمين غير المدراء) ====================
    Route::prefix('career-guidance')->middleware(['auth', 'career_guidance_officer'])->group(function () {
        Route::get('/dashboard', [CareerGuidanceController::class, 'dashboard'])->name('career-guidance.dashboard');
        Route::get('/nominations', [CareerGuidanceController::class, 'nominations'])->name('career-guidance.nominations');
        Route::get('/nominations/create', [CareerGuidanceController::class, 'createNomination'])->name('career-guidance.nominations.create');
        Route::post('/nominations', [CareerGuidanceController::class, 'nominateGraduate'])->name('career-guidance.nominations.store');
        Route::get('/nominations/{id}', [CareerGuidanceController::class, 'showNomination'])->name('career-guidance.nominations.show');
        Route::post('/nominations/{id}/status', [CareerGuidanceController::class, 'updateNominationStatus'])->name('career-guidance.nominations.update-status');
        Route::get('/nominations/{id}/edit-status', [CareerGuidanceController::class, 'editNominationStatusForm'])->name('career-guidance.nominations.edit-status');
        Route::put('/nominations/{id}/status-fullpage', [CareerGuidanceController::class, 'updateNominationStatusFullPage'])->name('career-guidance.nominations.update-status-fullpage');
        Route::get('/graduates', [CareerGuidanceController::class, 'graduates'])->name('career-guidance.graduates');
        Route::get('/graduates/create', [CareerGuidanceController::class, 'createGraduate'])->name('career-guidance.graduates.create');
        Route::post('/graduates', [CareerGuidanceController::class, 'storeGraduate'])->name('career-guidance.graduates.store');
        Route::get('/graduates/{id}', [CareerGuidanceController::class, 'showGraduate'])->name('career-guidance.graduates.show');
        Route::get('/graduates/{id}/edit', [CareerGuidanceController::class, 'editGraduate'])->name('career-guidance.graduates.edit');
        Route::put('/graduates/{id}', [CareerGuidanceController::class, 'updateGraduate'])->name('career-guidance.graduates.update');
        Route::patch('/graduates/{id}/toggle-status', [CareerGuidanceController::class, 'toggleGraduateStatus'])->name('career-guidance.graduates.toggle-status');
        Route::delete('/graduates/{id}', [CareerGuidanceController::class, 'destroyGraduate'])->name('career-guidance.graduates.destroy');
        Route::patch('/graduates/{id}/reset-password', [CareerGuidanceController::class, 'resetGraduatePassword'])->name('career-guidance.graduates.reset-password');
        Route::post('/graduates/{id}/create-user', [CareerGuidanceController::class, 'createGraduateAccount'])->name('career-guidance.graduates.create-user');
        Route::get('/advanced-reports', [CareerGuidanceController::class, 'advancedReports'])->name('career-guidance.advanced-reports');
        Route::get('/export-reports/pdf', [CareerGuidanceController::class, 'exportReportsPDF'])->name('career-guidance.export-reports.pdf');
        Route::get('/export-reports/excel', [CareerGuidanceController::class, 'exportReportsExcel'])->name('career-guidance.export-reports.excel');
        Route::get('/download-template', [CareerGuidanceController::class, 'downloadTemplate'])->name('career-guidance.download.template');
        Route::get('/import-graduates/create', [CareerGuidanceController::class, 'showImportForm'])->name('career-guidance.import.graduates.create');
        Route::post('/import-graduates', [CareerGuidanceController::class, 'importGraduates'])->name('career-guidance.import.graduates');

        // إدارة الشركات لمسؤول الإرشاد المهني
        Route::get('/companies', [CompanyController::class, 'index'])->name('career-guidance.companies');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('career-guidance.companies.show');

        // الموافقة على حسابات الخريجين
        Route::get('/pending-approvals', [App\Http\Controllers\GraduateRegistrationController::class, 'pendingApprovals'])->name('career-guidance.pending-approvals');
        Route::post('/pending-approvals/{id}/approve', [App\Http\Controllers\GraduateRegistrationController::class, 'approve'])->name('career-guidance.approve-graduate');
        Route::post('/pending-approvals/{id}/reject', [App\Http\Controllers\GraduateRegistrationController::class, 'reject'])->name('career-guidance.reject-graduate');
        Route::post('/pending-approvals/bulk-approve', [App\Http\Controllers\GraduateRegistrationController::class, 'bulkApprove'])->name('career-guidance.bulk-approve-graduates');
        Route::post('/pending-approvals/bulk-reject', [App\Http\Controllers\GraduateRegistrationController::class, 'bulkReject'])->name('career-guidance.bulk-reject-graduates');
    });

    // ==================== 💼 مسارات فرص العمل والتدريب ====================
    Route::prefix('job-opportunities')->middleware(['auth'])->group(function () {
        Route::get('/create', [JobOpportunityController::class, 'create'])->name('job-opportunities.create');
        Route::post('/', [JobOpportunityController::class, 'store'])->name('job-opportunities.store');
        Route::get('/', [JobOpportunityController::class, 'index'])->name('job-opportunities.index');
        Route::get('/index.blade.php', fn() => redirect()->route('job-opportunities.index'));
        Route::get('/index', fn() => redirect()->route('job-opportunities.index'));

        // مسارات متاحة للجميع وفق الصلاحيات وسياسات الوصول
        Route::get('/search', [JobOpportunityController::class, 'search'])->name('job-opportunities.search');
        Route::get('/{id}', [JobOpportunityController::class, 'show'])->where('id', '[0-9]+')->name('job-opportunities.show');
        Route::get('/{id}/edit', [JobOpportunityController::class, 'edit'])->where('id', '[0-9]+')->name('job-opportunities.edit');
        Route::put('/{id}', [JobOpportunityController::class, 'update'])->where('id', '[0-9]+')->name('job-opportunities.update');
        Route::delete('/{id}', [JobOpportunityController::class, 'destroy'])->where('id', '[0-9]+')->name('job-opportunities.destroy');
        Route::post('/{id}/approve', [JobOpportunityController::class, 'approve'])->where('id', '[0-9]+')->name('job-opportunities.approve');
        Route::post('/{id}/reject', [JobOpportunityController::class, 'reject'])->where('id', '[0-9]+')->name('job-opportunities.reject');
        Route::get('/{id}/nominations', [JobOpportunityController::class, 'nominations'])->where('id', '[0-9]+')->name('job-opportunities.nominations')->middleware('can:manage-nominations');
        Route::get('/statistics', [JobOpportunityController::class, 'statistics'])->name('job-opportunities.statistics');

        // مسارات خاصة بمسؤول الشراكات فقط
        Route::middleware('partnership_officer')->group(function () {
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

    // ==================== 🔔 نظام الإشعارات ====================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/{notification}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
        Route::get('/api/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.api');
        Route::match(['patch', 'post'], '/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::match(['patch', 'post'], '/{notification}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead']);
        Route::match(['patch', 'post'], '/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/read/delete', [App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('notifications.destroy-read');
        Route::get('/stats', [App\Http\Controllers\NotificationController::class, 'stats'])->name('notifications.stats');

        // مسارات الإدارة (للمدير فقط)
        Route::middleware('admin')->group(function () {
            Route::post('/test', [App\Http\Controllers\NotificationController::class, 'sendTestNotification'])->name('notifications.test');
        });
    });

    // ==================== 👤 المسارات العامة ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ==================== 🏢 مسارات الشركة ====================
    Route::middleware(['auth', 'company'])->prefix('company')->name('company.')->group(function () {
        Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');
        
        // مسارات ملف الشركة التعريفي
        Route::get('/profile', [CompanyController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [CompanyController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [CompanyController::class, 'updateProfile'])->name('profile.update');
        
        // مسارات التوظيف والمرشحين (ATS)
        Route::get('/nominations', [CompanyController::class, 'nominations'])->name('nominations');
        Route::get('/nominations/{nomination}', [CompanyController::class, 'showNomination'])->name('nominations.show');
        Route::put('/nominations/{nomination}/status', [CompanyController::class, 'updateNominationStatus'])->name('nominations.update-status');
        
        // مسارات معارض التوظيف للشركات
        Route::get('/job-fairs', [App\Http\Controllers\CompanyJobFairController::class, 'index'])->name('job-fairs.index');
        Route::get('/job-fairs/{fair}/qr-booth', [App\Http\Controllers\CompanyJobFairController::class, 'qrBooth'])->name('job-fairs.qr-booth');
        Route::get('/job-fairs/{fair}/scanner', [App\Http\Controllers\CompanyJobFairController::class, 'scanner'])->name('job-fairs.scanner');
        Route::post('/job-fairs/{fair}/scanner', [App\Http\Controllers\CompanyJobFairController::class, 'storeVisit'])->name('job-fairs.store-visit');
        Route::get('/job-fairs/{fair}/leads', [App\Http\Controllers\CompanyJobFairController::class, 'leads'])->name('job-fairs.leads');
        Route::get('/job-fairs/{fair}/search', [App\Http\Controllers\CompanyJobFairController::class, 'searchGraduates'])->name('job-fairs.search');
        Route::post('/job-fairs/update-lead-status', [App\Http\Controllers\CompanyJobFairController::class, 'updateLeadStatus'])->name('job-fairs.update-lead-status');
        Route::post('/job-fairs/visits/{visit}/outcome', [App\Http\Controllers\CompanyJobFairController::class, 'updateVisitOutcome'])->name('job-fairs.visit-outcome');
        Route::get('/job-fairs/{fair}/assets/{type}', [App\Http\Controllers\CompanyJobFairController::class, 'downloadAsset'])->name('job-fairs.download-asset');
    });

}); // نهاية مجموعة المسارات للمستخدمين المسجلين

// ==================== 🔧 مسارات التطوير والاختبار ====================
if (app()->environment('local')) {
    Route::get('/test', function () {
        return view('test');
    });
}

// ✅ Routes للاختبار - احتفظ بها خارج مجموعة auth
Route::get('/test-graduate-create', [CareerGuidanceController::class, 'createGraduate']);

// صفحة اختبار جديدة
Route::get('/new_test', function () {
    return view('new_test');
});

// ==================== 🎓 مسارات الخريجين - فرص العمل ====================
Route::middleware(['auth'])->prefix('graduate')->name('graduate.')->group(function () {
    
    // مسح الباركود للشركة
    Route::get('/job-fairs/{fair}/company/{company}/scan', [App\Http\Controllers\GraduateJobFairController::class, 'scanCompanyQr'])->name('job-fairs.company.scan');

    // فرص العمل
    Route::get('/job-opportunities', [App\Http\Controllers\GraduateJobController::class, 'index'])->name('job-opportunities.index');
    Route::get('/jobs', [App\Http\Controllers\GraduateJobController::class, 'index'])->name('jobs.index');
    Route::get('/job-opportunities/{id}', [App\Http\Controllers\GraduateJobController::class, 'show'])->name('job-opportunities.show');
    Route::post('/job-opportunities/{id}/apply', [App\Http\Controllers\GraduateJobController::class, 'apply'])->name('job-opportunities.apply');

    // ترشيحاتي ومقابلاتي
    Route::get('/my-applications', [App\Http\Controllers\GraduateJobController::class, 'myApplications'])->name('my-applications');
    Route::delete('/my-applications/{id}/cancel', [App\Http\Controllers\GraduateJobController::class, 'cancelApplication'])->name('my-applications.cancel');

    // الاستبيانات
    Route::get('/surveys', [App\Http\Controllers\Graduate\GraduateSurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{survey}', [App\Http\Controllers\Graduate\GraduateSurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [App\Http\Controllers\Graduate\GraduateSurveyController::class, 'store'])->name('surveys.store');

    // المفضلة
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{companyId}/toggle', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // 📜 شهادات المتدربين الخريجين
    Route::get('/certificates', [App\Http\Controllers\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show');
});

// ==================== 📜 التحقق العام من الشهادات ====================
Route::get('/certificates/verify/{code}', [App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify');

require __DIR__ . '/auth.php';

// ==================== 🎪 معرض التوظيف 2026 ====================

// الصفحة العامة للفعالية / المعرض (للجميع)
Route::get('/job-fair/{fair?}', [App\Http\Controllers\JobFairController::class, 'publicShow'])
    ->where('fair', '[0-9]+')
    ->name('job-fair.public');

// دليل الشركات والمؤسسات المشاركة في المعرض (للجميع)
Route::get('/job-fair/companies', [App\Http\Controllers\JobFairController::class, 'publicCompanies'])
    ->name('job-fair.public.companies.index');

Route::get('/job-fair/{fair}/companies', [App\Http\Controllers\JobFairController::class, 'publicCompanies'])
    ->where('fair', '[0-9]+')
    ->name('job-fair.public.companies');

// البرنامج العلمي والفعاليات المصاحبة (للجميع)
Route::get('/job-fair/program', [App\Http\Controllers\JobFairController::class, 'publicProgram'])
    ->name('job-fair.public.program.index');

Route::get('/job-fair/{fair}/program', [App\Http\Controllers\JobFairController::class, 'publicProgram'])
    ->where('fair', '[0-9]+')
    ->name('job-fair.public.program');

// الصفحة المستقلة للفعالية العلمية مع رمز QR (للجميع)
Route::get('/job-fair/events/{event}', [App\Http\Controllers\JobFairController::class, 'publicEventShow'])
    ->where('event', '[0-9]+')
    ->name('job-fair.public.events.show');

// مشاريع التخرج والأرشيف السنوي (للجميع)
Route::get('/job-fair/projects', [App\Http\Controllers\JobFairProjectController::class, 'publicIndex'])
    ->name('job-fair.public.projects.index');

Route::get('/job-fair/{fair}/projects', [App\Http\Controllers\JobFairProjectController::class, 'publicIndex'])
    ->where('fair', '[0-9]+')
    ->name('job-fair.public.projects');

// نموذج تقديم مشروع تخرج من قبل الخريجين بأنفسهم
Route::get('/job-fair/projects/submit', [App\Http\Controllers\JobFairProjectController::class, 'createSubmission'])
    ->name('job-fair.public.projects.submit');

Route::get('/job-fair/{fair}/projects/submit', [App\Http\Controllers\JobFairProjectController::class, 'createSubmission'])
    ->where('fair', '[0-9]+')
    ->name('job-fair.public.projects.submit-fair');

Route::post('/job-fair/projects/submit', [App\Http\Controllers\JobFairProjectController::class, 'storeSubmission'])
    ->name('job-fair.public.projects.store-submission');

// صفحة إشعار استلام مشروع التخرج
Route::get('/job-fair/projects/submitted/{project}', [App\Http\Controllers\JobFairProjectController::class, 'submissionSuccess'])
    ->where('project', '[0-9]+')
    ->name('job-fair.public.projects.submitted');

// الصفحة المستقلة لمشروع التخرج مع رمز QR وتفاصيل الفريق
Route::get('/job-fair/projects/{project}', [App\Http\Controllers\JobFairProjectController::class, 'publicShow'])
    ->where('project', '[0-9]+')
    ->name('job-fair.public.projects.show');


// تسجيل الزوار والتذكرة الرقمية للمعرض (متاح للجميع بدون تسجيل مسبق)
Route::post('/job-fair/{fair}/visitor-register', [App\Http\Controllers\JobFairVisitorController::class, 'store'])
    ->name('job-fair.visitor.register');
Route::get('/job-fair/visitor-ticket/{ticket}', [App\Http\Controllers\JobFairVisitorController::class, 'showTicket'])
    ->name('job-fair.visitor.ticket');

// تسجيل الخريج في المعرض (يتطلب تسجيل دخول)
Route::middleware('auth')->group(function () {
    Route::post('/job-fair/{fair}/register', [App\Http\Controllers\JobFairController::class, 'register'])->name('job-fair.register');
    Route::get('/job-fair/ticket/{registration}', [App\Http\Controllers\JobFairController::class, 'myTicket'])->name('job-fair.my-ticket');
    Route::get('/job-fair/{fair}/print-ticket', [App\Http\Controllers\JobFairController::class, 'printTicket'])->name('job-fair.ticket.print');
    
    // المراسلات (الشركات والخريجين)
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/api/recent', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.api.recent');
    Route::get('/messages/{id}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{id}', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');

    // السيرة الذاتية الرقمية (Digital CV Profile)
    Route::get('/graduate/profile/{id}', [App\Http\Controllers\PublicProfileController::class, 'show'])->name('graduate.profile.public');

    // الكتيب الرقمي والفعاليات للخريج
    Route::get('/job-fair/{fair}/catalog', [App\Http\Controllers\GraduateJobFairController::class, 'catalog'])->name('job-fair.catalog');
    Route::post('/job-fair/company/{company}/wishlist', [App\Http\Controllers\GraduateJobFairController::class, 'toggleWishlist'])->name('job-fair.wishlist.toggle');
    Route::post('/job-fair/event/{event}/toggle', [App\Http\Controllers\GraduateJobFairController::class, 'toggleEventRegistration'])->name('job-fair.event.toggle');
});

// إدارة المعرض (أدمن فقط)
Route::middleware(['auth', 'admin'])->prefix('admin/job-fair')->name('job-fair.admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\JobFairController::class, 'index'])->name('index');
    Route::get('/live/{fair}', [App\Http\Controllers\JobFairController::class, 'liveDashboard'])->name('live');
    Route::get('/create', [App\Http\Controllers\JobFairController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\JobFairController::class, 'store'])->name('store');
    Route::get('/{fair}', [App\Http\Controllers\JobFairController::class, 'show'])->name('show');
    Route::get('/{fair}/edit', [App\Http\Controllers\JobFairController::class, 'edit'])->name('edit');
    Route::put('/{fair}', [App\Http\Controllers\JobFairController::class, 'update'])->name('update');
    Route::post('/{fair}/status', [App\Http\Controllers\JobFairController::class, 'updateStatus'])->name('status');
    Route::post('/{fair}/toggle-feature', [App\Http\Controllers\JobFairController::class, 'toggleFeature'])->name('toggle-feature');
    Route::post('/{fair}/companies', [App\Http\Controllers\JobFairController::class, 'addCompany'])->name('add-company');
    Route::delete('/{fair}/companies/{company}', [App\Http\Controllers\JobFairController::class, 'removeCompany'])->name('remove-company');
    Route::get('/{fair}/export', [App\Http\Controllers\JobFairController::class, 'exportRegistrations'])->name('export');
    Route::get('/{fair}/attendance', [App\Http\Controllers\JobFairController::class, 'attendancePage'])->name('attendance');
    Route::post('/{fair}/reset-attendance', [App\Http\Controllers\JobFairController::class, 'resetAttendance'])->name('reset-attendance');
    Route::post('/check-in', [App\Http\Controllers\JobFairController::class, 'checkIn'])->name('check-in');
    
    // إدارة الفعاليات
    Route::get('/{fair}/events', [App\Http\Controllers\JobFairEventController::class, 'index'])->name('events.index');
    Route::post('/{fair}/events', [App\Http\Controllers\JobFairEventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [App\Http\Controllers\JobFairEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [App\Http\Controllers\JobFairEventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}/attendees', [App\Http\Controllers\JobFairEventController::class, 'attendees'])->name('events.attendees');
    Route::get('/events/{event}/export-attendees', [App\Http\Controllers\JobFairEventController::class, 'exportAttendees'])->name('events.export-attendees');

    // إدارة مشاريع التخرج والأرشيف
    Route::get('/{fair}/projects', [App\Http\Controllers\JobFairProjectController::class, 'adminIndex'])->name('projects.index');
    Route::post('/{fair}/projects', [App\Http\Controllers\JobFairProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [App\Http\Controllers\JobFairProjectController::class, 'update'])->name('projects.update');
    Route::patch('/projects/{project}/status', [App\Http\Controllers\JobFairProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::delete('/projects/{project}', [App\Http\Controllers\JobFairProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/{fair}/export-projects', [App\Http\Controllers\JobFairProjectController::class, 'export'])->name('projects.export');

    // إدارة الجهات الراعية (Sponsors)
    Route::post('/{fair}/sponsors', [App\Http\Controllers\JobFairController::class, 'storeSponsor'])->name('sponsors.store');
    Route::put('/sponsors/{sponsor}', [App\Http\Controllers\JobFairController::class, 'updateSponsor'])->name('sponsors.update');
    Route::delete('/sponsors/{sponsor}', [App\Http\Controllers\JobFairController::class, 'destroySponsor'])->name('sponsors.destroy');

    // إدارة الهوية البصرية والأصول الإعلامية للمعرض
    Route::post('/{fair}/brand-identity', [App\Http\Controllers\JobFairController::class, 'updateBrandIdentity'])->name('brand-identity.update');
    Route::delete('/{fair}/brand-identity/{asset}', [App\Http\Controllers\JobFairController::class, 'deleteBrandAsset'])->name('brand-identity.delete');
    Route::get('/{fair}/brand-identity/download/{type}', [App\Http\Controllers\JobFairController::class, 'downloadBrandAsset'])->name('brand-identity.download');

    // إدارة زوار المعرض
    Route::get('/{fair}/visitors', [App\Http\Controllers\JobFairVisitorController::class, 'adminIndex'])->name('visitors.index');
    Route::post('/visitors/{visitor}/check-in', [App\Http\Controllers\JobFairVisitorController::class, 'toggleCheckIn'])->name('visitors.check-in');
    Route::get('/{fair}/visitors/export', [App\Http\Controllers\JobFairVisitorController::class, 'export'])->name('visitors.export');
    Route::delete('/visitors/{visitor}', [App\Http\Controllers\JobFairVisitorController::class, 'destroy'])->name('visitors.destroy');
});

// ==================== 📋 نموذج تنظيم ومتابعة المهام (مشروع سنة 2026) ====================
Route::middleware('auth')->group(function () {
    Route::get('/job-fair/tasks', [App\Http\Controllers\JobFairTaskController::class, 'index'])->name('job-fair.tasks.index');
    Route::post('/job-fair/tasks', [App\Http\Controllers\JobFairTaskController::class, 'store'])->name('job-fair.tasks.store');
    Route::put('/job-fair/tasks/{task}', [App\Http\Controllers\JobFairTaskController::class, 'update'])->name('job-fair.tasks.update');
    Route::delete('/job-fair/tasks/{task}', [App\Http\Controllers\JobFairTaskController::class, 'destroy'])->name('job-fair.tasks.destroy');
    Route::get('/job-fair/tasks/print', [App\Http\Controllers\JobFairTaskController::class, 'print'])->name('job-fair.tasks.print');
    Route::get('/evaluation-followup/tasks', [App\Http\Controllers\JobFairTaskController::class, 'index'])->name('evaluation-followup.tasks.index');
});

// ==================== 🚀 مسار تغذية قاعدة البيانات الحية (Live Seeder & Migration Trigger) ====================
Route::get('/system/run-seeders-now', function () {
    if (request('key') !== 'gts_seed_2026_uot') {
        abort(403, 'Unauthorized');
    }
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    $seedOutput = \Illuminate\Support\Facades\Artisan::output();
    return response()->json([
        'status' => 'success',
        'message' => 'تم بنجاح تشغيل كافة الترحيلات وتغذية قاعدة البيانات وتحديث الصلاحيات بالكامل!',
        'migrations' => $migrateOutput,
        'seeders' => $seedOutput,
    ]);
});

// ==================== 🛡️ توجيه تلقائي ذكي لمنع أخطاء كتابة مسارات ملفات Blade ====================
Route::get('{any}', function ($any) {
    if (str_ends_with($any, '/index.blade.php')) {
        $clean = substr($any, 0, -strlen('/index.blade.php'));
        return redirect('/' . $clean);
    }
    if (str_ends_with($any, '.blade.php')) {
        $clean = substr($any, 0, -strlen('.blade.php'));
        return redirect('/' . $clean);
    }
    abort(404);
})->where('any', '.*\.blade\.php$');

