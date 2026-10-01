<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyAnalyticsService
{
    /**
     * Compute comprehensive statistics for a given survey.
     */
    public function compute(Survey $survey): array
    {
        if (!$survey->relationLoaded('responses')) {
            $survey->load(['responses.user']);
        }

        $responses = $survey->responses;
        $totalResponses = $responses->count();
        $targetCount = $this->getTargetAudienceCount($survey->target_audience);
        $completionRate = $targetCount > 0 ? round(($totalResponses / $targetCount) * 100, 1) : 0;

        $questions = is_array($survey->questions) ? $survey->questions : [];
        $questionsAnalytics = [];
        $allRatingScores = [];

        foreach ($questions as $index => $question) {
            $qType = $question['type'] ?? 'text';
            $qText = $question['question'] ?? "السؤال " . ($index + 1);
            $maxRating = isset($question['max_rating']) && (int)$question['max_rating'] > 0 ? (int)$question['max_rating'] : 5;
            $options = $question['options'] ?? [];

            // Extract all answers for this question
            $answersList = [];
            foreach ($responses as $response) {
                $rawAns = $this->extractAnswer($response, $index, $qText);
                if ($rawAns !== null && $rawAns !== '') {
                    $answersList[] = [
                        'value' => $rawAns,
                        'user_name' => $response->user ? $response->user->name : ($response->participant_name ?? 'مشارك مجهول'),
                        'user_email' => $response->user ? $response->user->email : ($response->participant_email ?? null),
                        'user_role' => $response->user ? $response->user->role : ($response->is_external ? 'مشارك خارجي' : 'زائر'),
                        'date' => $response->submitted_at ?? $response->created_at,
                    ];
                }
            }

            $totalAnswered = count($answersList);
            $answerRate = $totalResponses > 0 ? round(($totalAnswered / $totalResponses) * 100, 1) : 0;

            $itemAnalytics = [
                'index' => $index,
                'question' => $qText,
                'type' => $qType,
                'description' => $question['description'] ?? null,
                'required' => !empty($question['required']),
                'total_answered' => $totalAnswered,
                'answer_rate' => $answerRate,
            ];

            if ($qType === 'rating') {
                $ratings = [];
                $starsCount = [];
                for ($s = 1; $s <= $maxRating; $s++) {
                    $starsCount[$s] = 0;
                }

                foreach ($answersList as $item) {
                    $val = (float)$item['value'];
                    if ($val >= 1 && $val <= $maxRating) {
                        $ratings[] = $val;
                        $rounded = (int)round($val);
                        if (isset($starsCount[$rounded])) {
                            $starsCount[$rounded]++;
                        }
                    }
                }

                $avgScore = count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : 0;
                $pctScore = $maxRating > 0 ? round(($avgScore / $maxRating) * 100, 1) : 0;

                if (count($ratings) > 0) {
                    $allRatingScores[] = ($avgScore / $maxRating) * 5; // normalize to 5 scale
                }

                $distribution = [];
                // Sort stars descending (5, 4, 3, 2, 1)
                for ($s = $maxRating; $s >= 1; $s--) {
                    $cnt = $starsCount[$s];
                    $distribution[$s] = [
                        'stars' => $s,
                        'count' => $cnt,
                        'percentage' => count($ratings) > 0 ? round(($cnt / count($ratings)) * 100, 1) : 0,
                    ];
                }

                $itemAnalytics['max_rating'] = $maxRating;
                $itemAnalytics['average_score'] = $avgScore;
                $itemAnalytics['percentage_score'] = $pctScore;
                $itemAnalytics['ratings_count'] = count($ratings);
                $itemAnalytics['distribution'] = $distribution;

            } elseif (in_array($qType, ['radio', 'select'])) {
                $optCounts = [];
                foreach ($options as $opt) {
                    $optCounts[$opt] = 0;
                }

                foreach ($answersList as $item) {
                    $val = trim((string)$item['value']);
                    if (!isset($optCounts[$val])) {
                        $optCounts[$val] = 0;
                    }
                    $optCounts[$val]++;
                }

                $optBreakdown = [];
                $topChoice = null;
                $topCount = -1;

                foreach ($optCounts as $opt => $cnt) {
                    $pct = $totalAnswered > 0 ? round(($cnt / $totalAnswered) * 100, 1) : 0;
                    $optBreakdown[$opt] = [
                        'option' => $opt,
                        'count' => $cnt,
                        'percentage' => $pct,
                    ];
                    if ($cnt > $topCount) {
                        $topCount = $cnt;
                        $topChoice = $opt;
                    }
                }

                $itemAnalytics['options_breakdown'] = $optBreakdown;
                $itemAnalytics['top_choice'] = $topChoice;
                $itemAnalytics['top_choice_percentage'] = ($totalAnswered > 0 && $topCount > 0) ? round(($topCount / $totalAnswered) * 100, 1) : 0;

                // دعم قياس مؤشر الرضا من أسئلة الاختيار من متعدد ذات المقياس الرتبي
                $scaleMapping = [
                    'راضي جداً' => 5.0,
                    'راضي جدا' => 5.0,
                    'راضي' => 4.0,
                    'إلى حد ما راضي' => 3.0,
                    'الى حد ما راضي' => 3.0,
                    'غير راضي' => 1.5,
                    'غير راضٍ' => 1.5,
                    'غير راضي على الإطلاق' => 1.0,
                    'ممتاز' => 5.0,
                    'جيد جداً' => 4.0,
                    'جيد' => 3.0,
                    'مقبول' => 2.0,
                    'ضعيف' => 1.0,
                    'متوافقة تماماً 90_100%' => 5.0,
                    'متوافقة بشكل كبير 70_80%' => 4.0,
                    'متوافقة الى حد ما 50_60%' => 3.0,
                    'غير متوافقة على الاطلاق اقل من 50%' => 1.5,
                ];

                $scaleHits = [];
                foreach ($answersList as $item) {
                    $cleanVal = trim((string)$item['value']);
                    if (isset($scaleMapping[$cleanVal])) {
                        $scaleHits[] = $scaleMapping[$cleanVal];
                    }
                }
                if (count($scaleHits) >= max(1, count($answersList) * 0.5)) {
                    $avgScale = round(array_sum($scaleHits) / count($scaleHits), 2);
                    $allRatingScores[] = $avgScale;
                    $itemAnalytics['average_score'] = $avgScale;
                    $itemAnalytics['percentage_score'] = round(($avgScale / 5) * 100, 1);
                }

            } elseif ($qType === 'checkbox') {
                $optCounts = [];
                foreach ($options as $opt) {
                    $optCounts[$opt] = 0;
                }

                foreach ($answersList as $item) {
                    $val = $item['value'];
                    $selectedArray = is_array($val) ? $val : (is_string($val) ? explode(',', $val) : [$val]);
                    foreach ($selectedArray as $sel) {
                        $sel = trim((string)$sel);
                        if (!empty($sel)) {
                            if (!isset($optCounts[$sel])) {
                                $optCounts[$sel] = 0;
                            }
                            $optCounts[$sel]++;
                        }
                    }
                }

                $optBreakdown = [];
                $topChoice = null;
                $topCount = -1;

                foreach ($optCounts as $opt => $cnt) {
                    $pct = $totalResponses > 0 ? round(($cnt / $totalResponses) * 100, 1) : 0;
                    $optBreakdown[$opt] = [
                        'option' => $opt,
                        'count' => $cnt,
                        'percentage' => $pct,
                    ];
                    if ($cnt > $topCount) {
                        $topCount = $cnt;
                        $topChoice = $opt;
                    }
                }

                $itemAnalytics['options_breakdown'] = $optBreakdown;
                $itemAnalytics['top_choice'] = $topChoice;

            } elseif (in_array($qType, ['text', 'textarea'])) {
                $itemAnalytics['text_responses'] = array_slice($answersList, 0, 50); // latest 50
                $itemAnalytics['text_responses_count'] = count($answersList);

            } elseif ($qType === 'number') {
                $numbers = [];
                foreach ($answersList as $item) {
                    if (is_numeric($item['value'])) {
                        $numbers[] = (float)$item['value'];
                    }
                }
                $itemAnalytics['average_number'] = count($numbers) > 0 ? round(array_sum($numbers) / count($numbers), 2) : 0;
                $itemAnalytics['min_number'] = count($numbers) > 0 ? min($numbers) : 0;
                $itemAnalytics['max_number'] = count($numbers) > 0 ? max($numbers) : 0;
                $itemAnalytics['numbers_count'] = count($numbers);
            }

            $questionsAnalytics[] = $itemAnalytics;
        }

        $overallSatisfaction = count($allRatingScores) > 0 ? round(array_sum($allRatingScores) / count($allRatingScores), 2) : null;
        $satisfactionPercentage = $overallSatisfaction !== null ? round(($overallSatisfaction / 5) * 100, 1) : null;

        $internalCount = $responses->where('is_external', false)->count();
        $externalCount = $responses->where('is_external', true)->count();

        return [
            'total_responses' => $totalResponses,
            'target_audience_count' => $targetCount,
            'completion_rate' => $completionRate,
            'overall_satisfaction' => $overallSatisfaction,
            'satisfaction_percentage' => $satisfactionPercentage,
            'internal_count' => $internalCount,
            'external_count' => $externalCount,
            'questions_count' => count($questions),
            'questions_analytics' => $questionsAnalytics,
            'first_response_at' => $responses->min('created_at'),
            'last_response_at' => $responses->max('created_at'),
        ];
    }

    /**
     * Helper to safely extract answer from diverse response structures.
     */
    public function extractAnswer($response, int $index, string $qText)
    {
        $answers = $response->answers;
        if (empty($answers)) {
            $answers = $response->responses;
        }

        if (is_string($answers)) {
            $decoded = json_decode($answers, true);
            if (is_array($decoded)) {
                $answers = $decoded;
            }
        }

        if (!is_array($answers)) {
            return null;
        }

        $val = null;

        if (array_key_exists($index, $answers)) {
            $val = $answers[$index];
        } elseif (array_key_exists((string)$index, $answers)) {
            $val = $answers[(string)$index];
        } elseif (array_key_exists("question_$index", $answers)) {
            $val = $answers["question_$index"];
        } elseif (array_key_exists("q_$index", $answers)) {
            $val = $answers["q_$index"];
        } elseif (array_key_exists($qText, $answers)) {
            $val = $answers[$qText];
        }

        if (is_array($val) && array_key_exists('answer', $val)) {
            $val = $val['answer'];
        }

        return $val;
    }

    /**
     * Get count of target audience users.
     */
    public function getTargetAudienceCount($audience): int
    {
        switch ($audience) {
            case 'graduates':
                return User::where('role', 'graduate')->count();
            case 'companies':
                return User::where('role', 'company')->count();
            case 'training_coordinators':
                return User::where('role', 'training_coordinator')->count();
            case 'all':
            default:
                return User::whereIn('role', ['graduate', 'company', 'training_coordinator'])->count();
        }
    }

    /**
     * Export all survey responses as Excel-compatible CSV.
     */
    public function exportResponsesCsv(Survey $survey): StreamedResponse
    {
        $survey->load(['responses.user']);
        $questions = is_array($survey->questions) ? $survey->questions : [];
        $responses = $survey->responses;

        $fileName = 'survey_responses_' . $survey->id . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($survey, $questions, $responses) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel Arabic support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            $headers = ['#', 'اسم المشارك', 'البريد الإلكتروني', 'الصفة / الدور', 'تاريخ المشاركة'];
            foreach ($questions as $qIdx => $q) {
                $headers[] = 'س' . ($qIdx + 1) . ': ' . ($q['question'] ?? "السؤال " . ($qIdx + 1));
            }
            fputcsv($handle, $headers);

            // Data rows
            foreach ($responses as $rowIdx => $response) {
                $userName = $response->user ? $response->user->name : ($response->participant_name ?? 'مشارك مجهول');
                $userEmail = $response->user ? $response->user->email : ($response->participant_email ?? '-');
                $userRole = $response->user ? $response->user->role : ($response->is_external ? 'خارجي' : 'داخلي');
                $date = $response->submitted_at ? $response->submitted_at->format('Y-m-d H:i') : ($response->created_at ? $response->created_at->format('Y-m-d H:i') : '-');

                $row = [$rowIdx + 1, $userName, $userEmail, $userRole, $date];

                foreach ($questions as $qIdx => $q) {
                    $qText = $q['question'] ?? '';
                    $ans = $this->extractAnswer($response, $qIdx, $qText);
                    if (is_array($ans)) {
                        $row[] = implode('، ', $ans);
                    } else {
                        $row[] = $ans !== null ? (string)$ans : '-';
                    }
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
