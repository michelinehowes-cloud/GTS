<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'thumbnail_path',
        'published_at',
        'expires_at',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ الخبر
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * نطاق للأخبار النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق للأخبار المنشورة سارية العرض
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * هل انتهت فترة عرض الخبر؟
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * نص توضيحي لفترة وسريان عرض الخبر
     */
    public function getExpiryTextAttribute(): string
    {
        if (!$this->expires_at) {
            return 'عرض دائم ومستمر';
        }
        if ($this->expires_at->isPast()) {
            return 'انتهى في ' . $this->expires_at->format('Y/m/d');
        }
        return 'ينتهي في ' . $this->expires_at->format('Y/m/d H:i');
    }

    /**
     * الحصول على رابط الصورة المصغرة الكامل
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * الحصول على ملخص المحتوى بدون وسوم HTML
     */
    public function getExcerptAttribute()
    {
        $clean = trim(strip_tags($this->content));
        return mb_strlen($clean) > 130 ? mb_substr($clean, 0, 130) . '...' : $clean;
    }

    /**
     * هل الخبر منشور حالياً ومعروض للجمهور؟
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->is_active && $this->published_at && $this->published_at->isPast() && (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * شارة وبيانات حالة النشر
     */
    public function getStatusDataAttribute(): array
    {
        if (!$this->is_active) {
            return [
                'label' => 'مسودة معطلة',
                'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                'icon' => 'fas fa-eye-slash',
            ];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return [
                'label' => 'منتهي العرض (مؤرشف)',
                'class' => 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25',
                'icon' => 'fas fa-hourglass-end',
            ];
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return [
                'label' => 'مجدول للنشر',
                'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                'icon' => 'fas fa-clock',
            ];
        }

        return [
            'label' => 'منشور للعامة',
            'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'icon' => 'fas fa-check-circle',
        ];
    }

    /**
     * وقت القراءة التقديري بالدقائق
     */
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($words / 150));
    }
}
