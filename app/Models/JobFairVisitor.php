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

    public function getVisitPurposeLabelAttribute(): string
    {
        return match ($this->visit_purpose) {
            'explore_jobs' => 'استكشاف فرص العمل والتدريب',
            'visit_booths' => 'التعرف على الشركات العارضة',
            'attend_workshops' => 'حضور الندوات والبرنامج العلمي',
            'support_graduate' => 'مرافقة ودعم أحد الخريجين',
            'general' => 'زيارة واستطلاع عام',
            default => $this->visit_purpose ?: 'زيارة عامة',
        };
    }
}
