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
     * Get human-readable Arabic label by tier
     */
    public function getTierLabelAttribute(): string
    {
        return match(strtolower(trim((string)$this->tier))) {
            'strategic', 'الراعي الاستراتيجي' => 'الراعي الاستراتيجي',
            'diamond', 'الراعي الماسي' => 'الراعي الماسي',
            'platinum', 'الراعي البلاتيني' => 'الراعي البلاتيني',
            'gold', 'الراعي الذهبي' => 'الراعي الذهبي',
            'silver', 'الراعي الفضي' => 'الراعي الفضي',
            'bronze', 'الراعي البرونزي' => 'الراعي البرونزي',
            'tech', 'راعي التقنية' => 'راعي التقنية',
            'media', 'الراعي الإعلامي' => 'الراعي الإعلامي',
            default => $this->tier ?: 'الراعي الذهبي',
        };
    }

    /**
     * Get FontAwesome icon class by tier
     */
    public function getTierIconAttribute(): string
    {
        return match(strtolower(trim((string)$this->tier))) {
            'strategic', 'الراعي الاستراتيجي' => 'fa-crown',
            'diamond', 'الراعي الماسي' => 'fa-gem',
            'platinum', 'الراعي البلاتيني' => 'fa-star',
            'silver', 'الراعي الفضي' => 'fa-award',
            'tech', 'راعي التقنية' => 'fa-microchip',
            'media', 'الراعي الإعلامي' => 'fa-bullhorn',
            default => 'fa-trophy',
        };
    }

    /**
     * Get badge color class by tier
     */
    public function getTierBadgeClassAttribute(): string
    {
        return match(strtolower(trim((string)$this->tier))) {
            'diamond', 'الراعي الماسي' => 'badge-diamond',
            'platinum', 'الراعي البلاتيني' => 'badge-platinum',
            'gold', 'الراعي الذهبي' => 'badge-gold',
            'silver', 'الراعي الفضي' => 'badge-silver',
            'tech', 'راعي التقنية' => 'badge-tech',
            'strategic', 'الراعي الاستراتيجي' => 'badge-strategic',
            default => 'badge-gold',
        };
    }
}
