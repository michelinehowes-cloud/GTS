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
     * الحصول على ملخص المحتوى
     */
    public function getExcerptAttribute()
    {
        return strlen($this->content) > 100 ? substr($this->content, 0, 100) . '...' : $this->content;
    }
}
