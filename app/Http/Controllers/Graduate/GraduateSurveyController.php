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

        // سجل الخريج الأكاديمي
        $graduate = $user->graduateData 
            ?? \App\Models\GraduateData::where('user_id', $user->id)->first() 
            ?? \App\Models\GraduateData::where('email', $user->email)->first();

        // الاستعلام الأساسي للاستبيانات النشطة
        $baseQuery = Survey::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', now());
            });

        // 1. الاستبيانات العامة وتلك الموجهة للخريجين أو لكافة المستخدمين
        $generalSurveys = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereIn('target_audience', ['graduates', 'all'])
                  ->orWhereNull('target_audience');
            })
            ->where(function ($q) {
                $q->where('type', 'general')
                  ->orWhereNull('type')
                  ->orWhereNotIn('type', ['training', 'job_opportunity', 'job_fair']);
            })
            ->get();

        // 2. استبيانات معارض التوظيف
        $myFairIds = \App\Models\JobFairRegistration::where('user_id', $user->id)->pluck('job_fair_id')->toArray();
        $fairSurveys = (clone $baseQuery)
            ->where('type', 'job_fair')
            ->where(function ($q) {
                $q->whereIn('target_audience', ['graduates', 'all'])
                  ->orWhereNull('target_audience');
            })
            ->where(function ($q) use ($myFairIds) {
                $q->whereIn('related_id', $myFairIds)
                  ->orWhereNull('related_id');
            })
            ->get();

        // 3. استبيانات التدريب (للخريجين المسجلين أو المقبولين بالبرامج)
        $myTrainingIds = \App\Models\TrainingApplication::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'completed', 'attended'])
            ->pluck('training_id')
            ->toArray();

        $trainingSurveys = (clone $baseQuery)
            ->where('type', 'training')
            ->where(function ($q) {
                $q->whereIn('target_audience', ['graduates', 'all'])
                  ->orWhereNull('target_audience');
            })
            ->where(function ($q) use ($myTrainingIds) {
                $q->whereIn('related_id', $myTrainingIds)
                  ->orWhereNull('related_id');
            })
            ->get();

        // 4. استبيانات فرص العمل (للخريجين المرشحين)
        $myJobIds = [];
        if ($graduate) {
            $myJobIds = \App\Models\Nomination::where('graduate_id', $graduate->id)->pluck('job_opportunity_id')->toArray();
        }
        $jobSurveys = (clone $baseQuery)
            ->where('type', 'job_opportunity')
            ->where(function ($q) {
                $q->whereIn('target_audience', ['graduates', 'all'])
                  ->orWhereNull('target_audience');
            })
            ->where(function ($q) use ($myJobIds) {
                $q->whereIn('related_id', $myJobIds)
                  ->orWhereNull('related_id');
            })
            ->get();

        $surveys = $generalSurveys
            ->merge($fairSurveys)
            ->merge($trainingSurveys)
            ->merge($jobSurveys)
            ->unique('id');

        // التحقق من الاستبيانات التي تم الإجابة عليها
        $answeredSurveyIds = SurveyResponse::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('graduate.surveys.index', compact('surveys', 'answeredSurveyIds'));
    }

    public function show(Survey $survey)
    {
        // التحقق من وجود رد سابق
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->first();

        // إذا كان هناك رد سابق والاستبيان منتهي، منع التعديل
        if ($existingResponse && $survey->end_date && now()->isAfter($survey->end_date)) {
            return redirect()->route('graduate.surveys.index')
                ->with('info', 'لقد انتهت مدة هذا الاستبيان ولا يمكن تعديل الإجابات.');
        }

        return view('graduate.surveys.show', compact('survey', 'existingResponse'));
    }

    public function store(Request $request, Survey $survey)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $user = Auth::user();

        // البحث عن رد سابق
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            $existingResponse->update([
                'answers' => $request->answers,
                'submitted_at' => now(),
            ]);

            return redirect()->route('graduate.surveys.index')
                ->with('success', 'تم تحديث إجاباتك بنجاح. شكراً لمشاركتك!');
        }

        SurveyResponse::create([
            'survey_id'         => $survey->id,
            'user_id'           => $user->id,
            'participant_name'  => $user->name,
            'participant_email' => $user->email,
            'answers'           => $request->answers,
            'submitted_at'      => now(),
        ]);

        return redirect()->route('graduate.surveys.index')
            ->with('success', 'تم إرسال إجاباتك بنجاح. شكراً لمشاركتك القيمة في تقييم وتطوير خدماتنا!');
    }
}
