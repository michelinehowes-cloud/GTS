<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobFairEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'title',
        'type',
        'description',
        'topics',
        'speaker_name',
        'speaker_title',
        'speaker_bio',
        'speaker_image',
        'start_time',
        'end_time',
        'location',
        'target_audience',
        'capacity',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    public function attendees()
    {
        return $this->hasMany(JobFairEventAttendee::class, 'job_fair_event_id');
    }

    // ========== Accessors & Helpers ==========

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'masterclass'      => 'ماستر كلاس (Masterclass)',
            'workshop'         => 'ورشة عمل تطبيقية',
            'panel_discussion' => 'جلسة حوارية',
            'keynote'          => 'كلمة افتتاحية / رئيسية',
            default            => 'فعالية علمية',
        };
    }

    public function getTypeShortLabelAttribute(): string
    {
        return match($this->type) {
            'masterclass'      => 'ماستر كلاس',
            'workshop'         => 'ورشة عمل',
            'panel_discussion' => 'جلسة حوارية',
            'keynote'          => 'جلسة رئيسية',
            default            => 'فعالية',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'masterclass'      => 'fas fa-graduation-cap',
            'workshop'         => 'fas fa-laptop-code',
            'panel_discussion' => 'fas fa-comments',
            'keynote'          => 'fas fa-microphone-alt',
            default            => 'fas fa-chalkboard-teacher',
        };
    }

    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->capacity) {
            return null; // Unlimited seats
        }
        $count = $this->attendees()->count();
        return max(0, $this->capacity - $count);
    }

    public function getAttendeesCountAttribute(): int
    {
        return $this->attendees()->count();
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }
        if (!$this->capacity) {
            return false;
        }
        return $this->attendees()->count() >= $this->capacity;
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'ended' || ($this->end_time && $this->end_time->isPast())) {
            return 'ended';
        }
        if ($this->is_full) {
            return 'completed';
        }
        return $this->status ?: 'open';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->effective_status) {
            'open'      => 'مفتوح للتسجيل',
            'upcoming'  => 'قريباً',
            'completed' => 'مكتمل المقاعد',
            'ended'     => 'انتهت الفعالية',
            default     => 'متاح',
        };
    }

    public function getSpeakerImageUrlAttribute(): ?string
    {
        if ($this->speaker_image) {
            if (str_starts_with($this->speaker_image, 'http')) {
                return $this->speaker_image;
            }
            return Storage::url($this->speaker_image);
        }
        return null;
    }

    public function getShareUrlAttribute(): string
    {
        return route('job-fair.public.events.show', $this->id);
    }

    public function getQrCodeUrlAttribute(): string
    {
        $url = urlencode($this->share_url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={$url}";
    }

    public function getTopicsListAttribute(): array
    {
        if (!$this->topics) {
            return [];
        }
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->topics))));
    }
}
