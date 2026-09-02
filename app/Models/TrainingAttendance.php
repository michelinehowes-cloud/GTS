<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'user_id',
        'training_application_id',
        'date',
        'attended_at',
        'recorded_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'attended_at' => 'datetime',
    ];

    /**
     * التدريب المرتبط
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * الخريج الحاضر
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * طلب التدريب المرتبط
     */
    public function application()
    {
        return $this->belongsTo(TrainingApplication::class, 'training_application_id');
    }

    /**
     * المستخدم (المنسق / المدير) الذي سجل الحضور
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * نص الحالة بالعربية
     */
    public function getStatusArabicAttribute()
    {
        return match($this->status) {
            'present' => 'حاضر',
            'late' => 'متأخر',
            'excused' => 'معذور',
            'absent' => 'غائب',
            default => $this->status,
        };
    }

    /**
     * لون شارة الحالة
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'present' => 'bg-success',
            'late' => 'bg-warning text-dark',
            'excused' => 'bg-info text-dark',
            'absent' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
