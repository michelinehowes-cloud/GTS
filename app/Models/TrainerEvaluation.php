<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'training_id',
        'evaluator_id',
        'rating',
        'strengths',
        'weaknesses',
        'recommendations',
        'notes',
    ];

    /**
     * العلاقة مع المدرب
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * العلاقة مع التدريب
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * العلاقة مع المقيّم
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
