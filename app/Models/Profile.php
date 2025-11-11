<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'university_id', 
        'graduation_year', 'status', 'email_verification_token'
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verification_token'
    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'email_verified_at' => 'datetime',
    ];

    // العلاقات
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class, 'coordinator_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    // الصلاحيات
    public function isAdmin() { return $this->role === 'admin'; }
    public function isTrainingCoordinator() { return $this->role === 'training_coordinator'; }
    public function isPlacementCoordinator() { return $this->role === 'placement_coordinator'; }
    public function isCompany() { return $this->role === 'company'; }
    public function isGraduate() { return $this->role === 'graduate'; }
    public function isLiaisonCoordinator() { return $this->role === 'liaison_coordinator'; }
    public function isMonitoringEvaluationOfficer() { return $this->role === 'monitoring_evaluation_officer'; }

    // التحقق من الحساب
    public function isVerified()
    {
        return $this->status === 'active' && $this->email_verified_at !== null;
    }
}