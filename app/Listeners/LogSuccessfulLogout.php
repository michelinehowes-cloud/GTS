<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;

class LogSuccessfulLogout
{
    /**
     * تسجيل عملية تسجيل الخروج
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        if ($user) {
            AuditLog::logAction(
                'auth_logout',
                'تسجيل خروج للمستخدم (' . ($user->name ?? $user->email) . ')',
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
