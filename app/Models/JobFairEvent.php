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
        'speakers',
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
        'speakers'    => 'array',
    ];

    protected $appends = [
        'speakers_list',
        'speakers_names_summary',
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

    public static function resolveSpeakerImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        $path = trim($path);
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }
        return Storage::url($path);
    }

    public static function defaultSpeakerRoleForType(?string $type): string
    {
        return match($type) {
            'panel_discussion' => 'محاور',
            'keynote'          => 'متحدث رئيسي',
            default            => 'مدرب / محاضر',
        };
    }

    public function getSpeakerImageUrlAttribute(): ?string
    {
        if ($this->speaker_image) {
            return static::resolveSpeakerImageUrl($this->speaker_image);
        }

        $raw = $this->speakers;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (is_array($raw)) {
            foreach ($raw as $entry) {
                if (is_array($entry) && !empty($entry['image'])) {
                    return static::resolveSpeakerImageUrl($entry['image']);
                }
            }
        }

        return null;
    }

    /**
     * قائمة المتحدثين والمحاورين الموحدة (تدعم من 1 إلى عدة محاورين مع التوافق الكامل مع الحقول القديمة)
     */
    public function getSpeakersListAttribute(): array
    {
        $raw = $this->speakers;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        $defaultRole = static::defaultSpeakerRoleForType($this->type);
        $list = [];

        if (is_array($raw)) {
            foreach ($raw as $entry) {
                if (!is_array($entry)) {
                    continue;
                }
                $name  = trim((string) ($entry['name'] ?? ''));
                $title = trim((string) ($entry['title'] ?? ''));
                $role  = trim((string) ($entry['role'] ?? ''));
                $bio   = trim((string) ($entry['bio'] ?? ''));
                $image = !empty($entry['image']) ? trim((string) $entry['image']) : null;

                if ($name === '' && $title === '' && $bio === '' && !$image) {
                    continue;
                }

                $list[] = [
                    'name'      => $name,
                    'title'     => $title,
                    'role'      => $role !== '' ? $role : $defaultRole,
                    'bio'       => $bio,
                    'image'     => $image,
                    'image_url' => static::resolveSpeakerImageUrl($image),
                ];
            }
        }

        if (empty($list)) {
            $legacyName  = trim((string) ($this->speaker_name ?? ''));
            $legacyTitle = trim((string) ($this->speaker_title ?? ''));
            $legacyBio   = trim((string) ($this->speaker_bio ?? ''));
            $legacyImage = !empty($this->speaker_image) ? trim((string) $this->speaker_image) : null;

            if ($legacyName !== '' || $legacyTitle !== '' || $legacyBio !== '' || $legacyImage) {
                $list[] = [
                    'name'      => $legacyName,
                    'title'     => $legacyTitle,
                    'role'      => $defaultRole,
                    'bio'       => $legacyBio,
                    'image'     => $legacyImage,
                    'image_url' => static::resolveSpeakerImageUrl($legacyImage),
                ];
            }
        }

        return $list;
    }

    /**
     * ملخص أسماء المتحدثين والمحاورين مفصولة بفاصلة عربية
     */
    public function getSpeakersNamesSummaryAttribute(): string
    {
        $names = array_values(array_filter(array_map(
            fn($sp) => trim((string) ($sp['name'] ?? '')),
            $this->speakers_list
        )));

        if (!empty($names)) {
            return implode(' ، ', $names);
        }

        return trim((string) ($this->speaker_name ?? ''));
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

    /**
     * تحويل الوقت إلى صيغة عربية سهلة القراءة (12 ساعة مع ص/م)
     */
    public static function formatTimeArabic($time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            if ($time instanceof \Carbon\CarbonInterface) {
                $carbon = $time;
            } else {
                $carbon = \Carbon\Carbon::parse($time);
            }
            $hour = $carbon->format('h:i');
            $ampm = $carbon->format('A') === 'PM' ? 'م' : 'ص';
            return "{$hour} {$ampm}";
        } catch (\Throwable $e) {
            return is_string($time) ? $time : null;
        }
    }

    public function getFormattedStartTimeAttribute(): ?string
    {
        return static::formatTimeArabic($this->start_time);
    }

    public function getFormattedEndTimeAttribute(): ?string
    {
        return static::formatTimeArabic($this->end_time);
    }

    /**
     * نص توقيت الفعالية (مثال: من 10:00 ص إلى 01:00 م)
     */
    public function getTimingTextAttribute(): ?string
    {
        $start = $this->formatted_start_time;
        $end = $this->formatted_end_time;

        if ($start && $end) {
            return "من {$start} إلى {$end}";
        } elseif ($start) {
            return "يبدأ الساعة {$start}";
        } elseif ($end) {
            return "حتى {$end}";
        }

        return null;
    }

    /**
     * نص توقيت مختصر للبطاقات وتقويم الفعاليات (مثال: 10:00 ص - 01:00 م)
     */
    public function getCompactTimingTextAttribute(): ?string
    {
        $start = $this->formatted_start_time;
        $end = $this->formatted_end_time;

        if ($start && $end) {
            return "{$start} - {$end}";
        } elseif ($start) {
            return "{$start}";
        } elseif ($end) {
            return "حتى {$end}";
        }

        return null;
    }

    /**
     * نص التوقيت والجدول الزمني الكامل شاملاً التاريخ والساعة
     */
    public function getFullScheduleTextAttribute(): string
    {
        $dateText = $this->start_time ? $this->start_time->locale('ar')->isoFormat('dddd، D MMMM YYYY') : '';
        $timing = $this->timing_text;

        if ($dateText && $timing) {
            return "{$dateText} ({$timing})";
        }

        return $dateText ?: ($timing ?: 'يحدد لاحقاً وفق جدول المعرض');
    }
}
