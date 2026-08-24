<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'company_id',
        'booth_number',
        'booth_location',
        'participating_sectors',
        'available_positions',
        'requirements',
        'notes',
        'status',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
