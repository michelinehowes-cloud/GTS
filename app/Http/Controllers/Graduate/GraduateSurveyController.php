<?php

namespace App\Http\Controllers\Graduate;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraduateSurveyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // البحث عن سجل الخريج باستخدام البريد الإلكتروني
        $graduate = \App\Models\GraduateData::where('email', $user->email)->first();

        // 1. الاستبيانات العامة
        $generalSurveys = Survey::where('type', 'general')
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        $trainingSurveys = collect();
        $jobSurveys = collect();

        if ($graduate) {
            // 2. استبيانات التدريب (للخريجين المقبولين في التدريب)
            $trainingSurveys = Survey::where('type', 'training')
                ->where('is_active', true)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->whereHas('training', function ($query) use ($user) {
                    $query->whereHas('applications', function ($q) use ($user) {
                        $q->where('user_id', $user->id)->where('status', 'approved');
                    });
                })
                ->get();

            // 3. استبيانات فرص العمل (للخريجين المرشحين)
            $jobSurveys = Survey::where('type', 'job_opportunity')
                ->where('is_active', true)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->whereHas('jobOpportunity', function ($query) use ($graduate) {
                    $query->whereHas('nominations', function ($q) use ($graduate) {
                        $q->where('graduate_id', $graduate->id);
                    });
                })
                ->get();
        }

        $surveys = $generalSurveys->merge($trainingSurveys)->merge($jobSurveys);

        // التحقق من الاستبيانات التي تم الإجابة عليها
        $answeredSurveyIds = SurveyResponse::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('graduate.surveys.index', compact('surveys', 'answeredSurveyIds'));
    }

    public function show(Survey $survey)
    {
        // التحقق مما إذا كان قد أجاب مسبقاً
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse) {
            return redirect()->route('graduate.surveys.index')->with('info', 'لقد قمت بالإجابة على هذا الاستبيان مسبقاً.');
        }

        return view('graduate.surveys.show', compact('survey'));
    }

    public function store(Request $request, Survey $survey)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => Auth::id(),
            'answers' => $request->answers,
            'submitted_at' => now(),
        ]);

        return redirect()->route('graduate.surveys.index')->with('success', 'تم إرسال إجاباتك بنجاح. شكراً لمشاركتك!');
    }
}
