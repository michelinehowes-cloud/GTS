<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobFairVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'name',
        'phone',
        'email',
        'visitor_type',
        'education_level',
        'specialization',
        'organization',
        'city',
        'visit_purpose',
        'ticket_number',
        'qr_code',
        'attended',
        'check_in_at',
        'notes',
    ];

    protected $casts = [
        'attended' => 'boolean',
        'check_in_at' => 'datetime',
    ];

    public function jobFair(): BelongsTo
    {
        return $this->belongsTo(JobFair::class);
    }

    public function getVisitorTypeLabelAttribute(): string
    {
        return match ($this->visitor_type) {
            'student' => 'طالب جامعي',
            'job_seeker' => 'باحث عن عمل',
            'parent' => 'ولي أمر / عائلة',
            'company_rep' => 'ممثل شركة / جهة عمل',
            'academic' => 'أكاديمي / عضو هيئة تدريس',
            'general' => 'زائر عام',
            default => 'زائر عام',
        };
    }

    public function getVisitorTypeBadgeAttribute(): string
    {
        return match ($this->visitor_type) {
            'student' => 'bg-info text-white',
            'job_seeker' => 'bg-primary text-white',
            'parent' => 'bg-secondary text-white',
            'company_rep' => 'bg-success text-white',
            'academic' => 'bg-warning text-dark',
            default => 'bg-dark text-white',
        };
    }

    public function getEducationLevelLabelAttribute(): string
    {
        return match ($this->education_level) {
            'high_school' => 'ثانوي',
            'diploma' => 'دبلوم',
            'bachelor' => 'بكالوريوس',
            'master' => 'ماجستير',
            'phd' => 'دكتوراه',
            default => $this->education_level ?: '—',
        };
    }

    public static function purposeOptions(): array
    {
        return [
            'explore_jobs' => 'البحث عن فرص عمل وتدريب بأجنحة الشركات',
            'view_projects' => 'الاطلاع على مشاريع تخرج الطلاب والابتكارات',
            'networking' => 'بناء علاقات وشراكات مهنية مع الشركات',
            'attend_workshops' => 'حضور ورش العمل والجلسات الحوارية',
            'support_graduates' => 'تشجيع ومساندة الخريجين',
            'general_visit' => 'زيارة استكشافية عامة للفعاليات',
            'visit_booths' => 'التعرف على الشركات العارضة',
            'support_graduate' => 'مرافقة ودعم أحد الخريجين',
            'general' => 'زيارة واستطلاع عام',
        ];
    }

    public function getVisitPurposesArrayAttribute(): array
    {
        if (empty($this->visit_purpose)) {
            return [];
        }

        if (is_array($this->visit_purpose)) {
            return $this->visit_purpose;
        }

        if (is_string($this->visit_purpose)) {
            $decoded = json_decode($this->visit_purpose, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            if (str_contains($this->visit_purpose, ',')) {
                return array_map('trim', explode(',', $this->visit_purpose));
            }

            return [$this->visit_purpose];
        }

        return [];
    }

    public function getVisitPurposeLabelAttribute(): string
    {
        $purposes = $this->visit_purposes_array;
        if (empty($purposes)) {
            return 'زيارة واستكشاف المعرض';
        }

        $options = self::purposeOptions();
        $labels = [];

        foreach ($purposes as $p) {
            $labels[] = $options[$p] ?? $p;
        }

        return implode('، ', $labels);
    }
}
