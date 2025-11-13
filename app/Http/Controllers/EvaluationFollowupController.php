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
}
