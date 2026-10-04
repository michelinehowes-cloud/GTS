<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\AuditLog;

class LogFailedLogin
{
    /**
     * تسجيل محاولات الدخول الفاشلة والمشبوهة
     */
    public function handle(Failed $event): void
    {
        $identifier = $event->credentials['email'] 
            ?? $event->credentials['name'] 
            ?? $event->credentials['national_id'] 
            ?? 'غير محدد';

        AuditLog::logAction(
            'auth_login_failed',
            'محاولة دخول فاشلة للمعرف (' . $identifier . ')',
            'Security',
            $event->user?->id,
            null,
            [
                'attempted_identifier' => $identifier,
                'user_found' => (bool) $event->user,
                'guard' => $event->guard ?? 'web',
            ]
        );
    }
}
