<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairWishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'graduate_id',
        'job_fair_company_id',
    ];

    public function graduate()
    {
        return $this->belongsTo(User::class, 'graduate_id');
    }

    public function jobFairCompany()
    {
        return $this->belongsTo(JobFairCompany::class, 'job_fair_company_id');
    }
}
