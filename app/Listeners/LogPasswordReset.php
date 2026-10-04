<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use App\Models\AuditLog;

class LogPasswordReset
{
    /**
     * تسجيل استعادة وتغيير كلمة المرور
     */
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;
        if ($user) {
            AuditLog::logAction(
                'auth_password_reset',
                'تمت استعادة كلمة المرور بنجاح للمستخدم (' . ($user->name ?? $user->email) . ')',
                'User',
                $user->id,
                null,
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            );
        }
    }
}
