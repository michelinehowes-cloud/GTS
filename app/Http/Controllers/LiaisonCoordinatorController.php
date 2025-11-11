<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'bio', 'skills', 'education', 'experience', 'cv_url', 'portfolio_url'
    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'experience' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // دلالات للمهارات
    public function getSkillsListAttribute()
    {
        return $this->skills ? implode(', ', $this->skills) : '';
    }
}