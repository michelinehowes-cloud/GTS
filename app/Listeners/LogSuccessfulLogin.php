<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuditLog;

class LogSuccessfulLogin
{
    /**
     * تسجيل عملية تسجيل الدخول الناجحة
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        AuditLog::logAction(
            'auth_login_success',
            'تسجيل دخول ناجح للمستخدم (' . ($user->name ?? $user->email) . ') بدور [' . ($user->role ?? 'مستخدم') . ']',
            'User',
            $user->id,
            null,
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'guard' => $event->guard ?? 'web',
            ]
        );
    }
}
