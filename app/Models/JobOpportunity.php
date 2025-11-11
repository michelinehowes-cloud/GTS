<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'contract_type',
        'company_id',
        'location',
        'seats',
        'start_date',
        'end_date',
        'application_deadline',
        'required_specializations',
        'required_skills',
        'required_experience',
        'salary',
        'status',
        'benefits',
        'requirements',
        'created_by'
    ];

    protected $casts = [
        'required_specializations' => 'array',
        'required_skills' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'application_deadline' => 'date',
        'salary' => 'decimal:2'
    ];

    /**
     * العلاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ الفرصة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع الترشيحات (سيتم إنشاؤها لاحقاً)
     */
    public function nominations()
    {
        return $this->hasMany(Nomination::class);
    }

    /**
     * الحصول على عدد المتقدمين
     */
    public function getApplicantsCountAttribute()
    {
        return $this->nominations()->count();
    }

    /**
     * التحقق إذا كانت الفرصة مفتوحة للتقديم
     */
    public function getIsOpenForApplicationAttribute()
    {
        return $this->status === 'open' && $this->application_deadline >= now();
    }
    
}