<?php
// ملف: app/Models/Registration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'participant_name',
        'participant_email',
        'participant_phone',
        'notes',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}