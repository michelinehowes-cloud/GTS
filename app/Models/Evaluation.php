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
        'type', // 'performance', 'training', 'company'
        'scores',
        'comments',
        'recommendations',
        'evaluation_date',
        'status',
    ];

    protected $casts = [
        'scores' => 'array',
        'evaluation_date' => 'date',
    ];

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

    public function getAverageScoreAttribute(): float
    {
        if (!$this->scores) return 0;

        $scores = is_array($this->scores) ? $this->scores : json_decode($this->scores, true);
        if (!$scores) return 0;

        return round(array_sum($scores) / count($scores), 2);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
