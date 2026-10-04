<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\Company;
use App\Models\TrainingReport;
use App\Models\Evaluation;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TrainingAttendance;
use App\Models\Certificate;
use Carbon\Carbon;
use App\Services\NotificationService;

class TrainingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ========== 🎯 الدوال الأساسية ==========

    public function index()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.view') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بعرض البرامج التدريبية');
        }

        $trainings    = Training::with(['company', 'coordinator'])->latest()->get();
        $applications = TrainingApplication::with(['user', 'training'])->latest()->get();

        return view('admin.trainings.index', compact('trainings', 'applications'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.create') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بإضافة برامج تدريبية');
        }

        $companies = Company::all();
        $trainers = \App\Models\Trainer::all();
        $categories = Training::getCategories();
        if ($user->role == 'training_coordinator') {
            return view('training-coordinator.trainings.create', compact('companies', 'trainers', 'categories'));
        } else {
            return view('admin.trainings.create', compact('companies', 'trainers', 'categories'));
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.create') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بإضافة برامج تدريبية');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,course,seminar,internship',
            'duration' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'company_id' => 'nullable|exists:companies,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'category' => 'required|string|max:255',
            'instructor_name' => 'required_without:trainer_id|nullable|string|max:255',
            'training_days_of_week' => 'nullable|array',
            'training_days_of_week.*' => 'integer|between:0,6',
        ], [
            'end_date.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء مساوياً لتاريخ البدء أو بعده.',
            'start_date.required' => 'تاريخ بدء البرنامج التدريبي مطلوب.',
            'end_date.required' => 'تاريخ انتهاء البرنامج التدريبي مطلوب.',
            'title.required' => 'اسم البرنامج التدريبي مطلوب.',
            'duration.required' => 'المدة التقديرية مطلوبة.',
            'seats.required' => 'عدد المقاعد المتاحة مطلوب.',
        ]);

        // حماية الإسناد الجماعي الصارمة (Strict Whitelisting)
        $data = $request->only([
            'title', 'description', 'type', 'duration', 'start_date', 'end_date',
            'location', 'seats', 'status', 'company_id', 'trainer_id', 'category', 'instructor_name'
        ]);

        if ($user->role == 'training_coordinator') {
            $data['coordinator_id'] = $user->id;
        } elseif ($user->isAdmin() && $request->filled('coordinator_id')) {
            $data['coordinator_id'] = $request->coordinator_id;
        }

        // استخراج أيام التدريب المحددة في الأسبوع أو افتراض أيام العمل (الأحد - الخميس)
        if ($request->has('training_days_of_week') && is_array($request->input('training_days_of_week')) && count($request->input('training_days_of_week')) > 0) {
            $data['training_days_of_week'] = array_values(array_map('intval', $request->input('training_days_of_week')));
        } else {
            // أيام العمل الرسمية تلقائياً: الأحد(0) إلى الخميس(4) واستثناء الجمعة(5) والسبت(6)
            $data['training_days_of_week'] = [0, 1, 2, 3, 4];
        }

        $training = Training::create($data);

        // تسجيل في سجل الرقابة
        AuditLog::logAction(
            'TRAINING_CREATED',
            'Training',
            $training->id,
            null,
            ['title' => $training->title, 'type' => $training->type, 'seats' => $training->seats]
        );

        // إرسال إشعار
        try {
            $this->notificationService->notifyNewTraining($training);
        } catch (\Exception $e) {
            \Log::error('Failed to send training notification: ' . $e->getMessage());
        }

        if ($user->role == 'training_coordinator') {
            return redirect()->route('training-coordinator.trainings')
                ->with('success', 'تم إنشاء برنامج التدريب بنجاح');
        } else {
            return redirect()->route('admin.trainings')
                ->with('success', 'تم إنشاء برنامج التدريب بنجاح');
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.view') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بعرض تفاصيل التدريب');
        }

        $training = Training::with(['company', 'coordinator'])->findOrFail($id);
        
        $evaluations = Evaluation::with(['evaluator', 'trainerEvaluations.trainer'])
            ->where('training_id', $id)
            ->when($user->role == 'training_coordinator', function($query) {
                return $query->whereHas('evaluator', function ($q) {
                    $q->where('role', 'evaluation_followup');
                });
            })
            ->get();

        $applications = TrainingApplication::with('user')
            ->where('training_id', $id)
            ->latest()
            ->get();
            
        if ($user->role == 'training_coordinator') {
            return view('training-coordinator.trainings.show', compact('training', 'evaluations', 'applications'));
        } else {
            return view('admin.trainings.show', compact('training', 'evaluations', 'applications'));
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.edit') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بتعديل هذا البرنامج التدريبي');
        }

        $training = Training::findOrFail($id);
        $this->authorize('update', $training);
        $companies = Company::all();
        $trainers = \App\Models\Trainer::all();
        $categories = Training::getCategories();

        if ($user->role == 'training_coordinator') {
            return view('training-coordinator.trainings.edit', compact('training', 'companies', 'trainers', 'categories'));
        } else {
            return view('admin.trainings.edit', compact('training', 'companies', 'trainers', 'categories'));
        }
    }

    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);
        $this->authorize('update', $training);

        $user = auth()->user();
        $oldData = $training->only(['title', 'status', 'seats', 'type']);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,course,seminar,internship',
            'duration' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'company_id' => 'nullable|exists:companies,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'category' => 'required|string|max:255',
            'instructor_name' => 'required_without:trainer_id|nullable|string|max:255',
            'training_days_of_week' => 'nullable|array',
            'training_days_of_week.*' => 'integer|between:0,6',
        ], [
            'end_date.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء مساوياً لتاريخ البدء أو بعده.',
            'start_date.required' => 'تاريخ بدء البرنامج التدريبي مطلوب.',
            'end_date.required' => 'تاريخ انتهاء البرنامج التدريبي مطلوب.',
            'title.required' => 'اسم البرنامج التدريبي مطلوب.',
            'duration.required' => 'المدة التقديرية مطلوبة.',
            'seats.required' => 'عدد المقاعد المتاحة مطلوب.',
        ]);

        // حماية الإسناد الجماعي الصارمة (Strict Whitelisting)
        $data = $request->only([
            'title', 'description', 'type', 'duration', 'start_date', 'end_date',
            'location', 'seats', 'status', 'company_id', 'trainer_id', 'category', 'instructor_name'
        ]);
        if ($user->isAdmin() && $request->filled('coordinator_id')) {
            $data['coordinator_id'] = $request->coordinator_id;
        }

        // تحديث أيام التدريب المعتمدة
        if ($request->has('training_days_of_week') && is_array($request->input('training_days_of_week')) && count($request->input('training_days_of_week')) > 0) {
            $data['training_days_of_week'] = array_values(array_map('intval', $request->input('training_days_of_week')));
        } else {
            // أيام العمل الرسمية كخيار افتراضي عند الإلغاء
            $data['training_days_of_week'] = [0, 1, 2, 3, 4];
        }

        $training->update($data);

        // ======================================================
        // مزامنة الشهادات الصادرة مع بيانات التدريب المحدثة
        // يتم تحديث: اسم المدرب، عنوان البرنامج، بيانات الشركة، التواريخ
        // لا يتغير: رمز الشهادة، اسم المستلم، تاريخ الإصدار الأصلي
        // ======================================================
        $training->refresh()->load('trainer', 'company'); // إعادة تحميل البيانات والعلاقات المرتبطة
        $updatedInstructor = $training->trainer?->name ?? $training->instructor_name ?? 'مكتب تدريب وتأهيل الخريجين';

        Certificate::where('training_id', $training->id)
            ->where('status', 'valid')
            ->update([
                'instructor_name' => $updatedInstructor,
                'title'           => $training->title,
                'company_id'      => $training->company_id,
                'company_name'    => $training->company?->name,
                'company_logo'    => $training->company?->logo_path ?? $training->company?->logo,
                'has_company_collaboration' => (bool) $training->company_id,
                'start_date'      => $training->start_date,
                'end_date'        => $training->end_date,
            ]);

        AuditLog::logAction(
            'TRAINING_UPDATED',
            'Training',
            $training->id,
            $oldData,
            $training->only(['title', 'status', 'seats', 'type'])
        );

        if ($user->role == 'training_coordinator') {
            return redirect()->route('training-coordinator.trainings')
                ->with('success', 'تم تحديث برنامج التدريب بنجاح');
        } else {
            return redirect()->route('admin.trainings')
                ->with('success', 'تم تحديث برنامج التدريب بنجاح');
        }
    }

    public function destroy($id)
    {
        $training = Training::findOrFail($id);
        $this->authorize('delete', $training);

        $user = auth()->user();
        $snapshot = ['title' => $training->title];
        $training->delete();

        AuditLog::logAction(
            'TRAINING_DELETED',
            'Training',
            $id,
            $snapshot,
            null
        );

        if ($user->role == 'training_coordinator') {
            return redirect()->route('training-coordinator.trainings')
                ->with('success', 'تم حذف برنامج التدريب بنجاح');
        } else {
            return redirect()->route('admin.trainings')
                ->with('success', 'تم حذف برنامج التدريب بنجاح');
        }
    }

    // ========== 📊 لوحة تحكم منسق التدريب ==========

    public function coordinatorDashboard()
    {
        $stats = [
            'totalTrainings' => Training::count(),
            'activeTrainings' => Training::where('status', 'active')->count(),
            'inactiveTrainings' => Training::where('status', 'inactive')->count(),
            'completedTrainings' => Training::where('status', 'completed')->count(),
            'totalApplications' => TrainingApplication::count(),
            'pendingApplications' => TrainingApplication::where('status', 'pending')->count(),
            'approvedApplications' => TrainingApplication::where('status', 'approved')->count(),
            'rejectedApplications' => TrainingApplication::where('status', 'rejected')->count(),
            'totalTrainees' => TrainingApplication::where('status', 'approved')->count(),
        ];

        $recentApplications = TrainingApplication::with(['user', 'training'])
            ->latest()
            ->take(5)
            ->get();

        $recentTrainings = Training::with('company')
            ->latest()
            ->take(5)
            ->get();

        return view('training-coordinator.dashboard', compact('stats', 'recentApplications', 'recentTrainings'));
    }

    public function coordinatorTrainings()
    {
        $trainings = Training::with(['company', 'coordinator'])
            ->latest()
            ->get();

        return view('training-coordinator.trainings.index', compact('trainings'));
    }

    public function availableTrainings()
    {
        $trainings = Training::where('status', 'active')
            ->latest()
            ->get();

        $myApplications = TrainingApplication::where('user_id', auth()->id())
            ->get()
            ->keyBy('training_id');

        return view('graduate.trainings.index', compact('trainings', 'myApplications'));
    }

    // ========== 📝 إدارة الطلبات ==========

    public function coordinatorApplications()
    {
        $applications = TrainingApplication::with(['user', 'training.coordinator'])
            ->latest()
            ->get();

        $pendingCount = $applications->where('status', 'pending')->count();

        return view('training-coordinator.applications.index', compact('applications', 'pendingCount'));
    }

    public function approveApplication(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $application = TrainingApplication::with(['training.company', 'user'])->findOrFail($id);
        if ($user->role === 'training_coordinator' && (!$application->training || $application->training->coordinator_id !== $user->id)) {
            abort(403, 'غير مصرح لك بقبول طلبات لبرنامج تدريبي لا تشرف عليه.');
        }

        $application->update(['status' => 'approved']);

        // Send notification to the graduate with email and details
        try {
            $this->notificationService->sendToUser(
                $application->user,
                'تم قبول طلب التدريب',
                "تم قبول طلبك للتسجيل في برنامج التدريب: {$application->training->title}. يمكنك الآن البدء في التدريب.",
                'success',
                [
                    'model_type' => 'App\Models\TrainingApplication',
                    'model_id' => $application->id,
                    'send_email' => true,
                    'data' => [
                        'details' => [
                            'البرنامج التدريبي' => $application->training->title,
                            'الشركة المقدمة' => $application->training->company->name ?? 'غير محدد',
                            'الموقع' => $application->training->location,
                            'تاريخ البدء' => $application->training->start_date,
                            'المدة' => $application->training->duration,
                        ]
                    ]
                ]
            );
        } catch (\Exception $e) {}

        return redirect()->back()->with('success', 'تم الموافقة على طلب التدريب بنجاح');
    }

    /**
     * قبول طلبات التدريب دفعة واحدة (للمنسق)
     */
    public function bulkApproveApplications(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        $query = TrainingApplication::with(['user', 'training.company'])
            ->whereIn('id', $ids)
            ->where('status', 'pending');

        if ($user->role === 'training_coordinator') {
            $query->whereHas('training', function ($q) use ($user) {
                $q->where('coordinator_id', $user->id);
            });
        }

        $applications = $query->get();

        foreach ($applications as $application) {
            $application->update(['status' => 'approved']);
            try {
                $this->notificationService->sendToUser(
                    $application->user,
                    'تم قبول طلب التدريب',
                    "تم قبول طلبك للتسجيل في برنامج التدريب: {$application->training->title}.",
                    'success',
                    [
                        'model_type' => 'App\Models\TrainingApplication',
                        'model_id'   => $application->id,
                        'send_email' => true,
                        'data' => [
                            'details' => [
                                'البرنامج التدريبي' => $application->training->title,
                                'الشركة المقدمة'    => $application->training->company->name ?? 'غير محدد',
                                'الموقع'           => $application->training->location,
                                'تاريخ البدء'      => $application->training->start_date,
                                'المدة'            => $application->training->duration,
                            ]
                        ]
                    ]
                );
            } catch (\Exception $e) {}
        }

        return redirect()->back()->with('success', 'تم الموافقة على ' . $applications->count() . ' طلب(ات) بنجاح');
    }

    public function bulkRejectApplications(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        $query = TrainingApplication::with(['user', 'training'])
            ->whereIn('id', $ids);

        if ($user->role === 'training_coordinator') {
            $query->whereHas('training', function ($q) use ($user) {
                $q->where('coordinator_id', $user->id);
            });
        }

        $applications = $query->get();

        foreach ($applications as $application) {
            $application->update(['status' => 'rejected']);
            try {
                $this->notificationService->sendToUser(
                    $application->user,
                    'تم رفض طلب التدريب',
                    "نأسف لإبلاغك بأنه تم رفض طلبك للتسجيل في برنامج التدريب: {$application->training->title}.",
                    'warning',
                    [
                        'model_type' => 'App\Models\TrainingApplication',
                        'model_id'   => $application->id,
                        'send_email' => true
                    ]
                );
            } catch (\Exception $e) {}
        }

        return redirect()->back()->with('success', 'تم رفض ' . $applications->count() . ' طلب(ات) بنجاح');
    }

    public function bulkDeleteApplications(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        $query = TrainingApplication::whereIn('id', $ids);

        if ($user->role === 'training_coordinator') {
            $query->whereHas('training', function ($q) use ($user) {
                $q->where('coordinator_id', $user->id);
            });
        }

        $deletedCount = $query->delete();

        return redirect()->back()->with('success', 'تم إلغاء وحذف ' . $deletedCount . ' طلب(ات) بنجاح');
    }

    public function rejectApplication(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $application = TrainingApplication::with(['training', 'user'])->findOrFail($id);
        if ($user->role === 'training_coordinator' && (!$application->training || $application->training->coordinator_id !== $user->id)) {
            abort(403, 'غير مصرح لك برفض طلبات لبرنامج تدريبي لا تشرف عليه.');
        }

        $application->update(['status' => 'rejected']);

        // Send notification to the graduate with email
        try {
            $this->notificationService->sendToUser(
                $application->user,
                'تم رفض طلب التدريب',
                "نأسف لإبلاغك بأنه تم رفض طلبك للتسجيل في برنامج التدريب: {$application->training->title}. يمكنك التقديم على برامج تدريب أخرى.",
                'warning',
                [
                    'model_type' => 'App\Models\TrainingApplication',
                    'model_id' => $application->id,
                    'send_email' => true
                ]
            );
        } catch (\Exception $e) {}

        return redirect()->back()->with('success', 'تم رفض طلب التدريب بنجاح');
    }

    public function pendingApplication(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $application = TrainingApplication::with('training')->findOrFail($id);
        if ($user->role === 'training_coordinator' && (!$application->training || $application->training->coordinator_id !== $user->id)) {
            abort(403, 'غير مصرح لك بتعديل حالة طلبات لبرنامج تدريبي لا تشرف عليه.');
        }

        $application->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'تم إعادة الطلب إلى قيد المراجعة');
    }

    public function destroyApplication($id)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator')) {
            abort(403, 'غير مصرح لك بإدارة طلبات التدريب.');
        }

        $application = TrainingApplication::with('training')->findOrFail($id);
        if ($user->role === 'training_coordinator' && (!$application->training || $application->training->coordinator_id !== $user->id)) {
            abort(403, 'غير مصرح لك بحذف طلب تدريب لبرنامج لا تشرف عليه.');
        }

        $application->delete();

        return redirect()->back()->with('success', 'تم حذف طلب التدريب بنجاح');
    }

    // ========== 📅 التقويم ==========

    // ========== 📅 التقويم المعتمد ==========

    public function calendar(Request $request)
    {
        try {
            $month = (int) $request->input('month', Carbon::now()->month);
            $year  = (int) $request->input('year', Carbon::now()->year);

            if ($month < 1) {
                $month = 12;
                $year--;
            } elseif ($month > 12) {
                $month = 1;
                $year++;
            }

            $year = max(2020, min(2035, $year));

            $startDate = Carbon::create($year, $month, 1);
            $endDate   = $startDate->copy()->endOfMonth();

            // أسماء الأشهر بالعربية
            $arabicMonths = [
                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
            ];
            $monthName = $arabicMonths[$month] ?? $startDate->translatedFormat('F');

            // التنقل للشهر السابق والقادم
            $prevDate  = $startDate->copy()->subMonth();
            $prevMonth = $prevDate->month;
            $prevYear  = $prevDate->year;

            $nextDate  = $startDate->copy()->addMonth();
            $nextMonth = $nextDate->month;
            $nextYear  = $nextDate->year;

            $trainings = Training::with(['company', 'coordinator', 'applications'])->get();

            // شبكة التقويم المعتمدة: 42 يوماً تبدأ من الأحد (Sunday = 0) إلى السبت (Saturday = 6)
            $firstDayOffset = $startDate->dayOfWeek; // 0 = الأحد, 1 = الإثنين, ..., 6 = السبت
            $gridStart      = $startDate->copy()->subDays($firstDayOffset);
            $gridEnd        = $gridStart->copy()->addDays(41); // 6 أسابيع كاملة = 42 يوماً

            $days = [];
            $curr = $gridStart->copy();

            while ($curr <= $gridEnd) {
                $dateStr        = $curr->format('Y-m-d');
                $isCurrentMonth = ($curr->month == $month);
                $isToday        = $curr->isToday();

                $dayEvents = [];
                foreach ($trainings as $training) {
                    $startStr = $training->start_date ? $training->start_date->format('Y-m-d') : null;
                    $endStr   = $training->end_date ? $training->end_date->format('Y-m-d') : null;

                    $isOngoing = ($training->start_date && $training->end_date && $curr->between($training->start_date->startOfDay(), $training->end_date->endOfDay()));

                    // تحقق من أن هذا اليوم ضمن أيام التدريب الأسبوعية المعتمدة
                    if ($isOngoing) {
                        $allowedWeekDays = $training->training_days_of_week;
                        if (is_array($allowedWeekDays) && count($allowedWeekDays) > 0) {
                            if (!in_array($curr->dayOfWeek, array_map('intval', $allowedWeekDays), true)) {
                                continue; // يوم مستثنى: تخطّى التدريب لهذا اليوم
                            }
                        }
                    }

                    $isStart   = ($startStr === $dateStr);
                    $isEnd     = ($endStr === $dateStr && $startStr !== $endStr);

                    $typeColors = [
                        'workshop'   => ['bg' => '#059669', 'prefix' => 'ورشة: '],
                        'course'     => ['bg' => '#0d3882', 'prefix' => 'دورة: '],
                        'internship' => ['bg' => '#1d4ed8', 'prefix' => 'تدريب عملي: '],
                        'seminar'    => ['bg' => '#d97706', 'prefix' => 'ندوة: '],
                    ];
                    $cfg = $typeColors[$training->type] ?? ['bg' => '#0d3882', 'prefix' => ''];

                    if ($isStart) {
                        $fullLabel = $cfg['prefix'] . $training->title;
                        $dayEvents[] = [
                            'training'       => $training,
                            'kind'           => 'start',
                            'label'          => $fullLabel,
                            'short_label'    => Str::limit($fullLabel, 26, '...'),
                            'sub'            => 'انطلاق التدريب',
                            'bg'             => $cfg['bg'],
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => Str::limit($training->location ?? 'غير محدد', 20, '...'),
                        ];
                    } elseif ($isEnd) {
                        $fullLabel = 'ختام: ' . $training->title;
                        $dayEvents[] = [
                            'training'       => $training,
                            'kind'           => 'end',
                            'label'          => $fullLabel,
                            'short_label'    => Str::limit($fullLabel, 26, '...'),
                            'sub'            => 'اختتام التدريب',
                            'bg'             => '#d97706',
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => Str::limit($training->location ?? 'غير محدد', 20, '...'),
                        ];
                    } elseif ($isOngoing) {
                        $dayEvents[] = [
                            'training'       => $training,
                            'kind'           => 'ongoing',
                            'label'          => $training->title,
                            'short_label'    => Str::limit($training->title, 26, '...'),
                            'sub'            => 'جلسة تدريبية',
                            'bg'             => '#0d3882',
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => Str::limit($training->location ?? 'غير محدد', 20, '...'),
                        ];
                    }
                }

                $days[] = [
                    'date'           => $dateStr,
                    'day'            => $curr->day,
                    'isCurrentMonth' => $isCurrentMonth,
                    'isToday'        => $isToday,
                    'events'         => $dayEvents,
                ];

                $curr->addDay();
            }

            $weeks = array_chunk($days, 7);

            $stats = [
                'total'     => $trainings->count(),
                'active'    => $trainings->where('status', 'active')->count(),
                'seats'     => $trainings->sum('seats'),
                'locations' => $trainings->pluck('location')->filter()->unique()->count(),
            ];

            // تحضير أحداث FullCalendar مع الأخذ بعين الاعتبار أيام التدريب الأسبوعية المستثناة
            $typeColors = [
                'workshop'   => ['bg' => '#059669', 'border' => '#047857', 'prefix' => 'ورشة: '],
                'course'     => ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => 'دورة: '],
                'internship' => ['bg' => '#1d4ed8', 'border' => '#1e40af', 'prefix' => 'تدريب عملي: '],
                'seminar'    => ['bg' => '#d97706', 'border' => '#b45309', 'prefix' => 'ندوة: '],
            ];

            // بدلاً من حدث نطاق واحد يغطي كامل الفترة (مما يُظهر الأيام المستثناة)،
            // نُولّد أحداثاً يومية مستقلة لكل يوم تدريب فعلي حسب أيام الأسبوع المعتمدة
            $calendarTrainings = collect();

            foreach ($trainings as $t) {
                $cfg = $typeColors[$t->type] ?? ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => ''];
                $extendedProps = [
                    'type'          => $t->type,
                    'location'      => $t->location ?? 'غير محدد',
                    'rawTitle'      => $t->title,
                    'instructor'    => $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد'),
                    'seats'         => $t->seats ?? '—',
                    'status'        => $t->status,
                    'duration'      => $t->duration ?? '—',
                    'startDate'     => $t->start_date ? $t->start_date->format('Y-m-d') : '—',
                    'endDate'       => $t->end_date ? $t->end_date->format('Y-m-d') : '—',
                    'showUrl'       => route('training-coordinator.trainings.show', $t->id),
                    'attendanceUrl' => route('training-coordinator.trainings.attendance', $t->id),
                ];

                if (!$t->start_date) continue;

                $allowedDays = $t->training_days_of_week;
                $hasSpecificDays = is_array($allowedDays) && count($allowedDays) > 0 && count($allowedDays) < 7;

                if (!$hasSpecificDays) {
                    // لا توجد أيام مستثناة: استخدم حدث نطاق واحد
                    $calendarTrainings->push([
                        'id'              => $t->id,
                        'title'           => $cfg['prefix'] . $t->title,
                        'start'           => $t->start_date->format('Y-m-d'),
                        'end'             => $t->end_date ? $t->end_date->copy()->addDay()->format('Y-m-d') : null,
                        'url'             => route('training-coordinator.trainings.show', $t->id),
                        'backgroundColor' => $cfg['bg'],
                        'borderColor'     => $cfg['border'],
                        'textColor'       => '#ffffff',
                        'extendedProps'   => $extendedProps,
                        'className'       => 'fc-event-custom fc-event-' . $t->type,
                    ]);
                } else {
                    // توجد أيام مستثناة: نُولّد أحداثاً يومية للأيام الفعلية فقط
                    $allowedDays = array_map('intval', $allowedDays);
                    $current = Carbon::parse($t->start_date);
                    $endDate = $t->end_date ? Carbon::parse($t->end_date) : $current->copy();

                    while ($current->lte($endDate)) {
                        if (in_array($current->dayOfWeek, $allowedDays, true)) {
                            $calendarTrainings->push([
                                'id'              => $t->id . '_' . $current->format('Ymd'),
                                'title'           => $cfg['prefix'] . $t->title,
                                'start'           => $current->format('Y-m-d'),
                                'end'             => $current->copy()->addDay()->format('Y-m-d'),
                                'url'             => route('training-coordinator.trainings.show', $t->id),
                                'backgroundColor' => $cfg['bg'],
                                'borderColor'     => $cfg['border'],
                                'textColor'       => '#ffffff',
                                'extendedProps'   => $extendedProps,
                                'className'       => 'fc-event-custom fc-event-' . $t->type,
                            ]);
                        }
                        $current->addDay();
                    }
                }
            }

            return view('training-coordinator.calendar', compact(
                'trainings', 'weeks', 'month', 'year', 'monthName', 'arabicMonths',
                'prevMonth', 'prevYear', 'nextMonth', 'nextYear', 'stats', 'calendarTrainings'
            ));

        } catch (\Exception $e) {
            \Log::error('Training Coordinator Calendar Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('training-coordinator.dashboard')
                ->with('error', 'حدث خطأ في تحميل بيانات التقويم، يرجى المحاولة مرة أخرى لاحقاً.');
        }
    }

    private function generateCalendar($month, $year, $trainings)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        $calendar = [];
        $currentDay = $startDate->copy();

        $firstDayOfWeek = $currentDay->dayOfWeek;
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar[] = ['day' => null, 'trainings' => []];
        }

        while ($currentDay->month == $month) {
            $dayTrainings = $trainings->filter(function ($training) use ($currentDay) {
                return $currentDay->between(Carbon::parse($training->start_date)->startOfDay(), Carbon::parse($training->end_date)->endOfDay());
            });

            $calendar[] = [
                'day' => $currentDay->copy(),
                'trainings' => $dayTrainings
            ];

            $currentDay->addDay();
        }

        return [
            'days' => $days,
            'weeks' => array_chunk($calendar, 7),
            'month_name' => $this->getArabicMonthName($month)
        ];
    }

    private function getArabicMonthName($month)
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        return $months[$month] ?? 'غير معروف';
    }

    // ========== 🎓 طلبات الخريجين ==========

    public function apply(Request $request)
    {
        return $this->submitApplication($request);
    }

    public function showTraining($id)
    {
        $training = Training::with(['company', 'coordinator'])->findOrFail($id);
        return view('graduate.trainings.show', compact('training'));
    }

    public function submitApplication(Request $request, $id = null)
    {
        try {
            $user = Auth::user();
            $trainingId = $id ?? $request->training_id;

            if (!$trainingId) {
                return redirect()->back()->with('error', 'معرف التدريب مطلوب');
            }

            $training = Training::find($trainingId);

            if (!$training) {
                return redirect()->back()->with('error', 'التدريب غير موجود');
            }

            $existingApplication = TrainingApplication::where('user_id', $user->id)
                ->where('training_id', $trainingId)
                ->first();

            if ($existingApplication) {
                return redirect()->back()->with('error', 'لقد قدمت طلباً لهذا التدريب مسبقاً');
            }

            $application = TrainingApplication::create([
                'user_id' => $user->id,
                'training_id' => $trainingId,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            // Send notification to training coordinator and admin
            $this->notificationService->sendToRoles(
                ['training_coordinator', 'admin'],
                'طلب تدريب جديد',
                "تقدم {$user->name} بطلب للتسجيل في برنامج التدريب: {$training->title}",
                'training_application',
                [
                    'model_type' => 'App\Models\TrainingApplication',
                    'model_id' => $application->id
                ]
            );

            return redirect()->back()->with('success', 'تم تقديم طلب التدريب بنجاح، جاري المراجعة');

        } catch (\Exception $e) {
            \Log::error('Submit Application Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء تقديم طلب التدريب، يرجى المحاولة لاحقاً.');
        }
    }

    // ========== 📈 التقارير والمخططات البيانية المتقدمة ==========

    /**
     * عرض التقارير مع مخططات بيانية متقدمة
     */
    public function reports()
    {
        // إحصائيات أساسية
        $basicStats = [
            'totalTrainings' => Training::count(),
            'activeTrainings' => Training::where('status', 'active')->count(),
            'totalApplications' => TrainingApplication::count(),
            'pendingApplications' => TrainingApplication::where('status', 'pending')->count(),
            'approvedApplications' => TrainingApplication::where('status', 'approved')->count(),
        ];

        // مخططات بيانية متقدمة
        $advancedCharts = $this->generateAdvancedCharts();

        // تحليلات متقدمة
        $advancedAnalytics = $this->generateAdvancedAnalytics();

        $reports = TrainingReport::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('training-coordinator.reports', compact(
            'basicStats',
            'advancedCharts',
            'advancedAnalytics',
            'reports'
        ));
    }

    /**
     * إنشاء مخططات بيانية متقدمة
     */
    private function generateAdvancedCharts()
    {
        $typeMap = [
            'summer' => 'تدريب صيفي',
            'semester' => 'تدريب فصلي',
            'coop' => 'تدريب تعاوني',
            'field' => 'تدريب ميداني',
            'remote' => 'عن بُعد',
            'in_person' => 'حضوري',
            'hybrid' => 'مدمج',
            'academic' => 'أكاديمي',
            'vocational' => 'مهني',
        ];

        $statusMap = [
            'active' => 'نشط ومتاح',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'pending' => 'قيد الانتظار',
            'draft' => 'مسودة',
            'upcoming' => 'قادم',
        ];

        $appStatusMap = [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            'withdrawn' => 'منسحب',
        ];

        // 1. مخطط توزيع أنواع التدريبات الفعلي
        $trainingTypesRaw = Training::selectRaw('type, COUNT(*) as count')
            ->whereNotNull('type')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $trainingTypeLabels = [];
        $trainingTypeData = [];
        foreach ($trainingTypesRaw as $type => $count) {
            $trainingTypeLabels[] = $typeMap[$type] ?? $type;
            $trainingTypeData[] = (int) $count;
        }

        // 2. مخطط توزيع حالات التدريبات الفعلي
        $trainingStatusRaw = Training::selectRaw('status, COUNT(*) as count')
            ->whereNotNull('status')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $trainingStatusLabels = [];
        $trainingStatusData = [];
        foreach ($trainingStatusRaw as $status => $count) {
            $trainingStatusLabels[] = $statusMap[$status] ?? $status;
            $trainingStatusData[] = (int) $count;
        }

        // 3. مخطط توزيع طلبات التدريب الفعلي
        $applicationStatusRaw = TrainingApplication::selectRaw('status, COUNT(*) as count')
            ->whereNotNull('status')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $applicationStatusLabels = [];
        $applicationStatusData = [];
        foreach ($applicationStatusRaw as $status => $count) {
            $applicationStatusLabels[] = $appStatusMap[$status] ?? $status;
            $applicationStatusData[] = (int) $count;
        }

        // 4. مخطط التدريبات الشهرية الفعلي
        $monthlyTrainings = Training::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyLabels = [];
        $monthlyData = [];
        foreach ($monthlyTrainings as $training) {
            $monthlyLabels[] = $this->getArabicMonthName($training->month) . ' ' . $training->year;
            $monthlyData[] = (int) $training->count;
        }

        // 5. مخطط نسبة الإشغال الفعلي
        $occupancyRates = [];
        $trainings = Training::withCount(['applications'])->get();
        foreach ($trainings as $training) {
            if ($training->seats > 0) {
                $occupancyRate = ($training->applications_count / $training->seats) * 100;
                $occupancyRates[] = [
                    'training' => $training->title,
                    'rate' => min($occupancyRate, 100)
                ];
            }
        }

        // 6. مخطط توزيع الشركات الفعلي
        $companyDistribution = Training::with('company')
            ->get()
            ->filter(function ($training) {
                return $training->company !== null;
            })
            ->groupBy('company.name')
            ->map(function ($trainings) {
                return $trainings->count();
            })
            ->sortDesc()
            ->take(10)
            ->toArray();

        return [
            'training_types' => [
                'labels' => $trainingTypeLabels,
                'data' => $trainingTypeData,
                'colors' => ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
            ],
            'training_status' => [
                'labels' => $trainingStatusLabels,
                'data' => $trainingStatusData,
                'colors' => ['#10b981', '#6b7280', '#3b82f6', '#f59e0b']
            ],
            'application_status' => [
                'labels' => $applicationStatusLabels,
                'data' => $applicationStatusData,
                'colors' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280']
            ],
            'monthly_trends' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
                'color' => '#8b5cf6'
            ],
            'occupancy_rates' => $occupancyRates,
            'company_distribution' => [
                'labels' => array_keys($companyDistribution),
                'data' => array_values($companyDistribution),
                'colors' => ['#4f46e5', '#7c3aed', '#a855f7', '#c084fc', '#d946ef', '#ec4899', '#f43f5e', '#fb7185', '#fdba74', '#fcd34d']
            ]
        ];
    }

    /**
     * إنشاء تحليلات متقدمة
     */
    private function generateAdvancedAnalytics()
    {
        $totalTrainings = Training::count();
        $totalApplications = TrainingApplication::count();

        // معدل القبول
        $approvalRate = $totalApplications > 0 ?
            (TrainingApplication::where('status', 'approved')->count() / $totalApplications) * 100 : 0;

        // متوسط عدد المتقدمين لكل تدريب
        $avgApplicants = $totalTrainings > 0 ?
            round($totalApplications / $totalTrainings, 1) : 0;

        // نسبة الإشغال الإجمالية
        $totalSeats = Training::sum('seats');
        $totalOccupied = TrainingApplication::where('status', 'approved')->count();
        $overallOccupancy = $totalSeats > 0 ?
            round(($totalOccupied / $totalSeats) * 100, 1) : 0;

        // أكثر التدريبات طلباً
        $popularTrainings = Training::withCount(['applications'])
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($training) {
                return [
                    'name' => $training->title,
                    'applications' => $training->applications_count,
                    'occupancy_rate' => $training->seats > 0 ?
                        round(($training->applications_count / $training->seats) * 100, 1) : 0
                ];
            });

        // تحليل الأداء الزمني
        $performanceByMonth = Training::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'approval_rate' => round($approvalRate, 1),
            'avg_applicants' => $avgApplicants,
            'overall_occupancy' => $overallOccupancy,
            'popular_trainings' => $popularTrainings,
            'performance_trend' => $performanceByMonth,
            'insights' => $this->generateInsights($totalTrainings, $totalApplications, $approvalRate)
        ];
    }

    /**
     * إنشاء استنتاجات ذكية
     */
    private function generateInsights($totalTrainings, $totalApplications, $approvalRate)
    {
        $insights = [];

        if ($totalTrainings == 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'info-circle',
                'title' => 'بداية جديدة',
                'description' => 'يمكنك البدء بإضافة أول برنامج تدريب إلى النظام'
            ];
        }

        if ($approvalRate < 30) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'معدل قبول منخفض',
                'description' => 'معدل قبول الطلبات منخفض. قد تحتاج إلى مراجعة معايير القبول'
            ];
        } elseif ($approvalRate > 80) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'title' => 'معدل قبول ممتاز',
                'description' => 'معدل قبول الطلبات ممتاز. هذا يدل على جودة البرامج التدريبية'
            ];
        }

        if ($totalApplications > 50) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'users',
                'title' => 'شعبية عالية',
                'description' => 'هناك طلب كبير على برامج التدريب. يمكنك التفكير في زيادة العروض'
            ];
        }

        // إضافة استنتاجات إضافية
        $insights[] = [
            'type' => 'info',
            'icon' => 'lightbulb',
            'title' => 'نصيحة تحليلية',
            'description' => 'استخدم المخططات البيانية لتحديد أنماط النجاح وتحسين البرامج المستقبلية'
        ];

        return $insights;
    }

    // ========== 📤 رفع وإدارة التقارير ==========

    public function uploadReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:training_programs,training_applications,student_data,attendance,evaluation',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $file = $request->file('csv_file');
            $fileName = 'report_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('training-reports', $fileName, 'public');

            TrainingReport::create([
                'user_id' => auth()->id(),
                'report_name' => $request->report_type . '_report_' . date('Y-m-d'),
                'report_type' => $request->report_type,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'تم رفع التقرير بنجاح');

        } catch (\Exception $e) {
            \Log::error('Training Report Upload Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفع التقرير. يرجى المحاولة مرة أخرى لاحقاً.');
        }
    }

    public function downloadTemplate($type)
    {
        $templates = [
            'training_programs' => [
                ['title', 'description', 'type', 'duration', 'start_date', 'end_date', 'location', 'seats', 'status', 'company_id']
            ],
            'training_applications' => [
                ['user_id', 'training_id', 'status', 'applied_at']
            ],
            'student_data' => [
                ['name', 'email', 'phone', 'major', 'graduation_year', 'gpa', 'skills']
            ]
        ];

        if (!isset($templates[$type])) {
            return redirect()->back()->with('error', 'نموذج غير متوفر');
        }

        $fileName = $type . '_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($templates, $type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $templates[$type][0]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadReport($id)
    {
        $report = TrainingReport::where('user_id', auth()->id())->findOrFail($id);

        if (!Storage::disk('public')->exists($report->file_path)) {
            return redirect()->back()->with('error', 'الملف غير موجود');
        }

        return response()->download(storage_path('app/public/' . $report->file_path), $report->file_name);
    }

    public function deleteReport($id)
    {
        $report = TrainingReport::where('user_id', auth()->id())->findOrFail($id);

        Storage::disk('public')->delete($report->file_path);
        $report->delete();

        return redirect()->back()->with('success', 'تم حذف التقرير بنجاح');
    }

    /**
     * عرض صفحة الماسح الضوئي لتسجيل حضور الدورة
     */
    public function scanner(Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك باستخدام ماسح الحضور');
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            abort(403, 'غير مصرح لك باستخدام ماسح الحضور لبرنامج تدريبي تابع لمنسق آخر.');
        }

        $todayDate = now()->format('Y-m-d');
        $trainingDays = $training->training_days;
        $totalDays = $training->total_days_count;
        
        $totalApproved = TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();

        $todayAttended = TrainingAttendance::where('training_id', $training->id)
            ->where('date', $todayDate)
            ->count();

        $currentDayInfo = $trainingDays->firstWhere('date', $todayDate) ?? [
            'day_number' => 1,
            'day_name' => 'اليوم',
            'date' => $todayDate
        ];

        $viewData = compact('training', 'todayDate', 'trainingDays', 'totalDays', 'totalApproved', 'todayAttended', 'currentDayInfo');

        if ($user->role === 'admin') {
            return view('admin.trainings.scanner', $viewData);
        }
        return view('training-coordinator.trainings.scanner', $viewData);
    }

    /**
     * معالجة مسح QR وتسجيل الحضور متعدد الأيام
     */
    public function processScan(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتسجيل الحضور'], 403);
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتسجيل الحضور لبرنامج تدريبي تابع لمنسق آخر.'], 403);
        }

        $request->validate([
            'graduate_id' => 'required|integer|exists:users,id',
            'date' => 'nullable|date_format:Y-m-d'
        ]);

        $graduateId = $request->graduate_id;
        $targetDate = $request->input('date', now()->format('Y-m-d'));

        // البحث عن طلب التسجيل (يجب أن يكون مقبولاً)
        $application = TrainingApplication::with('user.graduateData')
            ->where('training_id', $training->id)
            ->where('user_id', $graduateId)
            ->where('status', 'approved')
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false, 
                'message' => 'هذا الخريج غير مسجل أو لم يتم قبول طلبه في هذه الدورة التدريبية.'
            ], 400);
        }

        $studentName = $application->user->name;
        $totalDays = $training->total_days_count;
        $totalApproved = TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();

        // التحقق من تسجيل الحضور في هذا اليوم المحدد
        $existing = TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $graduateId)
            ->where('date', $targetDate)
            ->first();

        $userTotalAttended = TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $graduateId)
            ->count();

        $todayAttendedCount = TrainingAttendance::where('training_id', $training->id)
            ->where('date', $targetDate)
            ->count();

        if ($existing) {
            $timeStr = $existing->attended_at ? $existing->attended_at->format('h:i A') : 'سابقاً';
            return response()->json([
                'success' => true,
                'already_attended' => true,
                'message' => "تم تسجيل حضور الخريج ({$studentName}) مسبقاً لهذا اليوم في تمام الساعة {$timeStr}.",
                'student_name' => $studentName,
                'faculty' => $application->user->graduateData->faculty ?? '---',
                'department' => $application->user->graduateData->department ?? '---',
                'national_id' => $application->user->graduateData->national_id ?? $application->user->graduateData->university_id ?? '---',
                'attended_time' => $timeStr,
                'user_attended_days' => $userTotalAttended,
                'total_days' => $totalDays,
                'today_attended_count' => $todayAttendedCount,
                'total_approved' => $totalApproved
            ]);
        }

        // إنشاء سجل حضور لليوم المحدد
        $attendance = TrainingAttendance::create([
            'training_id' => $training->id,
            'user_id' => $graduateId,
            'training_application_id' => $application->id,
            'date' => $targetDate,
            'attended_at' => now(),
            'recorded_by' => auth()->id(),
            'status' => 'present',
        ]);

        $application->update([
            'attended_at' => now()
        ]);

        $newUserTotalAttended = $userTotalAttended + 1;
        $newTodayAttendedCount = $todayAttendedCount + 1;
        $currentTimeStr = now()->format('h:i A');

        return response()->json([
            'success' => true,
            'already_attended' => false,
            'message' => "تم تسجيل حضور الخريج ({$studentName}) بنجاح (حضور {$newUserTotalAttended} من أصل {$totalDays} أيام).",
            'student_name' => $studentName,
            'faculty' => $application->user->graduateData->faculty ?? '---',
            'department' => $application->user->graduateData->department ?? '---',
            'national_id' => $application->user->graduateData->national_id ?? $application->user->graduateData->university_id ?? '---',
            'attended_time' => $currentTimeStr,
            'user_attended_days' => $newUserTotalAttended,
            'total_days' => $totalDays,
            'today_attended_count' => $newTodayAttendedCount,
            'total_approved' => $totalApproved
        ]);
    }

    /**
     * عرض مصفوفة وجدول الحضور اليومي الشامل للتدريب
     */
    public function attendance(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بإدارة حضور وغياب التدريب');
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            abort(403, 'غير مصرح لك بعرض كشف حضور برنامج تدريبي تابع لمنسق آخر.');
        }

        $training->load(['company', 'coordinator', 'trainer']);
        
        $applications = TrainingApplication::with(['user.graduateData'])
            ->where('training_id', $training->id)
            ->where('status', 'approved')
            ->get();

        $trainingDays = $training->training_days;
        $totalDays = $training->total_days_count;
        $todayDate = now()->format('Y-m-d');

        // جلب جميع سجلات الحضور لهذه الدورة وتجميعها بحسب المستخدم
        $allAttendances = TrainingAttendance::where('training_id', $training->id)
            ->get();

        $attendancesByUser = $allAttendances->groupBy('user_id');

        // إحصائيات الحضور
        $totalApproved = $applications->count();
        $todayAttendedCount = TrainingAttendance::where('training_id', $training->id)
            ->where('date', $todayDate)
            ->where('status', 'present')
            ->count();
        
        $totalPossibleAttendances = $totalApproved * $totalDays;
        $totalActualAttendances = TrainingAttendance::where('training_id', $training->id)
            ->where('status', 'present')
            ->count();
        
        $overallAttendanceRate = $totalPossibleAttendances > 0 
            ? round(($totalActualAttendances / $totalPossibleAttendances) * 100, 1) 
            : 0;

        $isAdmin = auth()->user()->role === 'admin';

        return view('training-coordinator.trainings.attendance', compact(
            'training',
            'applications',
            'trainingDays',
            'totalDays',
            'todayDate',
            'attendancesByUser',
            'totalApproved',
            'todayAttendedCount',
            'overallAttendanceRate',
            'isAdmin'
        ));
    }

    /**
     * تعديل / تبديل حالة حضور خريج يدوياً في تاريخ محدد (AJAX)
     */
    public function toggleAttendance(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتعديل الحضور'], 403);
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتعديل حضور برنامج تدريبي تابع لمنسق آخر.'], 403);
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'nullable|string|in:present,late,excused,absent,toggle'
        ]);

        $userId = $request->user_id;
        $date = $request->date;
        $reqStatus = $request->input('status', 'toggle');

        $application = TrainingApplication::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->firstOrFail();

        $attendance = TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if ($reqStatus === 'toggle') {
            if ($attendance) {
                // إذا كان حاضراً، إزالته (تغييره إلى غائب)
                $attendance->delete();
                $newStatus = 'absent';
                $message = 'تم إلغاء الحضور وجعله غائباً.';
            } else {
                // تسجيله كحاضر
                $attendance = TrainingAttendance::create([
                    'training_id' => $training->id,
                    'user_id' => $userId,
                    'training_application_id' => $application->id,
                    'date' => $date,
                    'attended_at' => now(),
                    'recorded_by' => auth()->id(),
                    'status' => 'present',
                ]);
                $newStatus = 'present';
                $message = 'تم تسجيل الحضور بنجاح.';
            }
        } elseif ($reqStatus === 'absent') {
            if ($attendance) {
                $attendance->delete();
            }
            $newStatus = 'absent';
            $message = 'تم تسجيل الغياب.';
        } else {
            // present, late, excused
            if ($attendance) {
                $attendance->update([
                    'status' => $reqStatus,
                    'attended_at' => $attendance->attended_at ?? now(),
                    'recorded_by' => auth()->id(),
                ]);
            } else {
                $attendance = TrainingAttendance::create([
                    'training_id' => $training->id,
                    'user_id' => $userId,
                    'training_application_id' => $application->id,
                    'date' => $date,
                    'attended_at' => now(),
                    'recorded_by' => auth()->id(),
                    'status' => $reqStatus,
                ]);
            }
            $newStatus = $reqStatus;
            $message = 'تم تحديث الحالة بنجاح.';
        }

        // إعادة حساب إحصائيات هذا الخريج
        $totalDays = $training->total_days_count;
        $userAttendedCount = TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->where('status', 'present')
            ->count();

        $userPct = $totalDays > 0 ? round(($userAttendedCount / $totalDays) * 100) : 0;

        // إعادة حساب إحصائيات اليوم والإجمالي
        $todayDate = now()->format('Y-m-d');
        $totalApproved = TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();
        $todayAttendedCount = TrainingAttendance::where('training_id', $training->id)
            ->where('date', $todayDate)
            ->where('status', 'present')
            ->count();
        $todayPercentage = $totalApproved > 0 ? round(($todayAttendedCount / $totalApproved) * 100) : 0;

        $totalPossibleAttendances = $totalApproved * $totalDays;
        $totalActualAttendances = TrainingAttendance::where('training_id', $training->id)
            ->where('status', 'present')
            ->count();
        $overallAttendanceRate = $totalPossibleAttendances > 0 
            ? round(($totalActualAttendances / $totalPossibleAttendances) * 100, 1) 
            : 0;

        return response()->json([
            'success' => true,
            'message' => $message,
            'new_status' => $newStatus,
            'user_attended_count' => $userAttendedCount,
            'total_days' => $totalDays,
            'user_percentage' => $userPct,
            'attended_time' => isset($attendance) && $attendance->attended_at ? $attendance->attended_at->format('h:i A') : '',
            'today_attended_count' => $todayAttendedCount,
            'today_percentage' => $todayPercentage,
            'overall_attendance_rate' => $overallAttendanceRate,
            'total_approved' => $totalApproved
        ]);
    }

    /**
     * تصدير كشف الحضور بصيغة CSV المتوافقة تماماً مع Microsoft Excel باللغة العربية
     * يدعم تصدير الحاضرين فقط أو الكشف الشامل متضمناً: الاسم، رقم الهاتف، التخصص، الكلية، الرقم الوطني، نسبة الالتزام
     */
    public function exportAttendance(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بتصدير كشف الحضور');
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            abort(403, 'غير مصرح لك بتصدير كشف حضور برنامج تدريبي تابع لمنسق آخر.');
        }

        $attendedOnly = $request->boolean('attended_only') || $request->get('filter') === 'attended';

        // جلب جميع الطلبات المقبولة مع بيانات المستخدم
        $applications = TrainingApplication::with(['user'])
            ->where('training_id', $training->id)
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        $trainingDays = $training->training_days ?? [];
        $totalDays = $training->total_days_count > 0 ? $training->total_days_count : (count($trainingDays) > 0 ? count($trainingDays) : 1);

        $allAttendances = TrainingAttendance::where('training_id', $training->id)
            ->get()
            ->groupBy('user_id');

        // تجهيز بيانات المتدربين مع احتساب الحضور ونسبة الالتزام بدقة
        $rowsData = [];
        foreach ($applications as $app) {
            $trainee = $app->user;
            if (!$trainee) continue;

            $userAttendances = $allAttendances[$trainee->id] ?? collect();
            $userAttendancesByDate = $userAttendances->keyBy(function ($a) {
                return $a->date ? Carbon::parse($a->date)->format('Y-m-d') : '';
            });

            $attendedCount = 0;
            $dayStatuses = [];

            foreach ($trainingDays as $day) {
                $dayDate = $day['date'] ?? '';
                $att = $dayDate ? ($userAttendancesByDate[$dayDate] ?? null) : null;

                if ($att && $att->status === 'present') {
                    $timeStr = $att->attended_at ? ' (' . Carbon::parse($att->attended_at)->format('H:i') . ')' : '';
                    $dayStatuses[] = 'حاضر' . $timeStr;
                    $attendedCount++;
                } elseif ($att && $att->status === 'late') {
                    $timeStr = $att->attended_at ? ' (' . Carbon::parse($att->attended_at)->format('H:i') . ')' : '';
                    $dayStatuses[] = 'متأخر' . $timeStr;
                    $attendedCount++;
                } elseif ($att && $att->status === 'excused') {
                    $dayStatuses[] = 'معذور';
                } else {
                    $dayStatuses[] = 'غائب';
                }
            }

            // فحص إضافي لدعم attended_at المباشر إذا لم تكن هناك سجلات تفصيلية
            if ($attendedCount === 0 && $app->attended_at !== null) {
                $attendedCount = 1;
            }

            // فلترة الطلبة الذين حضروا فقط عند تفعيل خيار الحاضرين فقط
            if ($attendedOnly && $attendedCount === 0) {
                continue;
            }

            $attendanceRate = $totalDays > 0 ? round(($attendedCount / $totalDays) * 100, 1) : 0;

            // تحديد وصف حالة الالتزام
            $commitmentStatus = 'غائب (لم يحضر)';
            if ($attendanceRate >= 100) {
                $commitmentStatus = 'ملتزم بالكامل (100%)';
            } elseif ($attendanceRate >= 80) {
                $commitmentStatus = 'ملتزم ممتاز (مؤهل للشهادة)';
            } elseif ($attendanceRate >= 50) {
                $commitmentStatus = 'التزام متوسط';
            } elseif ($attendanceRate > 0) {
                $commitmentStatus = 'حضور جزئي منخفض';
            }

            // تنسيق رقم الهاتف والرقم الوطني لتظهر كنص كامل في إكسل دون حذف الأصفار أو التحويل العلمي
            $rawPhone = $trainee->phone ?? '';
            $phoneStr = !empty($rawPhone) ? '="' . $rawPhone . '"' : '—';

            $rawNid = $trainee->national_id ?? '';
            $nidStr = !empty($rawNid) ? '="' . $rawNid . '"' : '—';

            $specialization = $trainee->specialization ?: ($trainee->major ?: '—');
            $faculty = $trainee->faculty ?: '—';

            $rowsData[] = [
                'name'              => $trainee->name ?? 'غير معروف',
                'phone'             => $phoneStr,
                'specialization'    => $specialization,
                'faculty'           => $faculty,
                'national_id'       => $nidStr,
                'email'             => $trainee->email ?: '—',
                'attended_count'    => $attendedCount,
                'attended_ratio'    => "{$attendedCount} / {$totalDays}",
                'attendance_rate'   => "{$attendanceRate}%",
                'commitment_status' => $commitmentStatus,
                'day_statuses'      => $dayStatuses,
            ];
        }

        $typeLabel = $attendedOnly ? 'الحاضرين_فقط' : 'الكشف_الشامل';
        $safeTitle = preg_replace('/[^\p{Arabic}\p{L}\p{N}_\-]+/u', '_', $training->title);
        $fileName = 'كشف_حضور_' . $safeTitle . '_' . $typeLabel . '_' . date('Y-m-d') . '.csv';
        $asciiName = 'attendance_' . $training->id . '_' . ($attendedOnly ? 'attended_only' : 'all') . '_' . date('Y-m-d') . '.csv';

        $callback = function () use ($rowsData, $trainingDays, $training, $attendedOnly, $totalDays) {
            $file = fopen('php://output', 'w');
            // إضافة UTF-8 BOM لفتح الملف باللغة العربية مباشرة في Microsoft Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // معلومات الدورة والتقرير في الترويسة
            fputcsv($file, ['البرنامج التدريبي:', $training->title]);
            fputcsv($file, ['الجهة / المنظمة:', $training->company->name ?? 'مكتب تدريب الخريجين']);
            fputcsv($file, ['فترة التدريب:', ($training->start_date ? $training->start_date->format('Y-m-d') : '—') . ' إلى ' . ($training->end_date ? $training->end_date->format('Y-m-d') : '—')]);
            fputcsv($file, ['إجمالي أيام التدريب:', $totalDays . ' يوم']);
            fputcsv($file, ['نوع الكشف المستخرج:', $attendedOnly ? 'الطلبة الذين حضروا فقط (نسبة الالتزام > 0%)' : 'الكشف الشامل لجميع المتدربين المقبولين']);
            fputcsv($file, ['عدد المتدربين في الكشف:', count($rowsData) . ' متدرب']);
            fputcsv($file, ['تاريخ ووقت التصدير:', date('Y-m-d H:i')]);
            fputcsv($file, []); // سطر فاصل

            // عناوين الأعمدة المطلوبة
            $header = [
                '#',
                'اسم الطالب / المتدرب',
                'رقم الهاتف',
                'التخصص',
                'الكلية',
                'الرقم الوطني',
                'البريد الإلكتروني',
                'أيام الحضور الفعلية',
                'إجمالي أيام الدورة',
                'نسبة الالتزام والحضور',
                'حالة الالتزام',
            ];

            foreach ($trainingDays as $day) {
                $header[] = "يوم {$day['day_number']} ({$day['date']})";
            }

            fputcsv($file, $header);

            // صفوف بيانات الطلبة
            foreach ($rowsData as $index => $row) {
                $csvRow = [
                    $index + 1,
                    $row['name'],
                    $row['phone'],
                    $row['specialization'],
                    $row['faculty'],
                    $row['national_id'],
                    $row['email'],
                    $row['attended_count'],
                    $totalDays,
                    $row['attendance_rate'],
                    $row['commitment_status'],
                ];

                foreach ($row['day_statuses'] as $status) {
                    $csvRow[] = $status;
                }

                fputcsv($file, $csvRow);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $asciiName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$asciiName}\"; filename*=UTF-8''" . rawurlencode($fileName),
        ]);
    }

    /**
     * قبول طلبات التدريب دفعة واحدة
     */
    public function bulkAcceptApplications(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.applications') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بالبت في طلبات التدريب');
        }

        if ($user->role === 'training_coordinator' && $training->coordinator_id !== $user->id) {
            abort(403, 'غير مصرح لك بإدارة طلبات برنامج تدريبي تابع لمنسق آخر.');
        }

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        // تحديث حالة الطلبات المحددة إلى "مقبول"
        TrainingApplication::whereIn('id', $ids)
            ->where('training_id', $training->id)
            ->update(['status' => 'approved']);

        // Send notifications (Optional, if notification logic supports bulk or you can loop)
        $applications = TrainingApplication::whereIn('id', $ids)->get();
        foreach($applications as $app) {
            try {
                $this->notificationService->sendToUser(
                    $app->user,
                    'تم قبول طلب التدريب',
                    "تم قبول طلبك للالتحاق ببرنامج: {$training->title}",
                    'application_approved',
                    ['training_id' => $training->id]
                );
            } catch (\Exception $e) {}
        }

        return redirect()->back()->with('success', 'تم قبول ' . count($ids) . ' طلب(ات) بنجاح');
    }

    /**
     * اعتماد وإصدار الشهادات للمتدربين المجتازين بنسبة الحضور المقررة
     */
    public function issueCertificates(Request $request, Training $training)
    {
        $user = auth()->user();
        $isAuthorized = $user->isAdmin() || $user->hasPermission('trainings.attendance') || ($user->role === 'training_coordinator' && ($training->coordinator_id === $user->id || $training->coordinator_id === null));
        if (!$isAuthorized) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك باعتماد وإصدار الشهادات لهذا البرنامج التدريبي.'], 403);
            }
            abort(403, 'غير مصرح لك باعتماد وإصدار الشهادات لهذا البرنامج التدريبي.');
        }

        $minPercentage = (int) $request->input('min_percentage', 75);
        // السماح بـ 0% (الجميع) وحتى 100%، مع رفض القيم السالبة أو أكثر من 100
        if ($minPercentage < 0 || $minPercentage > 100) {
            $minPercentage = 75;
        }

        $selectedUserIds = $request->input('selected_users', []);
        if (is_string($selectedUserIds)) {
            $selectedUserIds = array_filter(explode(',', $selectedUserIds));
        }

        $applications = TrainingApplication::with(['user.graduateData'])
            ->where('training_id', $training->id)
            ->where('status', 'approved')
            ->get();

        // ✅ حساب الحضور مباشرةً من جدول training_attendances (user_id + training_id)
        // بدلاً من الاعتماد على الـ accessor الذي قد يُرجع 0 بسبب training_application_id الفارغ
        $totalDays = max(1, (int) ($training->total_days_count ?? 1));

        $attendanceCounts = TrainingAttendance::where('training_id', $training->id)
            ->whereIn('status', ['present', 'late'])
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as attended'))
            ->groupBy('user_id')
            ->pluck('attended', 'user_id');

        $issuedCount = 0;
        $alreadyIssuedCount = 0;
        $issuedNames = [];

        // حساب ساعات التدريب (إذا لم تكن محددة صراحة، تُحسب كـ 4 ساعات لكل يوم تدريبي)
        $hours = $training->duration ? (int) filter_var($training->duration, FILTER_SANITIZE_NUMBER_INT) : null;
        if (!$hours || $hours <= 0) {
            $hours = $totalDays * 4;
        }

        // نوع الشهادة
        $certType = 'training_attendance';
        if ($training->type === 'internship') {
            $certType = 'cooperative_attendance';
        } elseif ($training->type === 'workshop') {
            $certType = 'workshop_attendance';
        }

        foreach ($applications as $app) {
            $trainee = $app->user;
            if (!$trainee) {
                continue;
            }

            // حساب النسبة من جدول الحضور الفعلي مباشرةً
            $attendedDays = (int) ($attendanceCounts[$trainee->id] ?? 0);
            $pct = (int) round(($attendedDays / $totalDays) * 100);
            $isExplicitlySelected = !empty($selectedUserIds) && in_array($trainee->id, $selectedUserIds);
            $meetsPercentage = $pct >= $minPercentage;

            if (!$isExplicitlySelected && (!empty($selectedUserIds) || !$meetsPercentage)) {
                continue;
            }

            // فحص هل الشهادة صادرة مسبقاً لهذا المتدرب في هذا البرنامج
            $existing = Certificate::where('training_id', $training->id)
                ->where('user_id', $trainee->id)
                ->first();

            if ($existing) {
                $alreadyIssuedCount++;
                continue;
            }

            // إنشاء الشهادة المعتمدة (الحدث التلقائي في الموديل سيرسل الإشعار فوراً)
            Certificate::create([
                'certificate_code' => Certificate::generateCode(),
                'user_id' => $trainee->id,
                'training_id' => $training->id,
                'company_id' => $training->company_id,
                'type' => $certType,
                'title' => $training->title,
                'recipient_name' => $trainee->name,
                'hours' => $hours,
                'issue_date' => now(),
                'start_date' => $training->start_date,
                'end_date' => $training->end_date,
                'instructor_name' => $training->trainer?->name ?? $training->instructor_name ?? 'مكتب تدريب وتأهيل الخريجين',
                'has_company_collaboration' => (bool) $training->company_id,
                'company_name' => $training->company?->name,
                'company_logo' => $training->company?->logo_path ?? $training->company?->logo,
                'status' => 'valid',
                'notes' => "تم اعتماد الشهادة بنسبة حضور {$pct}% من إجمالي {$totalDays} أيام تدريبية.",
            ]);

            $issuedCount++;
            $issuedNames[] = $trainee->name;
        }

        // تسجيل في سجل التدقيق
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'issue_certificates',
                'model_type' => Training::class,
                'model_id' => $training->id,
                'description' => "قام {$user->name} باعتماد وإصدار {$issuedCount} شهادة تدريبية للمجتازين بنسبة حضور {$minPercentage}% فأكثر.",
                'ip_address' => $request->ip(),
            ]);
        } catch (\Throwable $e) {}

        $message = "تم بنجاح اعتماد وإصدار ({$issuedCount}) شهادة تدريبية معتمدة وفق نسبة حضور ({$minPercentage}%+) وإرسال إشعارات التهنئة الفورية للمتدربين.";
        if ($alreadyIssuedCount > 0) {
            $message .= " (كما وُجدت {$alreadyIssuedCount} شهادة صادرة مسبقاً لم يتم تكرارها).";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'issued_count' => $issuedCount,
                'already_issued' => $alreadyIssuedCount,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * تصدير تقرير إكسل لبرامج وورش العمل المنفذة في شهر محدد (مثل شهر 9 - سبتمبر)
     * يشمل: اسم التدريب، نوع البرنامج، المدرب، تاريخ التدريب، عدد الحضور، وطلبة الملتزمين
     */
    public function exportMonthlyReport(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.view') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بتصدير تقارير التدريب.');
        }

        $month = $request->input('month', 9); // افتراضياً شهر 9 (سبتمبر)
        $year = $request->input('year', 2026); // افتراضياً سنة 2026
        $type = $request->input('type', 'all'); // ورش عمل / دورات / الكل
        $commitmentThreshold = (int) $request->input('commitment_rate', 75);
        if ($commitmentThreshold <= 0 || $commitmentThreshold > 100) {
            $commitmentThreshold = 75;
        }
        $reportFormat = $request->input('format', 'summary'); // summary أو detailed

        // بناء الاستعلام
        $query = Training::with(['company', 'coordinator', 'applications.user.graduateData', 'attendances']);

        // فلترة بالنوع
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // فلترة بالسنة
        if ($year && $year !== 'all') {
            $query->where(function ($q) use ($year) {
                $q->whereYear('start_date', $year)
                  ->orWhereYear('end_date', $year);
            });
        }

        // فلترة بالشهر
        if ($month && $month !== 'all') {
            $selectedYear = ($year && $year !== 'all') ? (int) $year : Carbon::now()->year;
            $monthStart = Carbon::create($selectedYear, (int) $month, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $query->where(function ($q) use ($month, $monthStart, $monthEnd) {
                $q->whereMonth('start_date', $month)
                  ->orWhereMonth('end_date', $month)
                  ->orWhere(function ($sub) use ($monthStart, $monthEnd) {
                      $sub->where('start_date', '<=', $monthEnd)
                          ->where('end_date', '>=', $monthStart);
                  });
            });
        }

        $trainings = $query->orderBy('start_date', 'asc')->get();

        // تجميع وتحليل البيانات لكل برنامج
        $reportData = [];
        $totalAttendeesAll = 0;
        $totalCommittedAll = 0;

        foreach ($trainings as $training) {
            $totalDays = max(1, (int) ($training->total_days_count ?? 1));
            $approvedApps = $training->applications->where('status', 'approved');

            // حساب عدد الأيام الفعلية التي حضرها كل طالب مسجل
            $attendanceCounts = TrainingAttendance::where('training_id', $training->id)
                ->whereIn('status', ['present', 'late'])
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(DISTINCT date) as days_attended')
                ->pluck('days_attended', 'user_id');

            $attendeesCount = 0;
            $committedStudents = [];
            $allTraineesDetails = [];

            foreach ($approvedApps as $app) {
                $trainee = $app->user;
                if (!$trainee) continue;

                $daysAttended = (int) ($attendanceCounts[$trainee->id] ?? 0);
                if ($daysAttended === 0 && $app->attended_at !== null) {
                    $daysAttended = 1;
                }

                $attPct = (int) round(($daysAttended / $totalDays) * 100);
                $isCommitted = ($attPct >= $commitmentThreshold);

                if ($daysAttended > 0) {
                    $attendeesCount++;
                }

                if ($isCommitted) {
                    $committedStudents[] = [
                        'id' => $trainee->id,
                        'name' => $trainee->name,
                        'email' => $trainee->email,
                        'phone' => $trainee->phone ?? $trainee->graduateData?->phone ?? '—',
                        'faculty' => $trainee->faculty ?? $trainee->graduateData?->faculty ?? '—',
                        'major' => $trainee->major ?? $trainee->graduateData?->department ?? '—',
                        'attended_days' => $daysAttended,
                        'total_days' => $totalDays,
                        'percentage' => $attPct,
                    ];
                }

                $allTraineesDetails[] = [
                    'id' => $trainee->id,
                    'name' => $trainee->name,
                    'email' => $trainee->email,
                    'phone' => $trainee->phone ?? $trainee->graduateData?->phone ?? '—',
                    'faculty' => $trainee->faculty ?? $trainee->graduateData?->faculty ?? '—',
                    'major' => $trainee->major ?? $trainee->graduateData?->department ?? '—',
                    'graduation_year' => $trainee->graduation_year ?? $trainee->graduateData?->graduation_year ?? '—',
                    'applied_at' => $app->applied_at ? $app->applied_at->format('Y-m-d') : '—',
                    'attended_days' => $daysAttended,
                    'total_days' => $totalDays,
                    'percentage' => $attPct,
                    'is_committed' => $isCommitted,
                ];
            }

            $committedCount = count($committedStudents);
            $commitmentRate = $attendeesCount > 0 ? round(($committedCount / $attendeesCount) * 100, 1) : 0;

            $totalAttendeesAll += $attendeesCount;
            $totalCommittedAll += $committedCount;

            $typeLabel = match($training->type) {
                'workshop' => 'ورشة عمل',
                'course' => 'دورة تدريبية',
                'internship' => 'تدريب عملي / تعاوني',
                default => $training->type ?? 'برنامج تدريبي',
            };

            $statusLabel = match($training->status) {
                'completed' => 'مكتمل',
                'active' => 'نشط',
                'inactive' => 'غير نشط',
                default => $training->status ?? '—',
            };

            $reportData[] = [
                'training_id' => $training->id,
                'title' => $training->title,
                'type_label' => $typeLabel,
                'category' => $training->category ?? 'عام',
                'instructor_name' => $training->instructor_name ?: ($training->coordinator?->name ?? 'مكتب تدريب وتأهيل الخريجين'),
                'start_date' => $training->start_date ? Carbon::parse($training->start_date)->format('Y-m-d') : '—',
                'end_date' => $training->end_date ? Carbon::parse($training->end_date)->format('Y-m-d') : '—',
                'training_days_count' => $totalDays,
                'location' => $training->location ?? 'غير محدد',
                'seats' => $training->seats ?? 'غير محدد',
                'approved_count' => $approvedApps->count(),
                'attendees_count' => $attendeesCount,
                'committed_count' => $committedCount,
                'commitment_rate' => $commitmentRate,
                'committed_students' => $committedStudents,
                'all_trainees' => $allTraineesDetails,
                'status_label' => $statusLabel,
            ];
        }

        // إذا كان الطلب استعراضاً سريعاً (Live Preview AJAX)
        if ($request->has('preview') || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'month' => $month,
                'year' => $year,
                'trainings_count' => count($reportData),
                'total_attendees' => $totalAttendeesAll,
                'total_committed' => $totalCommittedAll,
                'commitment_threshold' => $commitmentThreshold,
                'data' => $reportData,
            ]);
        }

        // إعداد ملف Excel (CSV with UTF-8 BOM)
        $monthName = match((string)$month) {
            '1' => 'يناير',
            '2' => 'فبراير',
            '3' => 'مارس',
            '4' => 'أبريل',
            '5' => 'مايو',
            '6' => 'يونيو',
            '7' => 'يوليو',
            '8' => 'أغسطس',
            '9' => 'سبتمبر_شهر_9',
            '10' => 'أكتوبر',
            '11' => 'نوفمبر',
            '12' => 'ديسمبر',
            default => 'كل_الأشهر',
        };

        $yearSuffix = ($year !== 'all' ? $year : date('Y'));
        $fileName = 'تقرير_تدريبات_وورش_عمل_' . $monthName . '_' . $yearSuffix . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($reportData, $reportFormat, $commitmentThreshold) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Arabic support in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($reportFormat === 'detailed') {
                // تقرير تفصيلي بكل متدرب في كل برنامج
                fputcsv($file, [
                    '#',
                    'اسم البرنامج التدريبي / ورشة العمل',
                    'نوع البرنامج',
                    'التخصص / المجال',
                    'اسم المدرب',
                    'تاريخ التدريب',
                    'اسم الطالب / الخريج',
                    'البريد الإلكتروني',
                    'رقم الهاتف',
                    'الكلية',
                    'التخصص / القسم',
                    'سنة التخرج',
                    'أيام الحضور الفعلية',
                    'إجمالي أيام التدريب',
                    'نسبة الحضور (%)',
                    'حالة الالتزام (' . $commitmentThreshold . '%+)',
                    'تاريخ التسجيل',
                ]);

                $index = 1;
                foreach ($reportData as $item) {
                    foreach ($item['all_trainees'] as $trainee) {
                        fputcsv($file, [
                            $index++,
                            $item['title'],
                            $item['type_label'],
                            $item['category'],
                            $item['instructor_name'],
                            $item['start_date'] . ' إلى ' . $item['end_date'],
                            $trainee['name'],
                            $trainee['email'],
                            $trainee['phone'],
                            $trainee['faculty'],
                            $trainee['major'],
                            $trainee['graduation_year'],
                            $trainee['attended_days'],
                            $trainee['total_days'],
                            $trainee['percentage'] . '%',
                            $trainee['is_committed'] ? 'ملتزم ✅' : 'غير ملتزم ❌',
                            $trainee['applied_at'],
                        ]);
                    }
                }
            } else {
                // تقرير ملخص البرامج مع أعداد وقوائم الملتزمين (المطلوب الرئيسي)
                fputcsv($file, [
                    '#',
                    'اسم التدريب / ورشة العمل',
                    'نوع البرنامج',
                    'التخصص / المجال',
                    'اسم المدرب',
                    'تاريخ البداية',
                    'تاريخ النهاية',
                    'عدد أيام التدريب الفعلية',
                    'المقاعد المتاحة',
                    'إجمالي المسجلين المقبولين',
                    'عدد الحضور الفعلي',
                    'عدد الطلبة الملتزمين (' . $commitmentThreshold . '%+)',
                    'نسبة الالتزام العامة (%)',
                    'أسماء ونسب الطلبة الملتزمين',
                    'حالة البرنامج',
                ]);

                $index = 1;
                foreach ($reportData as $item) {
                    $committedNamesList = collect($item['committed_students'])->map(function ($s) {
                        return $s['name'] . ' (' . $s['percentage'] . '%)';
                    })->implode(' | ');

                    if (empty($committedNamesList)) {
                        $committedNamesList = 'لا يوجد طلبة استوفوا النسبة';
                    }

                    fputcsv($file, [
                        $index++,
                        $item['title'],
                        $item['type_label'],
                        $item['category'],
                        $item['instructor_name'],
                        $item['start_date'],
                        $item['end_date'],
                        $item['training_days_count'],
                        $item['seats'],
                        $item['approved_count'],
                        $item['attendees_count'],
                        $item['committed_count'],
                        $item['commitment_rate'] . '%',
                        $committedNamesList,
                        $item['status_label'],
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * استيراد برامج وورش عمل تدريبية من ملف إكسل / CSV
     */
    public function importTrainings(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.create') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك باستيراد برامج التدريب.');
        }

        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ], [
            'csv_file.required' => 'يرجى اختيار ملف الإكسل أو الـ CSV المراد استيراده',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            // تجاهل الـ BOM إذا كان موجوداً
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            // قراءة سطر العناوين
            $header = fgetcsv($handle, 2000, ',');
            if (!$header) {
                fclose($handle);
                return back()->with('error', 'الملف فارغ أو غير صالح.');
            }

            // تنظيف أسماء الأعمدة وتحويلها إلى أحرف صغيرة
            $header = array_map(function ($h) {
                return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h)));
            }, $header);

            $coordinatorId = $user->role === 'training_coordinator' ? $user->id : (\App\Models\User::where('role', 'training_coordinator')->value('id') ?? $user->id);

            $rowNum = 1;
            while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                $rowNum++;
                if (empty(array_filter($row))) {
                    continue; // تجاهل الأسطر الفارغة
                }

                $data = [];
                foreach ($header as $colIdx => $colName) {
                    $data[$colName] = isset($row[$colIdx]) ? trim($row[$colIdx]) : null;
                }

                $title = $data['title'] ?? $data['اسم_التدريب'] ?? $data['العنوان'] ?? null;
                if (!$title) {
                    $errors[] = "السطر {$rowNum}: اسم التدريب مفقود.";
                    continue;
                }

                // معالجة نوع التدريب
                $rawType = strtolower($data['type'] ?? $data['النوع'] ?? '');
                $type = 'workshop';
                if (str_contains($rawType, 'دورة') || str_contains($title, 'دورة') || str_contains($rawType, 'course')) {
                    $type = 'course';
                } elseif (str_contains($rawType, 'تدريب عملي') || str_contains($rawType, 'تعاوني') || str_contains($rawType, 'internship')) {
                    $type = 'internship';
                }

                // معالجة حالة البرنامج
                $rawStatus = strtolower($data['status'] ?? $data['الحالة'] ?? 'متاح');
                $status = 'active';
                if (str_contains($rawStatus, 'مكتمل') || str_contains($rawStatus, 'completed')) {
                    $status = 'completed';
                } elseif (str_contains($rawStatus, 'غير نشط') || str_contains($rawStatus, 'inactive')) {
                    $status = 'inactive';
                }

                // معالجة التواريخ (يدعم 10/4/2026 و 2026-10-04 وغيرها)
                $startDate = null;
                if (!empty($data['start_date'])) {
                    try {
                        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $startDate = Carbon::createFromFormat('m/d/Y', $data['start_date'])->format('Y-m-d');
                        } catch (\Exception $e2) {
                            $startDate = now()->format('Y-m-d');
                        }
                    }
                }

                $endDate = null;
                if (!empty($data['end_date'])) {
                    try {
                        $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $endDate = Carbon::createFromFormat('m/d/Y', $data['end_date'])->format('Y-m-d');
                        } catch (\Exception $e2) {
                            $endDate = $startDate;
                        }
                    }
                }

                // معالجة الشركة الشريكة
                $companyId = !empty($data['company_id']) ? (int) $data['company_id'] : null;
                if ($companyId && !Company::where('id', $companyId)->exists()) {
                    $companyId = Company::first()?->id;
                }

                // المدرب
                $instructorName = $data['instructor_name'] ?? $data['المدرب'] ?? $data['اسم_المدرب'] ?? 'مكتب تدريب الخريجين';

                // المقاعد
                $seats = !empty($data['seats']) ? (int) $data['seats'] : 25;

                // الموقع
                $location = $data['location'] ?? 'جامعة طرابلس';

                // المدة
                $duration = $data['duration'] ?? '3 أيام';

                // الحفظ في قاعدة البيانات
                Training::updateOrCreate(
                    ['title' => $title],
                    [
                        'description' => $data['description'] ?? "برنامج تدريبي في: {$title}",
                        'type' => $type,
                        'duration' => $duration,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'location' => $location,
                        'seats' => $seats,
                        'status' => $status,
                        'company_id' => $companyId,
                        'coordinator_id' => $coordinatorId,
                        'instructor_name' => $instructorName,
                        'category' => $this->inferCategory($title),
                        'training_days_of_week' => [0, 1, 2, 3, 4],
                    ]
                );

                $imported++;
            }

            fclose($handle);
        }

        $msg = "تم استيراد ({$imported}) برنامج تدريبي بنجاح إلى قاعدة البيانات!";
        if (!empty($errors)) {
            $msg .= ' ملاحظات: ' . implode(' | ', $errors);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * استنتاج التخصص تلقائياً من عنوان التدريب
     */
    private function inferCategory(string $title): string
    {
        if (str_contains($title, 'بيانات') || str_contains($title, 'أمن') || str_contains($title, 'سيبراني') || str_contains($title, 'ذكاء') || str_contains($title, 'برمج')) {
            return 'تقنية المعلومات والتحول الرقمي';
        } elseif (str_contains($title, 'تسويق') || str_contains($title, 'مبيعات') || str_contains($title, 'إعلام')) {
            return 'التسويق والمبيعات';
        } elseif (str_contains($title, 'مشاريع') || str_contains($title, 'إدارة') || str_contains($title, 'قيادة')) {
            return 'الإدارة والقيادة';
        } elseif (str_contains($title, 'سوق العمل') || str_contains($title, 'مهارات') || str_contains($title, 'تنمية') || str_contains($title, 'ذاتية')) {
            return 'التنمية البشرية والمهارات الشخصية';
        } elseif (str_contains($title, 'محاسب') || str_contains($title, 'مالي') || str_contains($title, 'اقتصاد')) {
            return 'الاقتصاد والمالية والمحاسبة';
        }
        return 'التنمية البشرية والمهارات الشخصية';
    }
}
