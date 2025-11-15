<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
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

    public function create()
    {
        return view('evaluation-followup.surveys.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,radio,checkbox,select,rating,date,email,number',
            'questions.*.required' => 'boolean',
            'questions.*.description' => 'nullable|string',
            'questions.*.options' => 'required_if:questions.*.type,radio,checkbox,select|array',
            'questions.*.max_rating' => 'required_if:questions.*.type,rating|in:5,10',
            'target_audience' => 'required|in:all,graduates,companies,training_coordinators',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Survey::create([
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $request->questions,
            'target_audience' => $request->target_audience,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('evaluation-followup.surveys.index')
            ->with('success', 'تم إنشاء الاستبيان بنجاح');
    }



    public function show(Survey $survey)
    {
        $survey->load(['responses.user']);
        $responsesCount = $survey->responses()->count();
        $completionRate = $this->calculateCompletionRate($survey);

        return view('evaluation-followup.surveys.show', compact('survey', 'responsesCount', 'completionRate'));
    }

    public function edit(Survey $survey)
    {
        return view('evaluation-followup.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:text,radio,checkbox,select,rating,date,email,number',
            'questions.*.required' => 'boolean',
            'questions.*.description' => 'nullable|string',
            'questions.*.options' => 'required_if:questions.*.type,radio,checkbox,select|array',
            'questions.*.max_rating' => 'required_if:questions.*.type,rating|in:5,10',
            'target_audience' => 'required|in:all,graduates,companies,training_coordinators',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

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

        if ($totalPossibleResponses == 0) return 0;

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
