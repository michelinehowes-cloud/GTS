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
        'slug',
        'is_public',
    ];

    protected $casts = [
        'questions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
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
