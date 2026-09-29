<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Evaluation;
use App\Models\Enrollment;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringEvaluationController extends Controller
{
    public function dashboard()
    {
        $kpis = [
            'employment_rate' => $this->calculateEmploymentRate(),
            'training_completion_rate' => $this->calculateTrainingCompletionRate(),
            'company_satisfaction_rate' => $this->calculateCompanySatisfaction(),
            'average_training_to_employment_days' => $this->calculateAverageTransitionTime(),
        ];

        $activeSurveys = Survey::where('is_active', true)->get();

        return view('monitoring.dashboard', compact('kpis', 'activeSurveys'));
    }

    public function surveys()
    {
        $surveys = Survey::withCount('responses')->get();
        return view('monitoring.surveys', compact('surveys'));
    }

    public function createSurvey(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'questions' => 'required|array|min:1',
            'target_audience' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Survey::create($request->all());

        return redirect()->back()->with('success', 'تم إنشاء الاستبيان بنجاح');
    }

    public function kpiReports()
    {
        $reports = [
            'employment_by_specialization' => $this->getEmploymentBySpecialization(),
            'training_effectiveness' => $this->getTrainingEffectiveness(),
            'company_feedback' => $this->getCompanyFeedback(),
        ];

        return view('monitoring.kpi-reports', compact('reports'));
    }

    private function calculateEmploymentRate()
    {
        $totalGraduates = User::where('role', 'graduate')->count();
        $employedGraduates = Application::where('status', 'hired')->distinct('user_id')->count();
        
        return $totalGraduates > 0 ? ($employedGraduates / $totalGraduates) * 100 : 0;
    }

    private function calculateTrainingCompletionRate()
    {
        $totalEnrollments = Enrollment::count();
        $completedEnrollments = Enrollment::where('status', 'completed')->count();
        
        return $totalEnrollments > 0 ? ($completedEnrollments / $totalEnrollments) * 100 : 0;
    }

    // دوال حسابية أخرى للمؤشرات...
}