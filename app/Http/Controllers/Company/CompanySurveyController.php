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

        // معارض التوظيف التي تشارك فيها الشركة
        $myFairIds = $company
            ? JobFairCompany::where('company_id', $company->id)->pluck('job_fair_id')->toArray()
            : [];

        $surveys = Survey::query()
            ->with('jobFair')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhereDate('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', now()))
            // شرط أساسي: الاستبيان موجه للشركات أو للجميع فقط
            ->forAudience('companies')
            ->where(function ($q) use ($myFairIds) {
                // استبيانات عامة (غير مرتبطة بتدريب أو وظيفة أو معرض)
                $q->whereNull('type')
                  ->orWhereNotIn('type', ['training', 'job_opportunity', 'job_fair']);

                // استبيانات المعارض: للمعارض المشاركة فيها أو غير المرتبطة بمعرض بعينه
                $q->orWhere(function ($sub) use ($myFairIds) {
                    $sub->where('type', 'job_fair')
                        ->where(function ($r) use ($myFairIds) {
                            $r->whereNull('related_id');
                            if (!empty($myFairIds)) {
                                $r->orWhereIn('related_id', $myFairIds);
                            }
                        });
                });
            })
            ->latest()
            ->get();

        $answeredSurveyIds = SurveyResponse::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('company.surveys.index', compact('surveys', 'answeredSurveyIds', 'company'));
    }

    public function show(Survey $survey)
    {
        if ($redirect = $this->guard($survey)) {
            return $redirect;
        }

        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse && $survey->end_date && now()->isAfter($survey->end_date->copy()->endOfDay())) {
            return redirect()->route('company.surveys.index')
                ->with('info', 'لقد انتهت مدة هذا الاستبيان ولا يمكن تعديل الإجابات.');
        }

        return view('company.surveys.show', compact('survey', 'existingResponse'));
    }

    public function store(Request $request, Survey $survey)
    {
        if ($redirect = $this->guard($survey)) {
            return $redirect;
        }

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
                'answers'      => $request->answers,
                'submitted_at' => now(),
            ]);

            return redirect()->route('company.surveys.index')
                ->with('success', 'تم تحديث إجابات شركتكم بنجاح. شكراً لتعاونكم المستمر!');
        }

        SurveyResponse::create([
            'survey_id'         => $survey->id,
            'user_id'           => $user->id,
            'participant_name'  => $company?->name ?? $user->name,
            'participant_email' => $user->email,
            'answers'           => $request->answers,
            'submitted_at'      => now(),
        ]);

        return redirect()->route('company.surveys.index')
            ->with('success', 'تم تقديم إجابات الاستبيان بنجاح. نقدر تقييمكم وملاحظاتكم القيمة لتطوير الشراكة!');
    }

    /**
     * منع الوصول لاستبيان غير موجه للشركات (مثلاً عبر الرابط المباشر)
     */
    private function guard(Survey $survey)
    {
        if (!$survey->isForAudience('companies') || !$survey->is_active) {
            return redirect()->route('company.surveys.index')
                ->with('info', 'هذا الاستبيان غير متاح لحساب الشركة.');
        }

        return null;
    }
}
