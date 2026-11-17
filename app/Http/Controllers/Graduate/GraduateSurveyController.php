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

        // حسابات الشركات لها صفحة استبيانات مستقلة
        if ($user->role === 'company') {
            return redirect()->route('company.surveys.index');
        }

        $graduate = $user->graduateData
            ?? \App\Models\GraduateData::where('user_id', $user->id)->first()
            ?? \App\Models\GraduateData::where('email', $user->email)->first();

        $myFairIds = \App\Models\JobFairRegistration::where('user_id', $user->id)
            ->pluck('job_fair_id')->toArray();

        $myTrainingIds = \App\Models\TrainingApplication::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'completed', 'attended'])
            ->pluck('training_id')->toArray();

        $myJobIds = $graduate
            ? \App\Models\Nomination::where('graduate_id', $graduate->id)->pluck('job_opportunity_id')->toArray()
            : [];

        $linked = [
            'job_fair'        => $myFairIds,
            'training'        => $myTrainingIds,
            'job_opportunity' => $myJobIds,
        ];

        $surveys = Survey::query()
            ->with(['training', 'jobOpportunity', 'jobFair'])
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhereDate('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', now()))
            // شرط أساسي: الاستبيان موجه للخريجين أو للجميع فقط (لا تظهر استبيانات الشركات)
            ->forAudience('graduates')
            ->where(function ($q) use ($linked) {
                // استبيانات عامة غير مرتبطة بنشاط محدد
                $q->whereNull('type')
                  ->orWhereNotIn('type', array_keys($linked));

                // استبيانات مرتبطة بنشاط: تظهر لمن شارك فيه، أو إن لم تُربط بنشاط بعينه
                foreach ($linked as $type => $ids) {
                    $q->orWhere(function ($sub) use ($type, $ids) {
                        $sub->where('type', $type)
                            ->where(function ($r) use ($ids) {
                                $r->whereNull('related_id');
                                if (!empty($ids)) {
                                    $r->orWhereIn('related_id', $ids);
                                }
                            });
                    });
                }
            })
            ->latest()
            ->get();

        $answeredSurveyIds = SurveyResponse::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('graduate.surveys.index', compact('surveys', 'answeredSurveyIds'));
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
            return redirect()->route('graduate.surveys.index')
                ->with('info', 'لقد انتهت مدة هذا الاستبيان ولا يمكن تعديل الإجابات.');
        }

        return view('graduate.surveys.show', compact('survey', 'existingResponse'));
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

        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            $existingResponse->update([
                'answers'      => $request->answers,
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

    /**
     * منع الوصول لاستبيان غير موجه للخريجين (مثلاً عبر الرابط المباشر)
     */
    private function guard(Survey $survey)
    {
        if (Auth::user()->role === 'company') {
            return redirect()->route('company.surveys.show', $survey->id);
        }

        if (!$survey->isForAudience('graduates') || !$survey->is_active) {
            return redirect()->route('graduate.surveys.index')
                ->with('info', 'هذا الاستبيان غير متاح لحسابك.');
        }

        return null;
    }
}
