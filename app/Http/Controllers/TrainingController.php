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
        $categories = ['برمجة', 'تصميم', 'شبكات', 'إدارة', 'لغات', 'أخرى'];
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
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'company_id' => 'nullable|exists:companies,id',
            'trainer_id' => 'nullable|exists:trainers,id',
            'category' => 'required|string|max:255',
            'instructor_name' => 'required|string|max:255',
        ]);

        $data = $request->all();

        if ($user->role == 'training_coordinator') {
            $data['coordinator_id'] = $user->id;
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
                ->with('success', 'تم إضافة برنامج التدريب بنجاح');
        } else {
            return redirect()->route('admin.trainings')
                ->with('success', 'تم إضافة برنامج التدريب بنجاح');
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

        $applications = \App\Models\TrainingApplication::with('user')
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
        $companies = Company::all();
        $trainers = \App\Models\Trainer::all();
        $categories = ['برمجة', 'تصميم', 'شبكات', 'إدارة', 'لغات', 'أخرى'];

        if ($user->role == 'training_coordinator') {
            return view('training-coordinator.trainings.edit', compact('training', 'companies', 'trainers', 'categories'));
        } else {
            return view('admin.trainings.edit', compact('training', 'companies', 'trainers', 'categories'));
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.edit') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بتعديل هذا البرنامج التدريبي');
        }

        $training = Training::findOrFail($id);
        $oldData = $training->only(['title', 'status', 'seats', 'type']);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:workshop,course,seminar,internship',
            'duration' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
            'company_id' => 'nullable|exists:companies,id',
            'trainer_id' => 'nullable|exists:trainers,id',
        ]);

        $training->update($request->all());

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
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.delete') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بحذف هذا البرنامج التدريبي');
        }

        $training = Training::findOrFail($id);
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
        $application = TrainingApplication::with(['training.company', 'user'])->findOrFail($id);
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
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        $applications = TrainingApplication::with(['user', 'training.company'])
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->get();

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

        return redirect()->back()->with('success', 'تم الموافقة على ' . count($ids) . ' طلب(ات) بنجاح');
    }

    public function bulkRejectApplications(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        $applications = TrainingApplication::with(['user', 'training'])
            ->whereIn('id', $ids)
            ->get();

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

        return redirect()->back()->with('success', 'تم رفض ' . count($ids) . ' طلب(ات) بنجاح');
    }

    public function bulkDeleteApplications(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;
        TrainingApplication::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'تم إلغاء وحذف ' . count($ids) . ' طلب(ات) بنجاح');
    }

    public function rejectApplication(Request $request, $id)
    {
        $application = TrainingApplication::with(['training', 'user'])->findOrFail($id);
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
        $application = TrainingApplication::with('training')->findOrFail($id);
        $application->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'تم إعادة الطلب إلى قيد المراجعة');
    }

    public function destroyApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
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

                    $isStart   = ($startStr === $dateStr);
                    $isEnd     = ($endStr === $dateStr && $startStr !== $endStr);
                    $isOngoing = ($training->start_date && $training->end_date && $curr->between($training->start_date->startOfDay(), $training->end_date->endOfDay()));

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
                            'short_label'    => \Illuminate\Support\Str::limit($fullLabel, 26, '...'),
                            'sub'            => 'انطلاق التدريب',
                            'bg'             => $cfg['bg'],
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => \Illuminate\Support\Str::limit($training->location ?? 'غير محدد', 20, '...'),
                        ];
                    } elseif ($isEnd) {
                        $fullLabel = 'ختام: ' . $training->title;
                        $dayEvents[] = [
                            'training'       => $training,
                            'kind'           => 'end',
                            'label'          => $fullLabel,
                            'short_label'    => \Illuminate\Support\Str::limit($fullLabel, 26, '...'),
                            'sub'            => 'اختتام التدريب',
                            'bg'             => '#d97706',
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => \Illuminate\Support\Str::limit($training->location ?? 'غير محدد', 20, '...'),
                        ];
                    } elseif ($isOngoing) {
                        $dayEvents[] = [
                            'training'       => $training,
                            'kind'           => 'ongoing',
                            'label'          => $training->title,
                            'short_label'    => \Illuminate\Support\Str::limit($training->title, 26, '...'),
                            'sub'            => 'جلسة تدريبية',
                            'bg'             => '#0d3882',
                            'location'       => $training->location ?? 'غير محدد',
                            'short_location' => \Illuminate\Support\Str::limit($training->location ?? 'غير محدد', 20, '...'),
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

            return view('training-coordinator.calendar', compact(
                'trainings', 'weeks', 'month', 'year', 'monthName', 'arabicMonths',
                'prevMonth', 'prevYear', 'nextMonth', 'nextYear', 'stats'
            ));

        } catch (\Exception $e) {
            return redirect()->route('training-coordinator.dashboard')
                ->with('error', 'حدث خطأ في تحميل التقويم: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفع التقرير: ' . $e->getMessage());
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

        return Storage::disk('public')->download($report->file_path, $report->file_name);
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

        $todayDate = now()->format('Y-m-d');
        $trainingDays = $training->training_days;
        $totalDays = $training->total_days_count;
        
        $totalApproved = \App\Models\TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();

        $todayAttended = \App\Models\TrainingAttendance::where('training_id', $training->id)
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

        $request->validate([
            'graduate_id' => 'required|integer|exists:users,id',
            'date' => 'nullable|date_format:Y-m-d'
        ]);

        $graduateId = $request->graduate_id;
        $targetDate = $request->input('date', now()->format('Y-m-d'));

        // البحث عن طلب التسجيل (يجب أن يكون مقبولاً)
        $application = \App\Models\TrainingApplication::with('user.graduateData')
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
        $totalApproved = \App\Models\TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();

        // التحقق من تسجيل الحضور في هذا اليوم المحدد
        $existing = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $graduateId)
            ->where('date', $targetDate)
            ->first();

        $userTotalAttended = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $graduateId)
            ->count();

        $todayAttendedCount = \App\Models\TrainingAttendance::where('training_id', $training->id)
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
        $attendance = \App\Models\TrainingAttendance::create([
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

        $training->load(['company', 'coordinator', 'trainer']);
        
        $applications = \App\Models\TrainingApplication::with(['user.graduateData'])
            ->where('training_id', $training->id)
            ->where('status', 'approved')
            ->get();

        $trainingDays = $training->training_days;
        $totalDays = $training->total_days_count;
        $todayDate = now()->format('Y-m-d');

        // جلب جميع سجلات الحضور لهذه الدورة وتجميعها بحسب المستخدم
        $allAttendances = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->get();

        $attendancesByUser = $allAttendances->groupBy('user_id');

        // إحصائيات الحضور
        $totalApproved = $applications->count();
        $todayAttendedCount = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->whereDate('date', $todayDate)
            ->where('status', 'present')
            ->count();
        
        $totalPossibleAttendances = $totalApproved * $totalDays;
        $totalActualAttendances = \App\Models\TrainingAttendance::where('training_id', $training->id)
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

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'nullable|string|in:present,late,excused,absent,toggle'
        ]);

        $userId = $request->user_id;
        $date = $request->date;
        $reqStatus = $request->input('status', 'toggle');

        $application = \App\Models\TrainingApplication::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->firstOrFail();

        $attendance = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if ($reqStatus === 'toggle') {
            if ($attendance) {
                // إذا كان حاضراً، إزالته (تغييره إلى غائب)
                $attendance->delete();
                $newStatus = 'absent';
                $message = 'تم إلغاء الحضور وجعله غائباً.';
            } else {
                // تسجيله كحاضر
                $attendance = \App\Models\TrainingAttendance::create([
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
                $attendance = \App\Models\TrainingAttendance::create([
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
        $userAttendedCount = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->where('user_id', $userId)
            ->where('status', 'present')
            ->count();

        $userPct = $totalDays > 0 ? round(($userAttendedCount / $totalDays) * 100) : 0;

        // إعادة حساب إحصائيات اليوم والإجمالي
        $todayDate = now()->format('Y-m-d');
        $totalApproved = \App\Models\TrainingApplication::where('training_id', $training->id)
            ->where('status', 'approved')
            ->count();
        $todayAttendedCount = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->whereDate('date', $todayDate)
            ->where('status', 'present')
            ->count();
        $todayPercentage = $totalApproved > 0 ? round(($todayAttendedCount / $totalApproved) * 100) : 0;

        $totalPossibleAttendances = $totalApproved * $totalDays;
        $totalActualAttendances = \App\Models\TrainingAttendance::where('training_id', $training->id)
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
     * تصدير مصفوفة الحضور بصيغة CSV المتوافقة مع Excel باللغة العربية
     */
    public function exportAttendance(Request $request, Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('trainings.attendance') && $user->role !== 'training_coordinator') {
            abort(403, 'غير مصرح لك بتصدير كشف الحضور');
        }

        $applications = \App\Models\TrainingApplication::with(['user.graduateData'])
            ->where('training_id', $training->id)
            ->where('status', 'approved')
            ->get();

        $trainingDays = $training->training_days;
        $totalDays = $training->total_days_count;

        $allAttendances = \App\Models\TrainingAttendance::where('training_id', $training->id)
            ->get()
            ->groupBy('user_id');

        $fileName = 'attendance_' . Str::slug($training->title) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($applications, $trainingDays, $allAttendances, $totalDays) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Arabic support in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $header = ['#', 'اسم الخريج', 'الرقم الجامعي/الوطني', 'الكلية', 'القسم'];
            foreach ($trainingDays as $day) {
                $header[] = "يوم {$day['day_number']} ({$day['date']})";
            }
            $header[] = 'أيام الحضور';
            $header[] = 'نسبة الحضور (%)';

            fputcsv($file, $header);

            // Rows
            foreach ($applications as $index => $app) {
                $user = $app->user;
                $userAttendances = $allAttendances[$user->id] ?? collect();
                $userAttendancesByDate = $userAttendances->keyBy(fn($a) => $a->date->format('Y-m-d'));

                $row = [
                    $index + 1,
                    $user->name,
                    $user->graduateData->national_id ?? $user->graduateData->university_id ?? '---',
                    $user->graduateData->faculty ?? '---',
                    $user->graduateData->department ?? '---',
                ];

                $attendedCount = 0;
                foreach ($trainingDays as $day) {
                    $att = $userAttendancesByDate[$day['date']] ?? null;
                    if ($att && $att->status === 'present') {
                        $row[] = 'حاضر (' . ($att->attended_at ? $att->attended_at->format('H:i') : '') . ')';
                        $attendedCount++;
                    } elseif ($att && $att->status === 'late') {
                        $row[] = 'متأخر';
                        $attendedCount++;
                    } elseif ($att && $att->status === 'excused') {
                        $row[] = 'معذور';
                    } else {
                        $row[] = 'غائب';
                    }
                }

                $row[] = "{$attendedCount} / {$totalDays}";
                $row[] = ($totalDays > 0 ? round(($attendedCount / $totalDays) * 100, 1) : 0) . '%';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:training_applications,id'
        ]);

        $ids = $request->application_ids;

        // تحديث حالة الطلبات المحددة إلى "مقبول"
        \App\Models\TrainingApplication::whereIn('id', $ids)
            ->where('training_id', $training->id)
            ->update(['status' => 'approved']);

        // Send notifications (Optional, if notification logic supports bulk or you can loop)
        $applications = \App\Models\TrainingApplication::whereIn('id', $ids)->get();
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
}
