<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * تفعيل المراقبة التلقائية للنموذج عند التهيئة
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logModelActivity('created', $model, null, $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $old = [];
            $new = [];
            foreach ($dirty as $key => $newValue) {
                if ($model->isAuditExcluded($key)) {
                    continue;
                }
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $newValue;
            }

            if (!empty($new)) {
                self::logModelActivity('updated', $model, $old, $new);
            }
        });

        static::deleted(function ($model) {
            self::logModelActivity('deleted', $model, $model->getAuditableAttributes(), null);
        });
    }

    /**
     * تسجيل حركة النموذج في سجل الرقابة
     */
    protected static function logModelActivity(string $event, $model, ?array $old, ?array $new): void
    {
        $entityName = class_basename($model);
        $actionName = strtolower($entityName) . '_' . $event;

        AuditLog::logAction(
            $actionName,
            $entityName,
            $model->getKey(),
            $old,
            $new
        );
    }

    /**
     * جلب الحقول المسموح بتسجيلها وتجريد الحقول الحساسة
     */
    public function getAuditableAttributes(): array
    {
        $attributes = $this->attributesToArray();
        foreach ($this->getAuditExcludedFields() as $field) {
            unset($attributes[$field]);
        }
        return $attributes;
    }

    /**
     * هل الحقل مستثنى من السجل؟
     */
    public function isAuditExcluded(string $field): bool
    {
        return in_array($field, $this->getAuditExcludedFields(), true);
    }

    /**
     * قائمة الحقول الحساسة المستثناة من السجل (مثل كلمات المرور ورموز التذكر)
     */
    public function getAuditExcludedFields(): array
    {
        $defaultExcluded = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'updated_at', 'created_at'];

        return property_exists($this, 'auditExclude') 
            ? array_merge($defaultExcluded, $this->auditExclude)
            : $defaultExcluded;
    }
}
