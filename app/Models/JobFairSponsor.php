<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFairSponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_fair_id',
        'company_id',
        'name',
        'tier',
        'logo_path',
        'website',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function jobFair()
    {
        return $this->belongsTo(JobFair::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get badge color class by tier
     */
    public function getTierBadgeClassAttribute(): string
    {
        return match($this->tier) {
            'الراعي الماسي' => 'badge-diamond',
            'الراعي البلاتيني' => 'badge-platinum',
            'الراعي الذهبي' => 'badge-gold',
            'الراعي الفضي' => 'badge-silver',
            'راعي التقنية' => 'badge-tech',
            default => 'badge-gold',
        };
    }
}
