<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A phone that receives push notifications for a user (Firebase Cloud Messaging token).
 */
class DeviceToken extends Model
{
    protected $fillable = ['user_id', 'token', 'token_hash', 'platform', 'last_used_at'];

    protected $casts = ['last_used_at' => 'datetime'];

    public static function hashOf(string $token): string
    {
        return hash('sha256', $token);
    }

    /** Register (or move) a token to a user. A phone belongs to one user at a time. */
    public static function register(User $user, string $token, string $platform = 'android'): self
    {
        return static::updateOrCreate(
            ['token_hash' => static::hashOf($token)],
            ['user_id' => $user->id, 'token' => $token, 'platform' => $platform, 'last_used_at' => now()]
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
