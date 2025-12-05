<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'day_number',
        'content_evaluation',
        'trainer_evaluation',
        'overall_rating',
        'comments',
    ];

    protected $casts = [
        'content_evaluation' => 'array',
        'trainer_evaluation' => 'array',
        'overall_rating' => 'decimal:2',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
