<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaCamera extends Model
{
    use HasFactory;

    protected $table = 'media_cameras';

    protected $fillable = [
        'title',
        'location_tag',
        'stream_type',
        'stream_url',
        'is_live',
        'is_primary',
        'display_order',
        'description',
        'created_by',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'is_primary' => 'boolean',
        'display_order' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get clean embed URL for iframe or video player
     */
    public function getEmbedUrlAttribute()
    {
        $url = trim($this->stream_url);

        if ($this->stream_type === 'youtube_live') {
            // Extract youtube ID
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
                return 'https://www.youtube-nocookie.com/embed/' . $matches[1] . '?autoplay=1&mute=1&rel=0';
            }
            if (strlen($url) === 11) {
                return 'https://www.youtube-nocookie.com/embed/' . $url . '?autoplay=1&mute=1&rel=0';
            }
            return $url;
        }

        return $url;
    }
}
