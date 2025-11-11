<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_opportunity_id',
        'graduate_id',
        'nominated_by',
        'status',
        'nomination_notes',
        'matching_reasons',
        'interview_date',
        'interview_time',
        'interview_location',
        'interview_notes',
        'final_status',
        'company_feedback',
        'graduate_feedback',
        'nominated_at',
        'sent_to_company_at',
        'company_response_at',
        'interview_at',
        'final_decision_at',
        'notified_graduate',
        'notified_company'
    ];

    protected $casts = [
        'interview_date' => 'date',
        'nominated_at' => 'datetime',
        'sent_to_company_at' => 'datetime',
        'company_response_at' => 'datetime',
        'interview_at' => 'datetime',
        'final_decision_at' => 'datetime',
        'notified_graduate' => 'boolean',
        'notified_company' => 'boolean'
    ];

    /**
     * العلاقة مع فرصة العمل
     */
    public function jobOpportunity()
    {
        return $this->belongsTo(JobOpportunity::class);
    }

    /**
     * العلاقة مع بيانات الخريج
     */
    public function graduate()
    {
        return $this->belongsTo(GraduateData::class, 'graduate_id');
    }

    /**
     * العلاقة مع مسؤول الإرشاد المهني الذي رشح
     */
    public function nominator()
    {
        return $this->belongsTo(User::class, 'nominated_by');
    }

    /**
     * التحقق إذا كان الترشيح قيد المراجعة
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * التحقق إذا كان الترشيح مقبولاً
     */
    public function getIsAcceptedAttribute()
    {
        return $this->final_status === 'hired';
    }

    /**
     * التحقق إذا كان الترشيح يحتاج لمقابلة
     */
    public function getNeedsInterviewAttribute()
    {
        return in_array($this->status, ['sent_to_company', 'under_review', 'interview_scheduled']);
    }

    /**
     * الحصول على حالة الترشيح كنص
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد المراجعة',
            'sent_to_company' => 'مرسل للشركة',
            'under_review' => 'قيد الدراسة',
            'interview_scheduled' => 'مقابلة مجدولة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * الحصول على حالة القرار النهائي كنص
     */
    public function getFinalStatusTextAttribute()
    {
        $statuses = [
            'hired' => 'تم التوظيف',
            'not_hired' => 'لم يتم التوظيف',
            'in_progress' => 'قيد المعالجة'
        ];

        return $statuses[$this->final_status] ?? $this->final_status;
    }

    /**
     * البحث عن الترشيحات حسب الفرصة
     */
    public function scopeByOpportunity($query, $opportunityId)
    {
        return $query->where('job_opportunity_id', $opportunityId);
    }

    /**
     * البحث عن الترشيحات حسب الخريج
     */
    public function scopeByGraduate($query, $graduateId)
    {
        return $query->where('graduate_id', $graduateId);
    }

    /**
     * البحث عن الترشيحات حسب الحالة
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * الترشيحات النشطة (غير الملغاة أو المرفوضة)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['rejected', 'withdrawn']);
    }
}