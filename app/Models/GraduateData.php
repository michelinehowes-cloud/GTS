<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduateData extends Model
{
    use HasFactory;
    protected $table = 'graduates_data';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'national_id',
        'major',
        'university',
        'graduation_year',
        'gpa',
        'degree',
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
        'notes'
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
}