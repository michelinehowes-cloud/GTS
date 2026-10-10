<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'user_id',
        'sender_id',
        'model_type',
        'model_id',
        'data',
        'is_read',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'sender_id' => 'integer',
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected $appends = ['icon'];

    /**
     * العلاقة مع المستخدم المستلم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع المرسل
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * العلاقة مع النموذج المرتبط
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Scope للإشعارات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope للإشعارات حسب النوع
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope للإشعارات لمستخدم معين
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * تحديث حالة القراءة
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * تحديث حالة الإرسال
     */
    public function markAsSent(): bool
    {
        return $this->update(['sent_at' => now()]);
    }

    /**
     * التحقق من قراءة الإشعار
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * الحصول على أيقونة الإشعار حسب النوع
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger' => 'times-circle',
            'info' => 'info-circle',
            default => 'bell',
        };
    }

    /**
     * الحصول على لون الإشعار حسب النوع
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'success' => 'green',
            'warning' => 'yellow',
            'danger' => 'red',
            'info' => 'blue',
            default => 'gray',
        };
    }
}
