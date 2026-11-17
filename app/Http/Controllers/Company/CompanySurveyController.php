<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\JobFairCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySurveyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // معرفات معارض التوظيف التي تشارك فيها الشركة
        $myFairIds = [];
        if ($company) {
            $myFairIds = JobFairCompany::where('company_id', $company->id)->pluck('job_fair_id')->toArray();
        }

        // الاستعلام الأساسي للاستبيانات النشطة
        $baseQuery = Survey::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', now());
            });

        // 1. استبيانات عامة أو موجهة للشركات
        $companySurveys = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereIn('target_audience', ['companies', 'all']);
            })
            ->where(function ($q) {
                $q->where('type', 'general')
                  ->orWhereNull('type')
                  ->orWhereNotIn('type', ['training', 'job_opportunity', 'job_fair']);
            })
            ->get();

        // 2. استبيانات معارض التوظيف الخاصة بالشركات المشاركة
        $fairSurveys = (clone $baseQuery)
            ->where('type', 'job_fair')
            ->where(function ($q) {
                $q->whereIn('target_audience', ['companies', 'all'])
                  ->orWhereNull('target_audience');
            })
            ->where(function ($q) use ($myFairIds) {
                if (!empty($myFairIds)) {
                    $q->whereIn('related_id', $myFairIds)->orWhereNull('related_id');
                } else {
                    $q->whereNull('related_id');
                }
            })
            ->get();

        $surveys = $companySurveys->merge($fairSurveys)->unique('id');

        // الردود السابقة
        $answeredSurveyIds = SurveyResponse::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('company.surveys.index', compact('surveys', 'answeredSurveyIds', 'company'));
    }

    public function show(Survey $survey)
    {
        $user = Auth::user();

        // التحقق من وجود رد سابق
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse && $survey->end_date && now()->isAfter($survey->end_date)) {
            return redirect()->route('company.surveys.index')
                ->with('info', 'لقد انتهت مدة هذا الاستبيان ولا يمكن تعديل الإجابات.');
        }

        return view('company.surveys.show', compact('survey', 'existingResponse'));
    }

    public function store(Request $request, Survey $survey)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            $existingResponse->update([
                'answers' => $request->answers,
                'submitted_at' => now(),
            ]);

            return redirect()->route('company.surveys.index')
                ->with('success', 'تم تحديث إجابات شركتكم بنجاح. شكراً لتعاونكم المستمر!');
        }

        SurveyResponse::create([
            'survey_id'         => $survey->id,
            'user_id'           => $user->id,
            'participant_name'  => $company ? $company->name : $user->name,
            'participant_email' => $user->email,
            'answers'           => $request->answers,
            'submitted_at'      => now(),
        ]);

        return redirect()->route('company.surveys.index')
            ->with('success', 'تم تقديم إجابات الاستبيان بنجاح. نقدر تقييمكم وملاحظاتكم القيمة لتطوير الشراكة!');
    }
}
