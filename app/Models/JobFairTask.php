<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'user_id',
        'supervisor_id',
        'team_name',
        'task_description',
        'start_date',
        'due_date',
        'completed_date',
        'evaluation_score',
        'status',
        'notes',
        'signature_name',
        'signed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
        'signed_at' => 'datetime',
    ];

    /**
     * الموظف المكلف بالمهمة
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * مسؤول الفريق / المشرف
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * معرض التوظيف المرتبط
     */
    public function jobFair()
    {
        return $this->belongsTo(JobFair::class, 'job_fair_id');
    }

    /**
     * نص الحالة بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'مكتملة بنجاح',
            'in_progress' => 'قيد التنفيذ والمتابعة',
            'delayed' => 'متأخرة عن الجدول',
            'pending' => 'بانتظار البدء',
            default => 'قيد التنفيذ',
        };
    }

    /**
     * شارة الحالة
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'badge bg-success',
            'in_progress' => 'badge bg-primary',
            'delayed' => 'badge bg-danger',
            'pending' => 'badge bg-warning text-dark',
            default => 'badge bg-secondary',
        };
    }
}
