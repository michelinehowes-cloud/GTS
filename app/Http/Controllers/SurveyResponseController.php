<?php

namespace App\Http\Controllers;

use App\Models\SurveyResponse;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $responses = SurveyResponse::with(['survey', 'user'])
            ->latest()
            ->paginate(15);

        $surveys = Survey::all();

        return view('evaluation-followup.survey-responses.index', compact('responses', 'surveys'));
    }


    public function show(SurveyResponse $response)
    {
        $response->load(['survey', 'user']);

        return view('evaluation-followup.survey-responses.show', compact('response'));
    }

    public function destroy(SurveyResponse $response)
    {
        $response->delete();

        return redirect()->route('evaluation-followup.survey-responses.index')
            ->with('success', 'تم حذف رد الاستبيان بنجاح');
    }

    public function submit(Request $request, Survey $survey)
    {
        // Check if user already responded
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse) {
            return redirect()->back()
                ->with('error', 'لقد قمت بالرد على هذا الاستبيان مسبقاً');
        }

        // Validate responses
        $validator = $this->validateResponses($request->all(), $survey);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => Auth::id(),
            'answers' => $request->responses,
            'submitted_at' => now(),
        ]);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'تم إرسال ردك على الاستبيان بنجاح');
    }

    private function validateResponses($data, Survey $survey)
    {
        $rules = [];

        foreach ($survey->questions as $index => $question) {
            $fieldName = "responses.{$index}";

            switch ($question['type']) {
                case 'text':
                    $rules[$fieldName] = 'required|string|max:1000';
                    break;
                case 'radio':
                case 'select':
                    $rules[$fieldName] = 'required|string|in:' . implode(',', $question['options'] ?? []);
                    break;
                case 'checkbox':
                    $rules[$fieldName] = 'required|array|min:1';
                    $rules[$fieldName . '.*'] = 'string|in:' . implode(',', $question['options'] ?? []);
                    break;
            }
        }

        return \Illuminate\Support\Facades\Validator::make($data, $rules);
    }
}
