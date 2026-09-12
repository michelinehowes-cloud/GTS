<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'start_date',
        'end_date',
        'link',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ الإعلان
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * نطاق للإعلانات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق للإعلانات الحالية (بين تاريخ البدء والانتهاء)
     */
    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())->where('end_date', '>=', now());
    }

    /**
     * نطاق للإعلانات المستقبلية
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * نطاق للإعلانات المنتهية
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * التحقق إذا كان الإعلان نشطاً حالياً
     */
    public function getIsCurrentAttribute()
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }

    /**
     * طريقة للتحقق إذا كان الإعلان نشطاً حالياً
     */
    public function isCurrent()
    {
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }

    /**
     * التحقق مما إذا كان الإعلان منتهياً
     */
    public function isExpired()
    {
        return $this->end_date && now()->gt($this->end_date);
    }

    /**
     * التحقق مما إذا كان الإعلان قادماً (مجدولاً للمستقبل)
     */
    public function isUpcoming()
    {
        return $this->start_date && now()->lt($this->start_date);
    }

    /**
     * الحصول على بيانات الشارة والحالة للتصميم المعتمد
     */
    public function getStatusDataAttribute(): array
    {
        if (!$this->is_active) {
            return [
                'label' => 'معطل / مسودة',
                'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                'badge' => 'secondary',
                'icon' => 'fas fa-pause-circle'
            ];
        }

        if ($this->isExpired()) {
            return [
                'label' => 'منتهي الصلاحية',
                'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                'badge' => 'danger',
                'icon' => 'fas fa-history'
            ];
        }

        if ($this->isUpcoming()) {
            return [
                'label' => 'مجدول وقادم',
                'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                'badge' => 'warning',
                'icon' => 'fas fa-clock'
            ];
        }

        return [
            'label' => 'ساري ونشط',
            'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'badge' => 'success',
            'icon' => 'fas fa-bullhorn'
        ];
    }

    /**
     * الحصول على ملخص المحتوى
     */
    public function getExcerptAttribute()
    {
        $clean = strip_tags($this->content);
        return mb_strlen($clean) > 120 ? mb_substr($clean, 0, 120) . '...' : $clean;
    }
}
