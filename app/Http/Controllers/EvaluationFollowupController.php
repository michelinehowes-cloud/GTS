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
            'usersCount', 'companiesCount', 'trainingsCount', 'applicationsCount', 'pendingApplicationsCount',
            'stats', 'usersByRole', 'recentUsers', 'recentCompanies',
            'graduatesCount', 'partnershipDocumentsCount', 'jobOpportunitiesCount'
        ));
    }

    // You can add other methods here for reports, calendar, etc., if they need specific logic
    // For now, the routes point to existing controllers for reports and calendar.

    public function trainingReportsIndex()
    {
        $trainingController = new TrainingController();
        $data = $trainingController->reports()->getData(); // Get data from the reports method

        return view('evaluation-followup.training-reports', $data);
    }

    public function careerGuidanceReports()
    {
        $careerGuidanceController = new CareerGuidanceController();
        $data = $careerGuidanceController->advancedReports()->getData(); // Get data from the advancedReports method

        return view('evaluation-followup.career-guidance-reports', $data);
    }

    public function partnershipReports()
    {
        $partnershipController = new PartnershipController();
        $data = $partnershipController->reports()->getData(); // Get data from the reports method

        return view('evaluation-followup.partnership-reports', $data);
    }

    public function employmentReports()
    {
        $jobOpportunityController = new JobOpportunityController();
        $data = $jobOpportunityController->statistics()->getData(); // Get data from the statistics method

        return view('evaluation-followup.employment-reports', $data);
    }

    public function partnershipEmploymentReports()
    {
        $partnershipController = new PartnershipController();
        $partnershipController = new PartnershipController();
        $partnershipReportsData = $partnershipController->reports()->getData();

        $jobOpportunityController = new JobOpportunityController();
        $employmentReportsData = $jobOpportunityController->statistics()->getData();

        // Extract specific data for clarity and to avoid key conflicts
        $partnershipStats = $partnershipReportsData['partnershipStats'] ?? [];
        $opportunityStatsFromPartnership = $partnershipReportsData['opportunityStats'] ?? []; // Opportunities from partnership context

        $employmentStats = $employmentReportsData['stats'] ?? []; // All stats from employment context

        // Fetch overall counts for KPIs
        $totalPartnershipsCount = PartnershipDocument::count();
        $totalCompaniesCount = Company::count();
        $totalJobOpportunitiesCount = JobOpportunity::count();

        // Pass all necessary data to the view with distinct names
        return view('evaluation-followup.partnership-employment-reports', [
            'partnershipStats' => $partnershipStats,
            'opportunityStats' => $opportunityStatsFromPartnership, // Keep this distinct if needed for partnership view
            'employmentStats' => $employmentStats, // Use this for all employment-related charts
            'totalPartnershipsCount' => $totalPartnershipsCount,
            'totalCompaniesCount' => $totalCompaniesCount,
            'totalJobOpportunitiesCount' => $totalJobOpportunitiesCount,
            'jobOpportunityTrends' => JobOpportunity::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                                        ->groupBy('month')
                                        ->orderBy('month')
                                        ->get(),
        ]);
    }

    public function trainingProgramsIndex()
    {
        return view('evaluation-followup.training-programs.index', ['pageTitle' => 'إدارة برامج التدريب']);
    }

    public function trainingProgramsCreate()
    {
        return view('evaluation-followup.training-programs.create', ['pageTitle' => 'إضافة برنامج تدريب']);
    }

    public function trainingApplicationsIndex()
    {
        return view('evaluation-followup.training-applications.index', ['pageTitle' => 'طلبات التدريب']);
    }

    public function trainingStatistics()
    {
        return view('evaluation-followup.training-statistics', ['pageTitle' => 'التقارير والإحصائيات']);
    }

    public function trainingCalendarIndex(Request $request)
    {
        try {
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);
            
            $month = max(1, min(12, $month));
            $year = max(2020, min(2030, $year));
            
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
            
            $trainings = Training::where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })
                ->get();
            
            $calendar = $this->generateCalendar($month, $year, $trainings);
            
            return view('evaluation-followup.training-calendar.index', compact('calendar', 'trainings', 'month', 'year', 'startDate'));
            
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
            $dayTrainings = $trainings->filter(function($training) use ($currentDay) {
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
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        
        return $months[$month] ?? 'غير معروف';
    }

    public function careerGuidanceAdvancedReportsIndex()
    {
        $careerGuidanceController = new CareerGuidanceController();
        $data = $careerGuidanceController->advancedReports()->getData();

        return view('evaluation-followup.advanced-reports.index', $data);
    }

    public function exportReportsPDF(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController();
        return $careerGuidanceController->exportReportsPDF($request);
    }

    public function exportReportsExcel(Request $request)
    {
        $careerGuidanceController = new CareerGuidanceController();
        return $careerGuidanceController->exportReportsExcel($request);
    }

    public function evaluationReports()
    {
        $evaluations = \App\Models\Evaluation::with(['user', 'evaluator', 'training'])
            ->completed()
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
            'total_responses' => $surveys->sum(function($survey) {
                return $survey->responses->count();
            }),
            'average_completion_rate' => $surveys->avg(function($survey) {
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
            ->completed()
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
                return \App\Models\User::where('role', 'graduate')->count();
            case 'companies':
                return \App\Models\User::where('role', 'company')->count();
            case 'training_coordinators':
                return \App\Models\User::where('role', 'training_coordinator')->count();
            case 'all':
            default:
                return \App\Models\User::whereIn('role', ['graduate', 'company', 'training_coordinator'])->count();
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
            if ($score >= 4.5) $distribution['excellent']++;
            elseif ($score >= 3.5) $distribution['good']++;
            elseif ($score >= 2.5) $distribution['average']++;
            elseif ($score >= 1.5) $distribution['below_average']++;
            else $distribution['poor']++;
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
