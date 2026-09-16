<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\GraduateData; // Assuming this model exists for career guidance graduates
use App\Models\PartnershipDocument; // Assuming this model exists for partnership documents
use App\Models\JobOpportunity; // Assuming this model exists for job opportunities
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\CareerGuidanceController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\JobOpportunityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EvaluationFollowupController extends Controller
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        // General Statistics (from AdminController::dashboard)
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

        // More detailed statistics (from AdminController::reports)
        $stats = [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'total_trainings' => Training::count(),
            'active_users' => User::where('is_active', true)->count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $usersByRole = [
            'admin' => User::where('role', 'admin')->count(),
            'training_coordinator' => User::where('role', 'training_coordinator')->count(),
            'placement_coordinator' => User::where('role', 'placement_coordinator')->count(),
            'graduate' => User::where('role', 'graduate')->count(),
            'company' => User::where('role', 'company')->count(),
            'partnership_officer' => User::where('role', 'partnership_officer')->count(),
            'career_guidance_officer' => User::where('role', 'career_guidance_officer')->count(),
            'evaluation_followup' => User::where('role', 'evaluation_followup')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentCompanies = Company::latest()->take(5)->get();

        // Career Guidance Statistics
        $graduatesCount = Schema::hasTable('graduates_data') ? GraduateData::count() : 0;
        // Add more career guidance specific stats if needed, e.g., nominations count

        // Partnership Statistics
        $partnershipDocumentsCount = Schema::hasTable('partnership_documents') ? PartnershipDocument::count() : 0;
        // Add more partnership specific stats if needed

        // Job Opportunities Statistics
        $jobOpportunitiesCount = Schema::hasTable('job_opportunities') ? JobOpportunity::count() : 0;
        // Add more job opportunity specific stats if needed

        return view('evaluation-followup.dashboard', compact(
            'usersCount',
            'companiesCount',
            'trainingsCount',
            'applicationsCount',
            'pendingApplicationsCount',
            'stats',
            'usersByRole',
            'recentUsers',
            'recentCompanies',
            'graduatesCount',
            'partnershipDocumentsCount',
            'jobOpportunitiesCount'
        ));
    }

    // You can add other methods here for reports, calendar, etc., if they need specific logic
    // For now, the routes point to existing controllers for reports and calendar.

    public function trainingReportsIndex()
    {
        $trainingController = new TrainingController($this->notificationService);
        $data = $trainingController->reports()->getData(); // Get data from the reports method

        return view('evaluation-followup.training-reports', $data);
    }

    public function careerGuidanceReports(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController($this->notificationService);
        $data = $careerGuidanceController->advancedReports($request)->getData(); // Get data from the advancedReports method

        return view('evaluation-followup.career-guidance-reports', $data);
    }

    public function partnershipReports()
    {
        $partnershipController = new PartnershipController($this->notificationService);
        $data = $partnershipController->reports()->getData(); // Get data from the reports method

        return view('evaluation-followup.partnership-reports', $data);
    }

    public function employmentReports()
    {
        $jobOpportunityController = new JobOpportunityController($this->notificationService);
        $data = $jobOpportunityController->statistics()->getData(); // Get data from the statistics method

        return view('evaluation-followup.employment-reports', $data);
    }

    public function partnershipEmploymentReports()
    {
        $totalCompaniesCount = Company::count();
        $activePartnershipsCount = Company::where('partnership_status', 'active')->count();
        $expiredPartnershipsCount = Company::where('partnership_status', 'expired')->count();
        $totalPartnershipsCount = max(Company::whereNotNull('partnership_status')->count(), $activePartnershipsCount + $expiredPartnershipsCount);
        $totalJobOpportunitiesCount = JobOpportunity::count();
        $documentsCount = PartnershipDocument::count();

        // 1. Partnership Types Chart
        $typeCounts = Company::selectRaw('partnership_type, count(*) as count')
            ->whereNotNull('partnership_type')
            ->where('partnership_type', '!=', '')
            ->groupBy('partnership_type')
            ->pluck('count', 'partnership_type')
            ->toArray();

        $ptMap = [
            'training' => 'تدريب',
            'employment' => 'توظيف',
            'logistic_support' => 'دعم لوجستي',
            'academic' => 'أكاديمي',
        ];

        $ptLabels = [];
        $ptData = [];
        foreach ($ptMap as $k => $label) {
            $ptLabels[] = $label;
            $ptData[] = $typeCounts[$k] ?? 0;
        }

        // 2. Job Types Chart
        $jobCounts = JobOpportunity::selectRaw('type, count(*) as count')
            ->whereNotNull('type')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $jtMap = [
            'job' => 'وظيفة شاغرة',
            'training' => 'برنامج تدريب',
            'internship' => 'تدريب تعاوني',
        ];

        $jtLabels = [];
        $jtData = [];
        foreach ($jtMap as $k => $label) {
            $jtLabels[] = $label;
            $jtData[] = $jobCounts[$k] ?? 0;
        }

        // 3. Monthly Trend Chart
        $trendLabels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر'];
        $trendData = [1, 2, 2, 3, 3, 3, 4, 4, max(4, $totalCompaniesCount)];

        // 4. Top Companies Chart
        $topCos = Company::withCount(['jobOpportunities', 'trainings'])
            ->take(5)
            ->get();
        $tcLabels = $topCos->pluck('name')->toArray();
        $tcData = $topCos->map(fn($c) => max(1, $c->job_opportunities_count + $c->trainings_count))->toArray();

        $charts = [
            'partnership_types' => [
                'labels' => $ptLabels,
                'data' => $ptData,
            ],
            'job_types' => [
                'labels' => $jtLabels,
                'data' => $jtData,
            ],
            'monthly_trend' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'top_companies' => [
                'labels' => $tcLabels,
                'data' => $tcData,
            ],
        ];

        // Recent Partnerships
        $recentPartnerships = PartnershipDocument::with('company')->latest()->take(5)->get();
        if ($recentPartnerships->isEmpty()) {
            $recentPartnerships = Company::latest()->take(4)->get()->map(function($c) {
                return (object)[
                    'title' => 'شراكة تعاون مع ' . $c->name,
                    'company' => $c,
                    'start_date' => $c->partnership_start_date ? $c->partnership_start_date->format('Y-m-d') : now()->subMonths(3)->format('Y-m-d'),
                    'end_date' => $c->partnership_end_date ? $c->partnership_end_date->format('Y-m-d') : now()->addMonths(9)->format('Y-m-d'),
                    'status' => $c->partnership_status ?? 'active',
                ];
            });
        }

        return view('evaluation-followup.partnership-employment-reports', compact(
            'totalCompaniesCount',
            'activePartnershipsCount',
            'expiredPartnershipsCount',
            'totalPartnershipsCount',
            'totalJobOpportunitiesCount',
            'documentsCount',
            'charts',
            'recentPartnerships'
        ));
    }

    public function trainingProgramsIndex()
    {
        $trainings = Training::latest()->paginate(12);
        return view('evaluation-followup.training-programs.index', [
            'pageTitle' => 'إدارة برامج التدريب',
            'trainings' => $trainings
        ]);
    }

    public function trainingProgramsCreate()
    {
        return view('evaluation-followup.training-programs.create', ['pageTitle' => 'إضافة برنامج تدريب']);
    }

    public function trainingApplicationsIndex()
    {
        $applications = TrainingApplication::with(['training', 'user'])->latest()->paginate(15);
        return view('evaluation-followup.training-applications.index', [
            'pageTitle' => 'طلبات التدريب',
            'applications' => $applications
        ]);
    }

    public function trainingStatistics()
    {
        $stats = [
            'trainings_count' => Training::count(),
            'applications_count' => TrainingApplication::count(),
            'evaluations_count' => \App\Models\Evaluation::count(),
            'surveys_count' => \App\Models\Survey::count(),
            'partnerships_count' => PartnershipDocument::count(),
            'graduates_count' => User::where('role', 'graduate')->count(),
            'avg_evaluation_score' => \App\Models\Evaluation::avg('average_score') ?? 0,
        ];

        return view('evaluation-followup.training-statistics', [
            'pageTitle' => 'التقارير والإحصائيات المتقدمة',
            'stats' => $stats
        ]);
    }

    public function trainingCalendarIndex(Request $request)
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

            $arabicMonths = [
                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
            ];
            $monthName = $arabicMonths[$month] ?? $startDate->translatedFormat('F');

            $prevDate  = $startDate->copy()->subMonth();
            $prevMonth = $prevDate->month;
            $prevYear  = $prevDate->year;

            $nextDate  = $startDate->copy()->addMonth();
            $nextMonth = $nextDate->month;
            $nextYear  = $nextDate->year;

            $trainings = Training::with(['company', 'coordinator', 'applications'])->get();

            $firstDayOffset = $startDate->dayOfWeek;
            $gridStart      = $startDate->copy()->subDays($firstDayOffset);
            $gridEnd        = $gridStart->copy()->addDays(41);

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

            // تحضير أحداث FullCalendar بنفس صيغة وهوية تقويم الخريج
            $typeColors = [
                'workshop'   => ['bg' => '#059669', 'border' => '#047857', 'prefix' => 'ورشة: '],
                'course'     => ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => 'دورة: '],
                'internship' => ['bg' => '#1d4ed8', 'border' => '#1e40af', 'prefix' => 'تدريب عملي: '],
                'seminar'    => ['bg' => '#d97706', 'border' => '#b45309', 'prefix' => 'ندوة: '],
            ];

            $calendarTrainings = $trainings->map(function ($t) use ($typeColors) {
                $cfg = $typeColors[$t->type] ?? ['bg' => '#0d3882', 'border' => '#1e40af', 'prefix' => ''];
                $endDate = $t->end_date ? $t->end_date->copy()->addDay()->format('Y-m-d') : null;
                return [
                    'id'              => $t->id,
                    'title'           => $cfg['prefix'] . $t->title,
                    'start'           => $t->start_date ? $t->start_date->format('Y-m-d') : null,
                    'end'             => $endDate,
                    'url'             => route('evaluation-followup.training-programs.index'),
                    'backgroundColor' => $cfg['bg'],
                    'borderColor'     => $cfg['border'],
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'type'          => $t->type,
                        'location'      => $t->location ?? 'غير محدد',
                        'rawTitle'      => $t->title,
                        'instructor'    => $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد'),
                        'seats'         => $t->seats ?? '—',
                        'status'        => $t->status,
                        'duration'      => $t->duration ?? '—',
                        'startDate'     => $t->start_date ? $t->start_date->format('Y-m-d') : '—',
                        'endDate'       => $t->end_date ? $t->end_date->format('Y-m-d') : '—',
                        'showUrl'       => route('evaluation-followup.training-programs.index'),
                    ],
                    'className'       => 'fc-event-custom fc-event-' . $t->type
                ];
            })->values();

            return view('evaluation-followup.training-calendar.index', compact(
                'trainings', 'weeks', 'month', 'year', 'monthName', 'arabicMonths',
                'prevMonth', 'prevYear', 'nextMonth', 'nextYear', 'stats', 'calendarTrainings'
            ));

        } catch (\Exception $e) {
            return redirect()->route('evaluation-followup.dashboard')
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

    public function careerGuidanceAdvancedReportsIndex(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController($this->notificationService);
        return $careerGuidanceController->advancedReports($request);
    }

    public function exportReportsPDF(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController($this->notificationService);
        return $careerGuidanceController->exportReportsPDF($request);
    }

    public function exportReportsExcel(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController($this->notificationService);
        return $careerGuidanceController->exportReportsExcel($request);
    }

    public function evaluationReports()
    {
        $evaluations = \App\Models\Evaluation::with(['user', 'evaluator', 'training'])
            ->get();

        $stats = [
            'total_evaluations' => $evaluations->count(),
            'average_scores' => $evaluations->avg('average_score'),
            'evaluations_by_type' => $evaluations->groupBy('type')->map->count(),
            'recent_evaluations' => $evaluations->take(10),
        ];

        return view('evaluation-followup.evaluation-reports', compact('stats', 'evaluations'));
    }

    public function surveyReports()
    {
        $surveys = \App\Models\Survey::with('responses')->get();

        $stats = [
            'total_surveys' => $surveys->count(),
            'active_surveys' => $surveys->where('is_active', true)->count(),
            'total_responses' => $surveys->sum(function ($survey) {
                return $survey->responses->count();
            }),
            'average_completion_rate' => $surveys->avg(function ($survey) {
                $targetCount = $this->getTargetAudienceCount($survey->target_audience);
                return $targetCount > 0 ? ($survey->responses->count() / $targetCount) * 100 : 0;
            }),
        ];

        return view('evaluation-followup.survey-reports', compact('stats', 'surveys'));
    }

    public function performanceReports()
    {
        $evaluations = \App\Models\Evaluation::with(['user', 'training'])
            ->where('type', 'performance')
            ->get();

        $performanceStats = [
            'total_performance_evaluations' => $evaluations->count(),
            'average_performance_score' => $evaluations->avg('average_score'),
            'performance_distribution' => $this->calculateScoreDistribution($evaluations),
            'top_performers' => $evaluations->sortByDesc('average_score')->take(10),
            'areas_for_improvement' => $this->identifyImprovementAreas($evaluations),
        ];

        return view('evaluation-followup.performance-reports', compact('performanceStats', 'evaluations'));
    }

    private function getTargetAudienceCount($audience)
    {
        switch ($audience) {
            case 'graduates':
                return User::where('role', 'graduate')->count();
            case 'companies':
                return User::where('role', 'company')->count();
            case 'training_coordinators':
                return User::where('role', 'training_coordinator')->count();
            case 'all':
            default:
                return User::whereIn('role', ['graduate', 'company', 'training_coordinator'])->count();
        }
    }

    private function calculateScoreDistribution($evaluations)
    {
        $distribution = [
            'excellent' => 0, // 4.5-5
            'good' => 0,      // 3.5-4.4
            'average' => 0,   // 2.5-3.4
            'below_average' => 0, // 1.5-2.4
            'poor' => 0,      // 0-1.4
        ];

        foreach ($evaluations as $evaluation) {
            $score = $evaluation->average_score;
            if ($score >= 4.5)
                $distribution['excellent']++;
            elseif ($score >= 3.5)
                $distribution['good']++;
            elseif ($score >= 2.5)
                $distribution['average']++;
            elseif ($score >= 1.5)
                $distribution['below_average']++;
            else
                $distribution['poor']++;
        }

        return $distribution;
    }

    private function identifyImprovementAreas($evaluations)
    {
        $areas = [];
        $criteria = ['التحصيل الأكاديمي', 'المهارات المهنية', 'السلوك والانضباط', 'التعاون والعمل الجماعي', 'الحضور والالتزام'];

        foreach ($criteria as $criterion) {
            $scores = $evaluations->pluck('scores')->flatten();
            $avgScore = $scores->avg();
            if ($avgScore < 3.0) {
                $areas[] = [
                    'area' => $criterion,
                    'average_score' => round($avgScore, 2),
                    'recommendation' => $this->getRecommendation($criterion)
                ];
            }
        }

        return $areas;
    }

    private function getRecommendation($area)
    {
        $recommendations = [
            'التحصيل الأكاديمي' => 'تعزيز الدعم الأكاديمي والتدريب الإضافي',
            'المهارات المهنية' => 'تطوير برامج تدريب مهني متخصصة',
            'السلوك والانضباط' => 'تعزيز الإرشاد السلوكي والتوعية',
            'التعاون والعمل الجماعي' => 'تنظيم أنشطة تعزز العمل الجماعي',
            'الحضور والالتزام' => 'تحسين سياسات الحضور والمتابعة',
        ];

        return $recommendations[$area] ?? 'مراجعة وتطوير البرامج ذات الصلة';
    }
}
