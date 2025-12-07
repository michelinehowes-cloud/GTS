<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'university',
        'major',
        'degree',
        'graduation_year',
        'gpa',
        'skills',
        'experiences',
        'education',
        'resume_path',
        'is_active',
        'national_id',
        'date_of_birth',
        'gender',
        'city',
        'qualification',
        'specialization',
        'is_approved',
        'approved_at',
        'approved_by',
        'languages',
        'cv_path',
        'must_change_password',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'graduation_year' => 'integer',
        'gpa' => 'decimal:2',
        'skills' => 'array',
        'languages' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'date_of_birth' => 'date',
        'must_change_password' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    /**
     * الأدوار المتاحة في النظام
     */
    public static function getAvailableRoles()
    {
        return [
            'admin' => 'مدير النظام',
            'graduate' => 'خريج',
            'training_coordinator' => 'منسق التدريب',
            'partnership_officer' => 'مسؤول الشراكات والتوظيف',
            'career_guidance_officer' => 'مسؤول الإرشاد المهني',
            'evaluation_followup' => 'مسؤول التقييم والمتابعة',
            'media_officer' => 'مسؤول الميديا',
            'company' => 'شركة'
        ];
    }

    /**
     * التحقق إذا كان المستخدم مدير نظام
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * التحقق إذا كان المستخدم خريج
     */
    public function isGraduate()
    {
        return $this->role === 'graduate';
    }

    /**
     * التحقق إذا كان المستخدم منسق تدريب
     */
    public function isTrainingCoordinator()
    {
        return $this->role === 'training_coordinator';
    }

    /**
     * التحقق إذا كان المستخدم مسؤول الشراكات والتوظيف
     */
    public function isPartnershipOfficer()
    {
        return $this->role === 'partnership_officer';
    }

    /**
     * التحقق إذا كان المستخدم مسؤول الإرشاد المهني
     */
    public function isCareerGuidanceOfficer()
    {
        return $this->role === 'career_guidance_officer';
    }

    /**
     * التحقق إذا كان المستخدم شركة
     */
    public function isCompany()
    {
        return $this->role === 'company';
    }

    /**
     * التحقق إذا كان المستخدم مسؤول ميديا
     */
    public function isMediaOfficer()
    {
        return $this->role === 'media_officer';
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة الشركات
     */
    public function canManageCompanies()
    {
        return in_array($this->role, ['admin', 'partnership']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة فرص العمل
     */
    public function canManageJobOpportunities()
    {
        return in_array($this->role, ['admin', 'partnership']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة بيانات الخريجين
     */
    public function canManageGraduates()
    {
        return in_array($this->role, ['admin', 'career_guidance_officer']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة الترشيحات
     */
    public function canManageNominations()
    {
        return in_array($this->role, ['admin', 'career_guidance_officer', 'partnership_officer']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة وثائق الشراكة
     */
    public function canManagePartnershipDocuments()
    {
        return in_array($this->role, ['admin', 'partnership_officer']);
    }

    /**
     * الحصول على اسم الدور بشكل مقروء
     */
    public function getRoleNameAttribute()
    {
        $roles = self::getAvailableRoles();
        return $roles[$this->role] ?? $this->role;
    }

    /**
     * العلاقة مع الشركات (إذا كان المستخدم شركة)
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    /**
     * العلاقة مع فرص العمل التي أنشأها المستخدم
     */
    public function createdJobOpportunities()
    {
        return $this->hasMany(JobOpportunity::class, 'created_by');
    }

    /**
     * العلاقة مع بيانات الخريجين التي أضافها المستخدم
     */
    public function addedGraduates()
    {
        return $this->hasMany(GraduateData::class, 'added_by');
    }

    /**
     * العلاقة مع الترشيحات التي قام بها المستخدم
     */
    public function nominations()
    {
        return $this->hasMany(Nomination::class, 'nominated_by');
    }

    /**
     * العلاقة مع وثائق الشراكة التي رفعها المستخدم
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(PartnershipDocument::class, 'uploaded_by');
    }

    /**
     * العلاقة مع وثائق الشراكة التي راجعها المستخدم
     */
    public function reviewedDocuments()
    {
        return $this->hasMany(PartnershipDocument::class, 'reviewed_by');
    }

    /**
     * الحصول على عدد فرص العمل التي أنشأها المستخدم
     */
    public function getJobOpportunitiesCountAttribute()
    {
        return $this->createdJobOpportunities()->count();
    }

    /**
     * الحصول على عدد الخريجين الذين أضافهم المستخدم
     */
    public function getGraduatesAddedCountAttribute()
    {
        return $this->addedGraduates()->count();
    }

    /**
     * الحصول على عدد الترشيحات التي قام بها المستخدم
     */
    public function getNominationsCountAttribute()
    {
        return $this->nominations()->count();
    }

    /**
     * الحصول على عدد الوثائق التي رفعها المستخدم
     */
    public function getUploadedDocumentsCountAttribute()
    {
        return $this->uploadedDocuments()->count();
    }

    /**
     * البحث عن المستخدمين حسب الدور
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * البحث عن المستخدمين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * البحث عن مسؤولي الشراكات
     */
    public function scopePartnershipOfficers($query)
    {
        return $query->where('role', 'partnership_officer');
    }

    /**
     * البحث عن مسؤولي الإرشاد المهني
     */
    public function scopeCareerGuidanceOfficers($query)
    {
        return $query->where('role', 'career_guidance_officer');
    }
    // app/Models/User.php

    /**
     * التحقق إذا كان المستخدم يمكنه إضافة خريجين
     */
    public function canAddGraduates()
    {
        return in_array($this->role, ['admin', 'career_guidance_officer']);
    }
}
