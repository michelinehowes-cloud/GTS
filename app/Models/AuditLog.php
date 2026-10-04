<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
     * تشديد أمان السجل: السجل محصن ضد التلاعب (Tamper-Proof) للقراءة والإضافة فقط.
     * يمنع منعاً باتاً تعديل أو حذف أي سجل بعد تسجيله.
     */
    protected static function booted()
    {
        static::updating(function ($log) {
            throw new \RuntimeException('محاولة أمنية محظورة: سجل العمليات والرقابة محصن ضد التعديل (Tamper-Proof Audit Trail).');
        });

        static::deleting(function ($log) {
            throw new \RuntimeException('محاولة أمنية محظورة: سجل العمليات والرقابة محصن ضد الحذف نهائياً لحماية سلامة المراجعة القانونية.');
        });
    }

    /**
     * العلاقة مع المستخدم المنفذ للعملية
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * تسجيل حركة أمنية في سجل الرقابة بمرونة ودقة عالية
     */
    public static function logAction($action, $entityOrDesc, $entityId = null, $oldValues = null, $newValues = null)
    {
        try {
            $description = null;
            // دعم التوافق مع الاستدعاءات المختلفة
            if (is_string($entityId) && !is_numeric($entityId) && (is_numeric($oldValues) || is_null($oldValues))) {
                // استدعاء من النمط القديم: logAction($action, $description, $entityName, $entityId, $oldValues, $newValues)
                $description = $entityOrDesc;
                $actualEntity = $entityId;
                $actualEntityId = $oldValues;
                $actualOld = is_array($newValues) ? $newValues : null;
                $actualNew = is_array(func_get_args()[5] ?? null) ? func_get_args()[5] : null;
            } else {
                $actualEntity = $entityOrDesc;
                $actualEntityId = $entityId;
                $actualOld = $oldValues;
                $actualNew = $newValues;
            }

            // التأكد من استخراج مصفوفات القيم لتخزينها بـ JSON النظيف
            $parsedOld = is_array($actualOld) ? $actualOld : (is_string($actualOld) ? json_decode($actualOld, true) : null);
            $parsedNew = is_array($actualNew) ? $actualNew : (is_string($actualNew) ? json_decode($actualNew, true) : null);

            // حفظ الوصف التفصيلي إن وجد
            if ($description && is_string($description)) {
                if (!is_array($parsedNew)) {
                    $parsedNew = [];
                }
                if (!isset($parsedNew['description'])) {
                    $parsedNew['description'] = $description;
                }
            }

            return self::create([
                'user_id' => auth()->id() ?? null,
                'action' => $action,
                'entity' => (string) $actualEntity,
                'entity_id' => is_numeric($actualEntityId) ? (int) $actualEntityId : null,
                'old_values' => $parsedOld,
                'new_values' => $parsedNew,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Failed to write audit log: " . $e->getMessage());
            return null;
        }
    }

    /**
     * تصنيف نوع الحركة (أمان، مصادقة، بيانات، نسخ احتياطي)
     */
    public function getCategoryAttribute(): string
    {
        $actionLower = strtolower($this->action ?? '');

        if (str_starts_with($actionLower, 'auth_') || str_contains($actionLower, 'login') || str_contains($actionLower, 'logout')) {
            return 'auth';
        }

        if (str_starts_with($actionLower, 'security_') || str_contains($actionLower, 'blocked') || str_contains($actionLower, 'denied')) {
            return 'security';
        }

        if (str_starts_with($actionLower, 'backup_') || str_contains($actionLower, 'backup')) {
            return 'backup';
        }

        if (str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy')) {
            return 'delete';
        }

        if (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) {
            return 'update';
        }

        if (str_contains($actionLower, 'create') || str_contains($actionLower, 'store')) {
            return 'create';
        }

        return 'general';
    }

    /**
     * الوصف التلقائي للعملية
     */
    public function getDescriptionAttribute(): string
    {
        if (!empty($this->new_values['description']) && is_string($this->new_values['description'])) {
            return $this->new_values['description'];
        }

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
