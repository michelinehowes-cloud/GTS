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
        'sector',
        'faculty',
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
     * مزامنة اسم المتدرب في الشهادات تلقائياً عند تغيير الاسم
     */
    protected static function booted(): void
    {
        static::updated(function (User $user) {
            if ($user->isDirty('name') || $user->wasChanged('name')) {
                \App\Models\Certificate::where('user_id', $user->id)
                    ->update(['recipient_name' => $user->name]);
            }
        });
    }

    /**
     * الأدوار المتاحة في النظام
     */
    public static function getAvailableRoles()
    {
        return [
            'admin' => 'مدير النظام',
            'staff' => 'موظف مخصص الصلاحيات (Staff)',
            'training_coordinator' => 'منسق التدريب',
            'partnership_officer' => 'مسؤول الشراكات والتوظيف',
            'career_guidance_officer' => 'مسؤول الإرشاد المهني',
            'evaluation_followup' => 'مسؤول التقييم والمتابعة',
            'media_officer' => 'مسؤول الميديا',
            'company' => 'شركة',
            'graduate' => 'خريج',
        ];
    }

    /**
     * العلاقة مع الصلاحيات الممنوحة للمستخدم
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * التحقق إذا كان المستخدم مدير نظام
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * التحقق إذا كان المستخدم موظف نظام أو إداري
     */
    public function isStaff()
    {
        return $this->role === 'staff' || in_array($this->role, [
            'admin',
            'training_coordinator',
            'partnership_officer',
            'career_guidance_officer',
            'evaluation_followup',
            'media_officer',
        ]);
    }

    /**
     * هل هذا المستخدم هو مدير النظام الأساسي المحمي؟
     */
    public function isProtectedSuperAdmin(): bool
    {
        return $this->id === 1 || ($this->role === 'admin' && $this->email === 'admin@tripoliuniversity.edu.ly');
    }

    /**
     * التحقق من امتلاك المستخدم لصلاحية معينة
     */
    public function hasPermission(string $permission): bool
    {
        // مدير النظام يملك كافة الصلاحيات تلقائياً
        if ($this->isAdmin()) {
            return true;
        }

        // فحص الصلاحيات المحملة في الذاكرة لتجنب استعلامات N+1
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('name', $permission);
        }

        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * التحقق من امتلاك أي من الصلاحيات المحددة
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * التحقق من امتلاك جميع الصلاحيات المحددة
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * منح صلاحية للمستخدم
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        if ($permission && !$this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->attach($permission->id);
            $this->unsetRelation('permissions');
        }
        return $this;
    }

    /**
     * تعيين ومزامنة الصلاحيات للمستخدم (يقبل معرّفات أو كائنات أو أسماء صلاحيات)
     */
    public function syncPermissions(array $permissions)
    {
        $ids = [];
        foreach ($permissions as $p) {
            if (is_numeric($p)) {
                $ids[] = (int) $p;
            } elseif (is_string($p)) {
                $perm = Permission::where('name', $p)->first();
                if ($perm) {
                    $ids[] = $perm->id;
                }
            } elseif ($p instanceof Permission) {
                $ids[] = $p->id;
            }
        }
        $result = $this->permissions()->sync($ids);
        $this->unsetRelation('permissions');
        return $result;
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
        return $this->isAdmin() || $this->hasPermission('companies.view') || in_array($this->role, ['partnership_officer', 'partnership']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة فرص العمل
     */
    public function canManageJobOpportunities()
    {
        return $this->isAdmin() || $this->hasPermission('jobs.manage') || in_array($this->role, ['partnership_officer', 'partnership']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة بيانات الخريجين
     */
    public function canManageGraduates()
    {
        return $this->isAdmin() || $this->hasPermission('graduates.view') || in_array($this->role, ['career_guidance_officer']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة الترشيحات
     */
    public function canManageNominations()
    {
        return $this->isAdmin() || $this->hasPermission('nominations.manage') || in_array($this->role, ['career_guidance_officer', 'partnership_officer', 'company']);
    }

    /**
     * التحقق إذا كان المستخدم يمكنه إدارة وثائق الشراكة
     */
    public function canManagePartnershipDocuments()
    {
        return $this->isAdmin() || $this->hasPermission('partnerships.documents') || in_array($this->role, ['partnership_officer']);
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
     * المسمى العربي الرسمي للدور
     */
    public function getRoleArabicAttribute(): string
    {
        $map = [
            'admin' => 'مدير النظام',
            'partnership_officer' => 'مسؤول الشراكات وعلاقات الشركات ومعرض التوظيف',
            'career_guidance_officer' => 'مسؤول الإرشاد والتوجيه المهني',
            'training_coordinator' => 'منسق التدريب والتأهيل',
            'evaluation_followup' => 'مسؤول التقييم والمتابعة',
            'media_officer' => 'مسؤول الإعلام والتواصل',
            'company' => 'شركة شريكة',
            'graduate' => 'خريج',
            'staff' => 'موظف إداري',
        ];
        return $map[$this->role] ?? ($this->role_name ?? $this->role);
    }

    /**
     * التحقق إذا كان المستخدم يملك صلاحية إدارة معرض التوظيف
     */
    public function canManageJobFair(): bool
    {
        return $this->isAdmin() ||
               in_array($this->role, ['partnership_officer']) ||
               $this->hasAnyPermission([
                   'job_fair.view', 'job_fair.create', 'job_fair.edit', 'job_fair.delete',
                   'job_fair.manage', 'job_fair.events', 'job_fair.projects', 'job_fair.sponsors',
                   'job_fair.registrations', 'job_fair.attendance', 'job_fair.live',
                   'partnerships.manage', 'companies.view'
               ]);
    }

    /**
     * العلاقة مع الشركات (إذا كان المستخدم شركة)
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    /**
     * العلاقة مع بيانات الخريج
     */
    public function graduateData()
    {
        return $this->hasOne(GraduateData::class, 'user_id');
    }

    /**
     * الحصول على بيانات الخريج مع دعم الربط بالبريد كخيار احتياطي تلقائي
     */
    public function getGraduateDataAttribute()
    {
        if ($this->relationLoaded('graduateData')) {
            $data = $this->getRelation('graduateData');
            if ($data) return $data;
        } else {
            $data = $this->getRelationValue('graduateData');
            if ($data) return $data;
        }

        if ($this->email) {
            $data = GraduateData::where('email', $this->email)->first();
            if ($data) {
                $data->updateQuietly(['user_id' => $this->id]);
                return $data;
            }
        }
        return null;
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
     * الإشعارات الخاصة بالمستخدم
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * التدريبات التي تقدم أو حضرها الخريج
     */
    public function trainingApplications()
    {
        return $this->hasMany(TrainingApplication::class, 'user_id');
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
    /**
     * التحقق إذا كان المستخدم يمكنه إضافة خريجين
     */
    public function canAddGraduates()
    {
        return $this->isAdmin() || $this->hasPermission('graduates.create') || in_array($this->role, ['career_guidance_officer']);
    }

    /**
     * مسار لوحة التحكم المناسب لدور وصلاحيات المستخدم
     */
    public function getDashboardRouteAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'graduate':
                return route('graduate.dashboard');
            case 'company':
                return route('company.dashboard');
            case 'training_coordinator':
                return route('training-coordinator.dashboard');
            case 'partnership_officer':
                return route('partnership.dashboard');
            case 'career_guidance_officer':
                return route('career-guidance.dashboard');
            case 'evaluation_followup':
                return route('evaluation-followup.dashboard');
            case 'media_officer':
                return route('media.dashboard');
            case 'staff':
            default:
                if ($this->hasPermission('trainings.view')) return route('training-coordinator.dashboard');
                if ($this->hasPermission('companies.view') || $this->hasPermission('job_fair.view')) return route('partnership.dashboard');
                if ($this->hasPermission('graduates.view')) return route('career-guidance.dashboard');
                if ($this->hasPermission('evaluations.manage') || $this->hasPermission('surveys.manage')) return route('evaluation-followup.dashboard');
                if ($this->hasPermission('media.manage')) return route('media.dashboard');
                if ($this->hasPermission('users.view') || $this->hasPermission('users.manage')) return route('admin.users');
                return route('dashboard');
        }
    }

    /**
     * الشهادات الممنوحة للمستخدم
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
