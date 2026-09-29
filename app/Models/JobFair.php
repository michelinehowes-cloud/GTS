<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property \Illuminate\Support\Carbon|null $registration_deadline
 */
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
        'is_program_published',
        'is_projects_published',
        'registration_deadline',
        'notes',
        'media_kit_path',
        'brand_guidelines_path',
        'fair_logo_path',
        'fair_logo_white_path',
        'fair_logo_horizontal_path',
        'sponsors_logos_path',
        'media_kit_description',
        'created_by',
    ];

    protected $casts = [
        'event_date'            => 'date',
        'registration_deadline' => 'datetime',
        'registration_open'     => 'boolean',
        'is_program_published'  => 'boolean',
        'is_projects_published' => 'boolean',
    ];

    // ========== العلاقات ==========

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sponsors()
    {
        return $this->hasMany(JobFairSponsor::class)->orderBy('display_order')->where('is_active', true);
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

    public function projects()
    {
        return $this->hasMany(JobFairProject::class);
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
     * هل المعرض منتهي الصلاحية أو انقضت مدته؟
     */
    public function getIsEndedAttribute(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return true;
        }

        if ($this->event_date) {
            $eventDate = Carbon::parse($this->event_date);
            $endDateTime = $this->end_time 
                ? Carbon::parse($eventDate->format('Y-m-d') . ' ' . $this->end_time)
                : $eventDate->copy()->endOfDay();

            return Carbon::now()->gt($endDateTime);
        }

        return false;
    }

    /**
     * هل المعرض جاري أو قادم ويمكن التفاعل معه؟
     */
    public function getIsActiveAttribute(): bool
    {
        return !$this->is_ended && in_array($this->status, ['published', 'ongoing']);
    }

    /**
     * رابط الشعار الرئيسي للمعرض
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->fair_logo_path) {
            if (str_starts_with($this->fair_logo_path, 'http://') || str_starts_with($this->fair_logo_path, 'https://')) {
                return $this->fair_logo_path;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->fair_logo_path)) {
                return \Illuminate\Support\Facades\Storage::url($this->fair_logo_path);
            }
            if (file_exists(public_path($this->fair_logo_path))) {
                return asset($this->fair_logo_path);
            }
            if (file_exists(public_path('storage/' . $this->fair_logo_path))) {
                return asset('storage/' . $this->fair_logo_path);
            }
        }

        if ($this->banner_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->banner_image)) {
            return \Illuminate\Support\Facades\Storage::url($this->banner_image);
        }

        // استخدام الشعار الافتراضي فقط لمعرض التوظيف أو المعرض رقم 1
        if ($this->id === 1 || str_contains($this->title, 'معرض التوظيف')) {
            if (file_exists(public_path('images/job_fair_logo.png'))) {
                return asset('images/job_fair_logo.png');
            }
        }

        return null;
    }

    /**
     * رابط الشعار الأبيض المخصص للخلفيات الداكنة
     */
    public function getWhiteLogoUrlAttribute(): ?string
    {
        if ($this->fair_logo_white_path) {
            if (str_starts_with($this->fair_logo_white_path, 'http://') || str_starts_with($this->fair_logo_white_path, 'https://')) {
                return $this->fair_logo_white_path;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->fair_logo_white_path)) {
                return \Illuminate\Support\Facades\Storage::url($this->fair_logo_white_path);
            }
            if (file_exists(public_path($this->fair_logo_white_path))) {
                return asset($this->fair_logo_white_path);
            }
        }

        // إذا كان المعرض هو معرض التوظيف 2026 أو رقم 1، نستخدم الشعار الأبيض المخصص له
        if ($this->id === 1 || str_contains($this->title, 'معرض التوظيف')) {
            if (file_exists(public_path('images/job_fair_logo_white.png'))) {
                return asset('images/job_fair_logo_white.png');
            }
        }

        // إذا كان لأي معرض آخر شعاره الخاص، نستخدمه
        if ($this->logo_url) {
            return $this->logo_url;
        }

        return null;
    }

    /**
     * رابط الشعار الأفقي للهيدر وأشرطة التصفح
     */
    public function getHorizontalLogoUrlAttribute(): ?string
    {
        if ($this->fair_logo_horizontal_path) {
            if (str_starts_with($this->fair_logo_horizontal_path, 'http://') || str_starts_with($this->fair_logo_horizontal_path, 'https://')) {
                return $this->fair_logo_horizontal_path;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->fair_logo_horizontal_path)) {
                return \Illuminate\Support\Facades\Storage::url($this->fair_logo_horizontal_path);
            }
            if (file_exists(public_path($this->fair_logo_horizontal_path))) {
                return asset($this->fair_logo_horizontal_path);
            }
        }

        if ($this->id === 1 || str_contains($this->title, 'معرض التوظيف')) {
            if (file_exists(public_path('images/job_fair_logo_horizontal.png'))) {
                return asset('images/job_fair_logo_horizontal.png');
            }
        }

        if ($this->logo_url) {
            return $this->logo_url;
        }

        return null;
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

