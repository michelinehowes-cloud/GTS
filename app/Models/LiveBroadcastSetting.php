<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveBroadcastSetting extends Model
{
    use HasFactory;

    protected $table = 'live_broadcast_settings';

    protected $fillable = [
        'is_live_now',
        'broadcast_title',
        'broadcast_description',
        'active_camera_id',
        'viewers_count',
    ];

    protected $casts = [
        'is_live_now' => 'boolean',
        'viewers_count' => 'integer',
    ];

    public function activeCamera()
    {
        return $this->belongsTo(MediaCamera::class, 'active_camera_id');
    }

    /**
     * Helper to get singleton setting
     */
    public static function current()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'is_live_now' => false,
                'broadcast_title' => 'البث المباشر — فعاليات ومعرض جامعة طرابلس 2026',
                'broadcast_description' => 'تغطية حية ومباشرة للفعاليات، ورش العمل، وحفل التخرج ومعرض التوظيف.',
                'viewers_count' => 320,
            ]);
        }
        return $setting;
    }
}
