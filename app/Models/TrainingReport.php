<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_name',
        'report_type',
        'file_path',
        'file_name',
        'file_size',
        'description',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على لون النوع
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'training_programs' => 'primary',
            'training_applications' => 'success',
            'student_data' => 'info',
            'attendance' => 'warning',
            'evaluation' => 'danger'
        ];

        return $colors[$this->report_type] ?? 'secondary';
    }

    /**
     * الحصول على تسمية النوع
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'training_programs' => 'برامج التدريب',
            'training_applications' => 'طلبات التدريب',
            'student_data' => 'بيانات الطلاب',
            'attendance' => 'سجلات الحضور',
            'evaluation' => 'تقييمات التدريب'
        ];

        return $labels[$this->report_type] ?? $this->report_type;
    }

    /**
     * الحصول على اسم التقرير
     */
    public function getReportNameAttribute($value)
    {
        return $value ?: 'تقرير بدون عنوان';
    }
}