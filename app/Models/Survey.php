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

    // ملاحظة: نوع الاستبيان (type) عمود في جدول surveys وليس في الجداول المرتبطة،
    // لذا لا يجوز إضافة where('type') على العلاقة (كان يسبب خطأ 500 في MySQL لعدم وجود العمود في job_fairs).
    public function training()
    {
        return $this->belongsTo(Training::class, 'related_id');
    }

    public function jobOpportunity()
    {
        return $this->belongsTo(JobOpportunity::class, 'related_id');
    }

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class, 'related_id');
    }

    /**
     * القيم المقبولة للجمهور المستهدف لكل فئة (تشمل الصيغ العربية والمفرد احتياطاً)
     */
    public static function audienceValuesFor(string $audience): array
    {
        $all = ['all', 'الكل', 'الجميع'];

        return match ($audience) {
            'graduates' => array_merge($all, ['graduates', 'graduate', 'الخريجين', 'الخريجون']),
            'companies' => array_merge($all, ['companies', 'company', 'الشركات']),
            default     => $all,
        };
    }

    /**
     * تقييد الاستبيانات بحسب الجمهور المستهدف (graduates | companies)
     */
    public function scopeForAudience($query, string $audience)
    {
        $values = self::audienceValuesFor($audience);

        return $query->where(function ($q) use ($values, $audience) {
            $q->whereIn('target_audience', $values);
            // الاستبيانات القديمة بدون جمهور محدد تُعتبر للخريجين فقط
            if ($audience === 'graduates') {
                $q->orWhereNull('target_audience')->orWhere('target_audience', '');
            }
        });
    }

    public function isForAudience(string $audience): bool
    {
        $target = trim((string) $this->target_audience);
        if ($target === '') {
            return $audience === 'graduates';
        }
        return in_array($target, self::audienceValuesFor($audience), true);
    }

    public function getRelatedEntityAttribute()
    {
        if ($this->type === 'training') {
            return \App\Models\Training::find($this->related_id);
        } elseif ($this->type === 'job_opportunity') {
            return \App\Models\JobOpportunity::find($this->related_id); // Adjust model name if needed
        } elseif ($this->type === 'job_fair') {
            return \App\Models\JobFair::find($this->related_id);
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
