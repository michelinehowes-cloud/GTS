<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduateData extends Model
{
    use HasFactory;
    protected $table = 'graduates_data';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'national_id',
        'date_of_birth',
        'gender',
        'city',
        'major',
        'specialization',
        'university',
        'sector',
        'faculty',
        'graduation_year',
        'gpa',
        'degree',
        'qualification',
        'skills',
        'languages',
        'certifications',
        'employment_status',
        'work_experience',
        'cv_path',
        'portfolio_url',
        'address',
        'linkedin_url',
        'added_by',
        'data_source',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
        'graduation_year' => 'integer',
        'gpa' => 'decimal:2'
    ];

    /**
     * العلاقة مع المستخدم الذي أضاف البيانات
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * العلاقة مع حساب المستخدم للخريج
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * الحصول على حساب المستخدم مع دعم الربط بالبريد كخيار احتياطي تلقائي
     */
    public function getUserAttribute()
    {
        if ($this->relationLoaded('user')) {
            $user = $this->getRelation('user');
            if ($user) return $user;
        } elseif ($this->user_id) {
            $user = $this->getRelationValue('user');
            if ($user) return $user;
        }

        if ($this->email) {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                $this->updateQuietly(['user_id' => $user->id]);
                return $user;
            }
        }
        return null;
    }

    /**
     * مزامنة وإنشاء أو تحديث بيانات الخريج الكاملة من حساب المستخدم
     */
    public static function syncFromUser(User $user, array $additionalData = []): self
    {
        $existing = self::where('user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                if ($user->email) $q->where('email', $user->email);
            })
            ->first();

        $cleanSkillsStr = is_string($user->skills) ? str_replace('،', ',', $user->skills) : '';
        $skills = is_array($user->skills) ? $user->skills : ($cleanSkillsStr ? (json_decode($cleanSkillsStr, true) ?? array_values(array_filter(array_map('trim', explode(',', $cleanSkillsStr))))) : []);

        $cleanLangStr = is_string($user->languages) ? str_replace('،', ',', $user->languages) : '';
        $languages = is_array($user->languages) ? $user->languages : ($cleanLangStr ? (json_decode($cleanLangStr, true) ?? array_values(array_filter(array_map('trim', explode(',', $cleanLangStr))))) : []);

        $degreeValue = $user->qualification ?? $user->degree ?? ($existing?->degree) ?? 'بكالوريوس';
        $majorValue = $user->specialization ?? $user->major ?? ($existing?->major) ?? 'غير محدد';
        $experienceValue = $user->experiences ?? ($existing?->work_experience);
        $certificationsValue = $additionalData['certifications'] ?? $user->education ?? ($existing?->certifications);
        $employmentStatusValue = $additionalData['employment_status'] ?? ($existing?->employment_status) ?? 'seeking_opportunities';

        $dataToSync = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? ($existing?->phone),
            'national_id' => $user->national_id ?? ($existing?->national_id),
            'date_of_birth' => $user->date_of_birth ?? ($existing?->date_of_birth),
            'gender' => $user->gender ?? ($existing?->gender),
            'city' => $user->city ?? ($existing?->city) ?? 'طرابلس',
            'address' => $user->address ?? ($existing?->address),
            'university' => $user->university ?? ($existing?->university) ?? 'جامعة طرابلس',
            'sector' => $user->sector ?? ($existing?->sector),
            'faculty' => $user->faculty ?? ($existing?->faculty),
            'major' => $majorValue,
            'specialization' => $user->specialization ?? ($existing?->specialization) ?? $majorValue,
            'degree' => $degreeValue,
            'qualification' => $degreeValue,
            'graduation_year' => $user->graduation_year ?? ($existing?->graduation_year) ?? (int)date('Y'),
            'gpa' => $user->gpa ?? ($existing?->gpa),
            'skills' => !empty($skills) ? $skills : ($existing?->skills ?? []),
            'languages' => !empty($languages) ? $languages : ($existing?->languages ?? []),
            'work_experience' => $experienceValue,
            'certifications' => $certificationsValue,
            'cv_path' => $user->cv_path ?? ($existing?->cv_path),
            'employment_status' => $employmentStatusValue,
            'is_active' => $user->is_active ?? true,
            'data_source' => $existing?->data_source ?? 'system_sync',
            'added_by' => $existing?->added_by ?? $user->id,
        ];

        // دمج أي بيانات إضافية
        if (!empty($additionalData)) {
            $dataToSync = array_merge($dataToSync, $additionalData);
        }

        if ($existing) {
            $existing->fill(array_filter($dataToSync, function ($val) { return !is_null($val); }));
            $existing->user_id = $user->id;
            $existing->save();
            return $existing;
        }

        return self::create($dataToSync);
    }

    /**
     * العلاقة مع الترشيحات
     */
    public function nominations()

    {
        return $this->hasMany(Nomination::class, 'graduate_id');
    }

    /**
     * الحصول على عدد الترشيحات
     */
    public function getNominationsCountAttribute()
    {
        return $this->nominations()->count();
    }

    /**
     * الحصول على الترشيحات المقبولة
     */
    public function getAcceptedNominationsAttribute()
    {
        return $this->nominations()->where('status', 'accepted')->count();
    }

    /**
     * البحث عن الخريجين حسب التخصص
     */
    public function scopeByMajor($query, $major)
    {
        return $query->where('major', 'like', '%' . $major . '%');
    }

    /**
     * البحث عن الخريجين حسب سنة التخرج
     */
    public function scopeByGraduationYear($query, $year)
    {
        return $query->where('graduation_year', $year);
    }

    /**
     * البحث عن الخريجين حسب المهارات
     */
    public function scopeBySkills($query, $skills)
    {
        return $query->whereJsonContains('skills', $skills);
    }

    /*
    |--------------------------------------------------------------------------
    | Fallback Accessors for Self-Registered Graduates
    |--------------------------------------------------------------------------
    */

    public function getDateOfBirthAttribute($value)
    {
        return $value ?? $this->user?->date_of_birth;
    }

    public function getGenderAttribute($value)
    {
        return $value ?? $this->user?->gender;
    }

    public function getCityAttribute($value)
    {
        return $value ?? $this->user?->city;
    }

    public function getQualificationAttribute($value)
    {
        return $value ?? $this->attributes['degree'] ?? $this->user?->qualification ?? $this->user?->degree;
    }

    public function getDegreeAttribute($value)
    {
        return $value ?? $this->attributes['qualification'] ?? $this->user?->degree ?? $this->user?->qualification;
    }

    public function getSpecializationAttribute($value)
    {
        return $value ?? $this->attributes['major'] ?? $this->user?->specialization ?? $this->user?->major;
    }

    public function getMajorAttribute($value)
    {
        return $value ?? $this->attributes['specialization'] ?? $this->user?->major ?? $this->user?->specialization;
    }

    public function getSectorAttribute($value)
    {
        return $value ?? $this->user?->sector;
    }

    public function getFacultyAttribute($value)
    {
        return $value ?? $this->user?->faculty;
    }

    public function getAddressAttribute($value)
    {
        return $value ?? $this->user?->address;
    }

    public function getWorkExperienceAttribute($value)
    {
        return $value ?? $this->user?->experiences;
    }

    public function getExperiencesAttribute($value)
    {
        return $value ?? $this->attributes['work_experience'] ?? $this->user?->experiences;
    }

    public function getSkillsTextAttribute()
    {
        $skills = $this->skills ?? $this->user?->skills;
        if (is_array($skills)) {
            return implode(', ', array_filter($skills));
        }
        return is_string($skills) ? $skills : '';
    }

    public function getLanguagesTextAttribute()
    {
        $languages = $this->languages ?? $this->user?->languages;
        if (is_array($languages)) {
            return implode(', ', array_filter($languages));
        }
        return is_string($languages) ? $languages : '';
    }
}