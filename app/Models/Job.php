<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'company_id', 'title', 'type', 'description', 'requirements',
        'location', 'seats', 'deadline', 'salary', 'benefits', 'status'
    ];

    protected $casts = [
        'requirements' => 'array',
        'benefits' => 'array',
        'deadline' => 'date'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}