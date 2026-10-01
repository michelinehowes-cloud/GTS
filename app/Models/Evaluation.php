<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Keeping it for now but will be nullable or handled
        'evaluator_id',
        'training_id',
        'survey_response_id',
        'type', // 'training', 'employment', 'performance'
        'facilities_evaluation',
        'content_evaluation',
        'trainer_evaluation', // This might be used for single trainer or general comment, but we use TrainerEvaluation for specific trainers
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function evaluatable()
    {
        return $this->morphTo();
    }

    public function trainerEvaluations()
    {
        return $this->hasMany(TrainerEvaluation::class);
    }

    /**
     * حساب متوسط الدرجة التقييمية لضمان عمل الإحصائيات بدقة
     */
    public function getAverageScoreAttribute(): float
    {
        if (!empty($this->score) && (float)$this->score > 0) {
            return (float)$this->score;
        }

        $allRatings = [];
        $sources = [
            $this->content_evaluation,
            $this->trainer_evaluation,
            $this->facilities_evaluation,
            $this->organization_evaluation,
            $this->impact_evaluation,
            $this->criteria_scores,
        ];

        foreach ($sources as $source) {
            if (is_array($source)) {
                foreach ($source as $val) {
                    if (is_numeric($val) && (float)$val > 0) {
                        $allRatings[] = (float)$val;
                    }
                }
            }
        }

        return count($allRatings) > 0 ? round(array_sum($allRatings) / count($allRatings), 2) : 0.0;
    }
}
