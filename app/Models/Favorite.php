<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['graduate_id', 'company_id'];

    public function graduate() {
        return $this->belongsTo(User::class, 'graduate_id');
    }
    
    public function company() {
        return $this->belongsTo(User::class, 'company_id');
    }
