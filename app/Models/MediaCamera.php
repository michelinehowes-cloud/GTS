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
     * Get clean embed URL for iframe, video, or image player
     */
    public function getEmbedUrlAttribute()
    {
        $url = trim($this->stream_url);

        // تحويل الأرقام العربية المشرقية (٠١٢٣٤٥٦٧٨٩) إلى أرقام إنجليزية (0123456789)
        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $url = str_replace($arabic, $english, $url);

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

        // إضافة بروتوكول http إذا لم يُكتب
        if (!preg_match('/^https?:\/\//i', $url) && !str_starts_with($url, '//')) {
            $url = 'http://' . $url;
        }

        // إذا كان رابط كاميرا هاتف IP Webcam وينتهي بالمنفذ فقط مثل :8080 أو :8080/
        if (preg_match('/:\d{4,5}\/?$/', $url)) {
            return rtrim($url, '/') . '/video';
        }

        return $url;
    }

    /**
     * هل البث من نوع صورة متدفقة MJPEG (مثل كاميرات الهواتف IP Webcam / DroidCam)؟
     */
    public function getIsMjpegAttribute(): bool
    {
        if ($this->stream_type === 'youtube_live') {
            return false;
        }
        $url = strtolower($this->embed_url ?? '');
        return str_contains($url, '/video') 
            || str_contains($url, '/mjpeg')
            || str_contains($url, ':8080')
            || str_contains($url, ':4747');
    }

    /**
     * هل البث فيديو مباشر مدعوم في وسم HTML5 video (مثل MP4, OGG, WebM, HLS أو بث VLC المحلي)؟
     */
    public function getIsDirectVideoAttribute(): bool
    {
        if ($this->stream_type === 'youtube_live' || $this->is_mjpeg) {
            return false;
        }

        $url = strtolower($this->embed_url ?? '');
        return $this->stream_type === 'hls_m3u8'
            || str_ends_with($url, '.m3u8')
            || str_ends_with($url, '.mp4')
            || str_ends_with($url, '.ogg')
            || str_ends_with($url, '.webm')
            || str_contains($url, ':8090')
            || str_contains($url, ':8088');
    }
}
