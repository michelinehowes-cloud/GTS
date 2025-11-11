<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    protected $fillable = [
        'title', 'description', 'duration', 'start_date', 'end_date', 
        'capacity', 'status', 'coordinator_id', 'requirements'
    ];

    protected $casts = [
        'requirements' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'training_participants')
                    ->withTimestamps();
    }
}