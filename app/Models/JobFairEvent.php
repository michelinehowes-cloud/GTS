<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'title',
        'description',
        'speaker_name',
        'start_time',
        'end_time',
        'location',
        'capacity',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    public function attendees()
    {
        return $this->hasMany(JobFairEventAttendee::class, 'job_fair_event_id');
    }
}
