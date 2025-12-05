<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'trainer_id',
        'training_id',
        'evaluator_id',
        'scores',
        'rating',
        'strengths',
        'weaknesses',
        'recommendations',
        'notes',
    ];

    /**
     * Accessor for comments to alias notes
     */
    public function getCommentsAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

    /**
     * Mutator for comments to alias notes
     */
    public function setCommentsAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }

    protected $casts = [
        'scores' => 'array',
        'rating' => 'decimal:2',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    // Accessors & Mutators
    public function getAverageScoreAttribute(): float
    {
        if (!$this->scores)
            return 0;

        $scores = $this->scores;
        if (!$scores || !is_array($scores) || count($scores) === 0)
            return 0;

        return round(array_sum($scores) / count($scores), 2);
    }
}
