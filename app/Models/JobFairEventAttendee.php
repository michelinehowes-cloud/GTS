<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairEventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_event_id',
        'graduate_id',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(JobFairEvent::class, 'job_fair_event_id');
    }

    public function graduate()
    {
        return $this->belongsTo(User::class, 'graduate_id');
    }
}
