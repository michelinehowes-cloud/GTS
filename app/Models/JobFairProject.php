<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobFairProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'title',
        'faculty',
        'department',
        'graduation_year',
        'academic_year',
        'supervisor_name',
        'supervisor_title',
        'team_members',
        'summary',
        'objectives',
        'description',
        'poster_image',
        'cover_image',
        'gallery_images',
        'video_url',
        'project_url',
        'attachments',
        'booth_number',
        'status',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
        'team_members'    => 'array',
        'gallery_images'  => 'array',
        'attachments'     => 'array',
        'is_featured'     => 'boolean',
        'views_count'     => 'integer',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    // ========== Accessors & Helpers ==========

    public function getPosterUrlAttribute(): ?string
    {
        if ($this->poster_image) {
            if (str_starts_with($this->poster_image, 'http')) {
                return $this->poster_image;
            }
            return Storage::url($this->poster_image);
        }
        return null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            return Storage::url($this->cover_image);
        }
        return $this->poster_url;
    }

    public function getShareUrlAttribute(): string
    {
        return route('job-fair.public.projects.show', $this->id);
    }

    public function getQrCodeUrlAttribute(): string
    {
        $url = urlencode($this->share_url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data={$url}";
    }

    public function getTeamListAttribute(): array
    {
        $members = $this->team_members;
        if (is_array($members)) {
            return $members;
        }

        $raw = is_string($members) ? $members : (string)($this->attributes['team_members'] ?? '');
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            // fallback: split lines
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: []));
            return array_map(fn($line) => ['name' => $line], $lines);
        }
        return [];
    }

    public function getGalleryListAttribute(): array
    {
        if (is_array($this->gallery_images)) {
            return array_map(function($img) {
                if (str_starts_with($img, 'http')) return $img;
                return Storage::url($img);
            }, $this->gallery_images);
        }
        return [];
    }

    public function getAttachmentsListAttribute(): array
    {
        if (is_array($this->attachments)) {
            return $this->attachments;
        }
        return [];
    }

    public function getFacultyIconAttribute(): string
    {
        return match(true) {
            str_contains($this->faculty, 'تقنية') || str_contains($this->faculty, 'معلومات') || str_contains($this->department, 'برمجيات') || str_contains($this->department, 'حاسوب') => 'fas fa-laptop-code',
            str_contains($this->faculty, 'هندسة') => 'fas fa-cogs',
            str_contains($this->faculty, 'صيدلة') || str_contains($this->faculty, 'طب') => 'fas fa-pills',
            str_contains($this->faculty, 'علوم') => 'fas fa-flask',
            str_contains($this->faculty, 'اقتصاد') || str_contains($this->faculty, 'تجارة') => 'fas fa-chart-line',
            str_contains($this->faculty, 'فنون') || str_contains($this->faculty, 'إعلام') => 'fas fa-paint-brush',
            default => 'fas fa-graduation-cap',
        };
    }

    public function getFacultyColorClassAttribute(): string
    {
        return match(true) {
            str_contains($this->faculty, 'تقنية') => 'badge-it',
            str_contains($this->faculty, 'هندسة') => 'badge-engineering',
            str_contains($this->faculty, 'صيدلة') => 'badge-pharmacy',
            str_contains($this->faculty, 'علوم') => 'badge-science',
            str_contains($this->faculty, 'اقتصاد') => 'badge-economics',
            default => 'badge-default',
        };
    }
}
