<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'questions',
        'target_audience',
        'start_date',
        'end_date',
        'is_active',
        'type', // Added
        'related_id', // Added
        'slug',
        'is_public',
    ];

    protected $casts = [
        'questions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class, 'related_id')->where('type', 'training');
    }

    public function jobOpportunity()
    {
        return $this->belongsTo(JobOpportunity::class, 'related_id')->where('type', 'job_opportunity');
    }

    public function getRelatedEntityAttribute()
    {
        if ($this->type === 'training') {
            return \App\Models\Training::find($this->related_id);
        } elseif ($this->type === 'job_opportunity') {
            return \App\Models\JobOpportunity::find($this->related_id); // Adjust model name if needed
        }
        return null;
    }

    public function isActive(): bool
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            if (empty($survey->slug)) {
                $survey->slug = Str::slug($survey->title . '-' . Str::random(6));
            }
        });
    }

    public function getPublicUrlAttribute()
    {
        return route('public.survey.show', $this->slug);
    }
}
