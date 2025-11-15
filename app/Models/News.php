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
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
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
     * نطاق للأخبار المنشورة
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * الحصول على رابط الصورة المصغرة الكامل
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * الحصول على ملخص المحتوى
     */
    public function getExcerptAttribute()
    {
        return strlen($this->content) > 150 ? substr($this->content, 0, 150) . '...' : $this->content;
    }
}
