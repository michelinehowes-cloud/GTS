<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
        'bio',
        'photo',
        'linkedin_url',
    ];

    /**
     * العلاقة مع التدريبات
     */
    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}
