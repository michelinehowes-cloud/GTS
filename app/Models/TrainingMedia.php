<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'file_path',
        'file_type',
        'caption',
        'uploaded_by',
        'is_welcome_page_media',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_welcome_page_media' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * العلاقة مع التدريب
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * العلاقة مع المستخدم الذي قام بالتحميل
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * نطاق للوسائط النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق لوسائط واجهة الترحيب
     */
    public function scopeWelcomePageMedia($query)
    {
        return $query->where('is_welcome_page_media', true);
    }

    /**
     * نطاق لوسائط التدريبات
     */
    public function scopeTrainingMedia($query)
    {
        return $query->whereNotNull('training_id');
    }

    /**
     * الحصول على نوع الملف بالعربية
     */
    public function getFileTypeArabicAttribute()
    {
        return $this->file_type === 'image' ? 'صورة' : 'فيديو';
    }

    /**
     * الحصول على رابط الملف الكامل
     */
    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
