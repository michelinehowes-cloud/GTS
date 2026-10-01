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
        return $query->whereIn('category', ['employment', 'partners']);
    }

    public function scopeEvents($query)
    {
        return $query->whereIn('category', ['events', 'internal', 'visitors']);
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
            'partners' => 'تقييم الشركاء والرعاة',
            'events' => 'المعارض والفعاليات',
            'internal' => 'الفريق الداخلي والتنظيم',
            'visitors' => 'زوار الفعاليات والمعارض',
            'general' => 'قالب عام',
            default => 'قالب عام',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'training' => 'emerald',
            'employment', 'partners' => 'blue',
            'events', 'internal', 'visitors' => 'amber',
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
