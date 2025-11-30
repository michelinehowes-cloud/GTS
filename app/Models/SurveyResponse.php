<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'answers',  // Changed from 'responses'
        'submitted_at',
        'participant_email',
        'participant_name',
        'is_external',
    ];

    protected $casts = [
        'answers' => 'array',  // Changed from 'responses'
        'submitted_at' => 'datetime',
        'is_external' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBySurvey($query, $surveyId)
    {
        return $query->where('survey_id', $surveyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExternal($query)
    {
        return $query->where('is_external', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_external', false);
    }
}
