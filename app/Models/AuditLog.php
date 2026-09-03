<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    public $timestamps = false; // يستخدم حقل timestamp المخصص

    protected $fillable = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'timestamp',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'timestamp' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم المنفذ للعملية
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * تسجيل حركة أمنية في سجل الرقابة
     */
    public static function logAction($action, $entity, $entityId = null, $oldValues = null, $newValues = null)
    {
        try {
            return self::create([
                'user_id' => auth()->id() ?? null,
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning("Failed to write audit log: " . $e->getMessage());
            return null;
        }
    }

    /**
     * الوصول البديل لاسم الحدث
     */
    public function getEventAttribute()
    {
        return $this->action;
    }

    /**
     * الوصف التلقائي للعملية
     */
    public function getDescriptionAttribute()
    {
        $desc = "إجراء ({$this->action}) على ({$this->entity})";
        if ($this->entity_id) {
            $desc .= " رقم #{$this->entity_id}";
        }
        return $desc;
    }

    /**
     * تاريخ الإنشاء كبديل لـ timestamp
     */
    public function getCreatedAtAttribute()
    {
        return $this->timestamp ?? now();
    }
}
