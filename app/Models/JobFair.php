<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobFair extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'hall_map',
        'banner_image',
        'max_graduates',
        'max_companies',
        'status',
        'survey_id',
        'registration_open',
        'registration_deadline',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'event_date'            => 'date',
        'registration_deadline' => 'datetime',
        'registration_open'     => 'boolean',
    ];

    // ========== العلاقات ==========

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function companies()
    {
        return $this->hasMany(JobFairCompany::class);
    }

    public function registrations()
    {
        return $this->hasMany(JobFairRegistration::class);
    }

    public function registeredGraduates()
    {
        return $this->belongsToMany(User::class, 'job_fair_registrations')
                    ->withPivot(['qr_code', 'registration_number', 'attended', 'check_in_at', 'status', 'interests'])
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(JobFairEvent::class);
    }

    // ========== Accessors ==========

    /**
     * هل المعرض قادم قريباً؟
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->event_date >= Carbon::today();
    }

    /**
     * عدد الأيام المتبقية
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, Carbon::today()->diffInDays($this->event_date, false));
    }

    /**
     * عدد الخريجين المسجلين
     */
    public function getRegistrationsCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    /**
     * عدد الشركات المشاركة
     */
    public function getCompaniesCountAttribute(): int
    {
        return $this->companies()->where('status', 'confirmed')->count();
    }

    /**
     * هل التسجيل لا يزال متاحاً؟
     */
    public function getCanRegisterAttribute(): bool
    {
        if (!$this->registration_open) return false;
        if ($this->registration_deadline && Carbon::now()->gt($this->registration_deadline)) return false;
        if ($this->max_graduates && $this->registrations_count >= $this->max_graduates) return false;
        return in_array($this->status, ['published', 'ongoing']);
    }
}
