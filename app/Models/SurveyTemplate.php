<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'target_audience',
        'type',
        'questions',
        'is_system',
        'created_by',
    ];

    protected $casts = [
        'questions' => 'array',
        'is_system' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeTraining($query)
    {
        return $query->where('category', 'training');
    }

    public function scopeEmployment($query)
    {
        return $query->where('category', 'employment');
    }

    public function scopeEvents($query)
    {
        return $query->where('category', 'events');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'training' => 'التدريب وورش العمل',
            'employment' => 'التوظيف والشراكات',
            'events' => 'المعارض والفعاليات',
            default => 'قالب عام',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'training' => 'emerald',
            'employment' => 'blue',
            'events' => 'amber',
            default => 'purple',
        };
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->target_audience) {
            'graduates' => 'الخريجين',
            'companies' => 'الشركات وأصحاب العمل',
            'training_coordinators' => 'منسقي التدريب',
            default => 'كافة الفئات (الجميع)',
        };
    }
}
