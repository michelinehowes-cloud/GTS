<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'industry',
        'address',
        'website',
        'description',
        'logo_path',
        'is_approved',
        'partnership_type',
        'partnership_types',
        'partnership_status',
        'partnership_notes',
        'rejection_notes',
        'partnership_start_date',
        'partnership_end_date',
        'contact_person',
        'contact_position',
        'contact_phone',
        'contact_email'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'partnership_start_date' => 'date',
        'partnership_end_date' => 'date',
        'partnership_types' => 'array',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع التدريبات
     */
    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
    /**
     * العلاقة مع فرص العمل - ✅ إضافة هذه العلاقة
     */
    public function jobOpportunities()
    {
        return $this->hasMany(JobOpportunity::class);
    }

    /**
     * العلاقة مع وثائق الشراكة - ✅ إضافة هذه العلاقة
     */
    public function partnershipDocuments()
    {
        return $this->hasMany(PartnershipDocument::class);
    }

    /**
     * نطاق الشركات الموافق عليها
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * نطاق الشركات غير الموافق عليها
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
/**
     * الحصول على عدد فرص العمل - ✅ إضافة هذه الدالة
     */
    public function getJobOpportunitiesCountAttribute()
    {
        return $this->jobOpportunities()->count();
    }

    /**
     * الحصول على عدد وثائق الشراكة - ✅ إضافة هذه الدالة
     */
    public function getPartnershipDocumentsCountAttribute()
    {
        return $this->partnershipDocuments()->count();
    }

    /**
     * قاموس مسميات أنواع الشراكة المعتمدة
     */
    public static function partnershipTypeLabels(): array
    {
        return [
            'employment' => 'توظيف',
            'training' => 'تدريب ميداني',
            'logistic_support' => 'رعاية ودعم لوجستي',
            'academic' => 'تعاون أكاديمي وبحثي',
            'workshops' => 'ورش عمل وندوات',
            'training_employment' => 'تدريب + توظيف',
        ];
    }

    /**
     * قائمة أنواع الشراكة مصفوفة (مع الحفاظ على التوافقية)
     */
    public function getPartnershipTypesListAttribute(): array
    {
        if (!empty($this->partnership_types) && is_array($this->partnership_types)) {
            return $this->partnership_types;
        }

        if (!empty($this->partnership_type)) {
            if ($this->partnership_type === 'training_employment') {
                return ['training', 'employment'];
            }
            return [$this->partnership_type];
        }

        return [];
    }

    /**
     * مسميات أنواع الشراكة بالعربية
     */
    public function getPartnershipTypesLabelsAttribute(): array
    {
        $all = self::partnershipTypeLabels();
        $list = $this->partnership_types_list;

        return array_map(function ($type) use ($all) {
            return $all[$type] ?? $type;
        }, $list);
    }

    /**
     * نطاق الشركات قيد المراجعة
     */
    public function scopeUnderReview($query)
    {
        return $query->where('is_approved', false)->orWhere('partnership_status', 'under_review');
    }
}