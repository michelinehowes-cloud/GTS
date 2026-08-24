<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairVisit extends Model
{
    protected $fillable = ['job_fair_id', 'company_id', 'graduate_id', 'notes'];

    public function jobFair() {
        return $this->belongsTo(JobFair::class);
    }
    
    public function company() {
        return $this->belongsTo(User::class, 'company_id');
    }
    
    public function graduate() {
        return $this->belongsTo(User::class, 'graduate_id');
    }
