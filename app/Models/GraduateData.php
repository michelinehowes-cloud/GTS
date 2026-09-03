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
        return $this->belongsTo(User::class, 'email', 'email');
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