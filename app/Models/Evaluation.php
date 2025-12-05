<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluator_id',
        'training_id',
        'survey_response_id',
        'type',
        'facilities_evaluation',
        'content_evaluation',
        'trainer_evaluation',
        'organization_evaluation',
        'impact_evaluation',
        'employment_evaluation',
        'scores',
        'score',
        'overall_rating',
        'comments',
        'strengths',
        'weaknesses',
        'recommendations',
        'evaluation_date',
        'status',
        'evaluation_type',
        'criteria_scores',
        'evaluatable_type',
        'evaluatable_id',
    ];

    protected $casts = [
        'facilities_evaluation' => 'array',
        'content_evaluation' => 'array',
        'trainer_evaluation' => 'array',
        'organization_evaluation' => 'array',
        'impact_evaluation' => 'array',
        'employment_evaluation' => 'array',
        'scores' => 'array',
        'criteria_scores' => 'array',
        'evaluation_date' => 'date',
        'overall_rating' => 'decimal:2',
        'score' => 'decimal:2',
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function surveyResponse(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function evaluatable()
    {
        return $this->morphTo();
    }

    // Accessors & Mutators
    public function getAverageScoreAttribute(): float
    {
        if (!$this->scores)
            return 0;

        $scores = is_array($this->scores) ? $this->scores : json_decode($this->scores, true);
        if (!$scores)
            return 0;

        return round(array_sum($scores) / count($scores), 2);
    }

    public function getOverallRatingAttribute($value): float
    {
        if ($value)
            return (float) $value;

        // حساب التقييم الإجمالي من جميع التقييمات الفرعية
        $ratings = [];

        if ($this->facilities_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->facilities_evaluation);
        }
        if ($this->content_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->content_evaluation);
        }
        if ($this->trainer_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->trainer_evaluation);
        }
        if ($this->organization_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->organization_evaluation);
        }
        if ($this->impact_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->impact_evaluation);
        }
        if ($this->employment_evaluation) {
            $ratings[] = $this->calculateSectionAverage($this->employment_evaluation);
        }

        return $ratings ? round(array_sum($ratings) / count($ratings), 2) : 0;
    }

    private function calculateSectionAverage($section): float
    {
        if (!is_array($section))
            return 0;

        $values = array_filter($section, 'is_numeric');
        return $values ? round(array_sum($values) / count($values), 2) : 0;
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByTraining($query, $trainingId)
    {
        return $query->where('training_id', $trainingId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('evaluation_date', '>=', now()->subDays($days));
    }
}
