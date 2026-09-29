<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\SurveyAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $surveys = Survey::with('responses')->latest()->paginate(15);

        return view('evaluation-followup.surveys.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        $selectedTemplate = null;
        if ($request->filled('template_id')) {
            $selectedTemplate = \App\Models\SurveyTemplate::find($request->input('template_id'));
        }

        $templates = \App\Models\SurveyTemplate::all();

        return view('evaluation-followup.surveys.create', compact('selectedTemplate', 'templates'));
    }

    public function store(Request $request)
    {
        // تنظيف البيانات - إزالة الخيارات الفارغة
        if ($request->has('questions')) {
            $questions = $request->questions;
            foreach ($questions as $index => $question) {
                if (isset($question['options']) && is_array($question['options'])) {
                    // إزالة الخيارات الفارغة
                    $questions[$index]['options'] = array_values(array_filter($question['options'], function ($option) {
                        return !empty(trim($option));
                    }));
                }
            }
            $request->merge(['questions' => $questions]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'nullable|in:training,job_opportunity,job_fair',
            'related_id' => 'nullable|integer',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,textarea,radio,checkbox,select,rating,date,email,number',
            'questions.*.required' => 'boolean',
            'questions.*.description' => 'nullable|string',
            'questions.*.options' => 'nullable|array',  // Changed to nullable
            'questions.*.options.*' => 'nullable|string',  // Each option should be string
            'questions.*.max_rating' => 'nullable|in:5,10',
            'target_audience' => 'required|in:all,graduates,companies,training_coordinators',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Custom validation for options based on question type
        $validator->after(function ($validator) use ($request) {
            if ($request->has('questions')) {
                foreach ($request->questions as $index => $question) {
                    $type = $question['type'] ?? '';

                    // Options are required for radio, checkbox, and select
                    if (in_array($type, ['radio', 'checkbox', 'select'])) {
                        if (empty($question['options']) || !is_array($question['options'])) {
                            $validator->errors()->add(
                                "questions.$index.options",
                                "يجب إضافة خيارات للسؤال رقم " . ($index + 1)
                            );
                        }
                    }

                    // Max rating is required for rating type
                    if ($type === 'rating' && empty($question['max_rating'])) {
                        $validator->errors()->add(
                            "questions.$index.max_rating",
                            "يجب تحديد عدد النجوم للسؤال رقم " . ($index + 1)
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $survey = Survey::create([
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $request->questions,
            'target_audience' => $request->target_audience,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'type' => $request->type,
            'related_id' => $request->related_id,
        ]);

        // حفظ كقالب جديد إذا تم اختيار ذلك
        if ($request->has('save_as_template') && !empty($request->input('template_title'))) {
            \App\Models\SurveyTemplate::create([
                'title' => $request->input('template_title'),
                'description' => $request->input('description'),
                'category' => $request->input('template_category', 'general'),
                'target_audience' => $request->input('target_audience', 'all'),
                'type' => $request->input('type'),
                'questions' => $request->input('questions'),
                'is_system' => false,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('evaluation-followup.surveys.index')
            ->with('success', 'تم إنشاء الاستبيان بنجاح' . ($request->has('save_as_template') ? ' وحفظه أيضاً كقالب جديد للاستخدام المستقبلي!' : ''));
    }



    public function show(Survey $survey, SurveyAnalyticsService $analyticsService)
    {
        $survey->load(['responses.user']);
        $analytics = $analyticsService->compute($survey);
        $responsesCount = $analytics['total_responses'];
        $completionRate = $analytics['completion_rate'];

        return view('evaluation-followup.surveys.show', compact('survey', 'analytics', 'responsesCount', 'completionRate'));
    }

    public function report(Survey $survey, SurveyAnalyticsService $analyticsService)
    {
        return $this->show($survey, $analyticsService);
    }

    public function exportResponses(Survey $survey, SurveyAnalyticsService $analyticsService)
    {
        return $analyticsService->exportResponsesCsv($survey);
    }

    public function edit(Survey $survey)
    {
        return view('evaluation-followup.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        // تنظيف البيانات - إزالة الخيارات الفارغة
        if ($request->has('questions')) {
            $questions = $request->questions;
            foreach ($questions as $index => $question) {
                if (isset($question['options']) && is_array($question['options'])) {
                    // إزالة الخيارات الفارغة
                    $questions[$index]['options'] = array_values(array_filter($question['options'], function ($option) {
                        return !empty(trim($option));
                    }));
                }
            }
            $request->merge(['questions' => $questions]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:training,job_opportunity,job_fair',
            'related_id' => 'nullable|integer',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,textarea,radio,checkbox,select,rating,date,email,number',
            'questions.*.required' => 'boolean',
            'questions.*.description' => 'nullable|string',
            'questions.*.options' => 'nullable|array',  // Changed to nullable
            'questions.*.options.*' => 'nullable|string',  // Each option should be string
            'questions.*.max_rating' => 'nullable|in:5,10',
            'target_audience' => 'required|in:all,graduates,companies,training_coordinators',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Custom validation for options based on question type
        $validator->after(function ($validator) use ($request) {
            if ($request->has('questions')) {
                foreach ($request->questions as $index => $question) {
                    $type = $question['type'] ?? '';

                    // Options are required for radio, checkbox, and select
                    if (in_array($type, ['radio', 'checkbox', 'select'])) {
                        if (empty($question['options']) || !is_array($question['options'])) {
                            $validator->errors()->add(
                                "questions.$index.options",
                                "يجب إضافة خيارات للسؤال رقم " . ($index + 1)
                            );
                        }
                    }

                    // Max rating is required for rating type
                    if ($type === 'rating' && empty($question['max_rating'])) {
                        $validator->errors()->add(
                            "questions.$index.max_rating",
                            "يجب تحديد عدد النجوم للسؤال رقم " . ($index + 1)
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $survey->update([
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $request->questions,
            'target_audience' => $request->target_audience,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'type' => $request->type,
            'related_id' => $request->related_id,
        ]);

        return redirect()->route('evaluation-followup.surveys.index')
            ->with('success', 'تم تحديث الاستبيان بنجاح');
    }



    public function destroy(Survey $survey)
    {
        // Check if survey has responses
        if ($survey->responses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الاستبيان لأنه يحتوي على ردود');
        }

        $survey->delete();

        return redirect()->route('evaluation-followup.surveys.index')
            ->with('success', 'تم حذف الاستبيان بنجاح');
    }

    private function calculateCompletionRate(Survey $survey)
    {
        // This is a simplified calculation - you might want to implement more sophisticated logic
        $totalPossibleResponses = $this->getTargetAudienceCount($survey->target_audience);
        $actualResponses = $survey->responses()->count();

        if ($totalPossibleResponses == 0)
            return 0;

        return round(($actualResponses / $totalPossibleResponses) * 100, 2);
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
}
