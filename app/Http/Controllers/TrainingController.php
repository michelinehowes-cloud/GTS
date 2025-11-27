<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\Company;
use App\Models\TrainingReport;
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
        $trainings = Training::with('company')->latest()->get();
        $applications = TrainingApplication::with(['user', 'training'])->latest()->get();

        return view('admin.trainings.index', compact('trainings', 'applications'));
    }

    public function create()
    {
        $companies = Company::all();
        $categories = ['برمجة', 'تصميم', 'شبكات', 'إدارة', 'لغات', 'أخرى']; // مثال للفئات

        if (auth()->user()->role == 'training_coordinator') {
            return view('training-coordinator.trainings.create', compact('companies', 'categories'));
        } else {
            return view('admin.trainings.create', compact('companies', 'categories'));
        }
    }

    public function store(Request $request)
    {
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
            'category' => 'required|string|max:255',
            'instructor_name' => 'required|string|max:255',
        ]);

        $data = $request->all();

        if (auth()->user()->role == 'training_coordinator') {
            $data['coordinator_id'] = auth()->id();
        }

        $training = Training::create($data);

        // إرسال إشعار
        try {
            $this->notificationService->notifyNewTraining($training);
        } catch (\Exception $e) {
            \Log::error('Failed to send training notification: ' . $e->getMessage());
        }

        if (auth()->user()->role == 'training_coordinator') {
            return redirect()->route('training-coordinator.trainings')
                ->with('success', 'تم إضافة برنامج التدريب بنجاح');
        } else {
            return redirect()->route('admin.trainings')
                ->with('success', 'تم إضافة برنامج التدريب بنجاح');
        }
    }

    public function show($id)
    {
        $training = Training::with('company')->findOrFail($id);

        if (auth()->user()->role == 'training_coordinator') {
            return view('training-coordinator.trainings.show', compact('training'));
        } else {
            return view('admin.trainings.show', compact('training'));
        }
    }

    public function edit($id)
    {
        $training = Training::findOrFail($id);
        $companies = Company::all();
        $categories = ['برمجة', 'تصميم', 'شبكات', 'إدارة', 'لغات', 'أخرى']; // مثال للفئات

        if (auth()->user()->role == 'training_coordinator') {
            return view('training-coordinator.trainings.edit', compact('training', 'companies', 'categories'));
        } else {
            return view('admin.trainings.edit', compact('training', 'companies', 'categories'));
        }
    }

    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);

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
        ]);

        $training->update($request->all());

        if (auth()->user()->role == 'training_coordinator') {
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
        $training->delete();

        if (auth()->user()->role == 'training_coordinator') {
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
            ->withCount([
                'applications',
                'applications as pending_applications_count' => function ($query) {
                    $query->where('status', 'pending');
                }
            ])
            ->latest()
            ->get();

        $myTrainingsCount = $trainings->count();
        $activeTrainingsCount = $trainings->where('status', 'active')->count();
        $inactiveTrainingsCount = $trainings->where('status', 'inactive')->count();
        $completedTrainingsCount = $trainings->where('status', 'completed')->count();

        return view('training-coordinator.trainings.index', compact(
            'trainings',
            'myTrainingsCount',
            'activeTrainingsCount',
            'inactiveTrainingsCount',
            'completedTrainingsCount'
        ));
    }

    public function availableTrainings()
    {
        $trainings = Training::where('status', 'active')
            ->latest()
            ->get();

        return view('graduate.trainings.index', compact('trainings'));
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

    public function approveApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
        $application->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'تم الموافقة على طلب التدريب بنجاح');
    }

    public function rejectApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
        $application->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'تم رفض طلب التدريب بنجاح');
    }

    public function pendingApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
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

    public function calendar(Request $request)
    {
        try {
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);

            $month = max(1, min(12, $month));
            $year = max(2020, min(2030, $year));

            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $trainings = Training::where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
                ->get();

            $calendar = $this->generateCalendar($month, $year, $trainings);

            return view('training-coordinator.calendar', compact('calendar', 'trainings', 'month', 'year', 'startDate'));

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

    public function submitApplication(Request $request)
    {
        try {
            $user = Auth::user();
            $trainingId = $request->training_id;

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
        // 1. مخطط توزيع أنواع التدريبات
        $trainingTypes = Training::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        // 2. مخطط توزيع حالات التدريبات
        $trainingStatus = Training::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // 3. مخطط توزيع طلبات التدريب
        $applicationStatus = TrainingApplication::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // 4. مخطط التدريبات الشهرية
        $monthlyTrainings = Training::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $monthlyLabels = [];
        $monthlyData = [];
        foreach ($monthlyTrainings as $training) {
            $monthlyLabels[] = $this->getArabicMonthName($training->month) . ' ' . $training->year;
            $monthlyData[] = $training->count;
        }

        // 5. مخطط نسبة الإشغال
        $occupancyRates = [];
        $trainings = Training::withCount(['applications'])->get();
        foreach ($trainings as $training) {
            if ($training->seats > 0) {
                $occupancyRate = ($training->applications_count / $training->seats) * 100;
                $occupancyRates[] = [
                    'training' => $training->title,
                    'rate' => min($occupancyRate, 100) // لا تتجاوز 100%
                ];
            }
        }

        // 6. مخطط توزيع الشركات
        $companyDistribution = Training::with('company')
            ->get()
            ->groupBy('company.name')
            ->map(function ($trainings) {
                return $trainings->count();
            })
            ->sortDesc()
            ->take(10)
            ->toArray();

        return [
            'training_types' => [
                'labels' => array_keys($trainingTypes),
                'data' => array_values($trainingTypes),
                'colors' => ['#4f46e5', '#10b981', '#f59e0b', '#ef4444']
            ],
            'training_status' => [
                'labels' => array_keys($trainingStatus),
                'data' => array_values($trainingStatus),
                'colors' => ['#10b981', '#6b7280', '#3b82f6']
            ],
            'application_status' => [
                'labels' => array_keys($applicationStatus),
                'data' => array_values($applicationStatus),
                'colors' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280']
            ],
            'monthly_trends' => [
                'labels' => array_reverse($monthlyLabels),
                'data' => array_reverse($monthlyData),
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
}
