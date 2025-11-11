<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'user_id',
        'status',
        'message',
        'applied_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}