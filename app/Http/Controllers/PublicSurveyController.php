<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicSurveyController extends Controller
{
    public function show($slug)
    {
        $survey = Survey::where('slug', $slug)
                       ->where('is_public', true)
                       ->where('is_active', true)
                       ->where('start_date', '<=', now())
                       ->where('end_date', '>=', now())
                       ->first();

        if (!$survey) {
            abort(404, 'الاستبيان غير متاح أو انتهت صلاحيته');
        }

        return view('public.surveys.show', compact('survey'));
    }

    public function store(Request $request, $slug)
    {
        $survey = Survey::where('slug', $slug)
                       ->where('is_public', true)
                       ->where('is_active', true)
                       ->where('start_date', '<=', now())
                       ->where('end_date', '>=', now())
                       ->first();

        if (!$survey) {
            abort(404, 'الاستبيان غير متاح أو انتهت صلاحيته');
        }

        // التحقق من أن المشارك لم يرد على الاستبيان من قبل
        $existingResponse = SurveyResponse::where('survey_id', $survey->id)
                                         ->where('participant_email', $request->participant_email)
                                         ->first();

        if ($existingResponse) {
            return redirect()->back()
                           ->with('error', 'لقد قمت بالرد على هذا الاستبيان من قبل')
                           ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'responses' => 'required|array',
        ]);

        // التحقق من صحة الإجابات حسب أسئلة الاستبيان
        $questions = $survey->questions;
        $responses = $request->responses;

        foreach ($questions as $index => $question) {
            $questionKey = "question_{$index}";
            $isRequired = $question['required'] ?? false;

            if ($isRequired && (!isset($responses[$questionKey]) || empty($responses[$questionKey]))) {
                $validator->errors()->add("responses.{$questionKey}", "هذا السؤال مطلوب: {$question['question']}");
            }

            // التحقق من صحة الخيارات للأسئلة ذات الاختيارات
            if (in_array($question['type'], ['radio', 'checkbox', 'select']) && isset($responses[$questionKey])) {
                $allowedOptions = $question['options'] ?? [];
                $userResponse = $responses[$questionKey];

                if (is_array($userResponse)) {
                    foreach ($userResponse as $option) {
                        if (!in_array($option, $allowedOptions)) {
                            $validator->errors()->add("responses.{$questionKey}", "خيار غير صحيح في السؤال: {$question['question']}");
                        }
                    }
                } else {
                    if (!in_array($userResponse, $allowedOptions)) {
                        $validator->errors()->add("responses.{$questionKey}", "خيار غير صحيح في السؤال: {$question['question']}");
                    }
                }
            }

            // التحقق من صحة التقييم
            if ($question['type'] === 'rating' && isset($responses[$questionKey])) {
                $maxRating = $question['max_rating'] ?? 5;
                $rating = (int) $responses[$questionKey];
                if ($rating < 1 || $rating > $maxRating) {
                    $validator->errors()->add("responses.{$questionKey}", "التقييم يجب أن يكون بين 1 و {$maxRating}");
                }
            }

            // التحقق من صحة التاريخ
            if ($question['type'] === 'date' && isset($responses[$questionKey])) {
                $date = $responses[$questionKey];
                if (!strtotime($date)) {
                    $validator->errors()->add("responses.{$questionKey}", "تاريخ غير صحيح في السؤال: {$question['question']}");
                }
            }

            // التحقق من صحة البريد الإلكتروني
            if ($question['type'] === 'email' && isset($responses[$questionKey])) {
                $email = $responses[$questionKey];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add("responses.{$questionKey}", "بريد إلكتروني غير صحيح في السؤال: {$question['question']}");
                }
            }

            // التحقق من صحة الرقم
            if ($question['type'] === 'number' && isset($responses[$questionKey])) {
                $number = $responses[$questionKey];
                if (!is_numeric($number)) {
                    $validator->errors()->add("responses.{$questionKey}", "يجب إدخال رقم صحيح في السؤال: {$question['question']}");
                }
            }
        }

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // حفظ الرد
        SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => auth()->id(),
            'answers' => $responses,
            'participant_name' => $request->participant_name,
            'participant_email' => $request->participant_email,
            'is_external' => !auth()->check(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('public.survey.thankyou', $survey->slug)
                        ->with('success', 'تم إرسال ردك على الاستبيان بنجاح');
    }

    public function thankyou($slug)
    {
        $survey = Survey::where('slug', $slug)->first();

        if (!$survey) {
            abort(404);
        }

        return view('public.surveys.thankyou', compact('survey'));
    }
}
