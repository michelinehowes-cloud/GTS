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

    /**
     * العلاقة مع التقييمات
     */
    public function trainerEvaluations()
    {
        return $this->hasMany(TrainerEvaluation::class);
    }

    /**
     * العلاقة مع التقييمات من خلال جدول trainer_evaluations
     */
    public function evaluations()
    {
        return $this->belongsToMany(Evaluation::class, 'trainer_evaluations');
    }

    /**
     * حساب متوسط التقييم
     */
    public function averageRating()
    {
        return $this->trainerEvaluations()->avg('rating');
    }
}
