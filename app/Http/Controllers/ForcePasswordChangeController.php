<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordChangeController extends Controller
{
    /**
     * عرض صفحة تغيير كلمة المرور الإجباري
     */
    public function show()
    {
        return view('auth.force-password-change');
    }

    /**
     * تحديث كلمة المرور
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.letters' => 'كلمة المرور يجب أن تحتوي على حروف',
            'password.mixed' => 'كلمة المرور يجب أن تحتوي على حروف كبيرة وصغيرة',
            'password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام',
            'password.symbols' => 'كلمة المرور يجب أن تحتوي على رموز',
        ]);

        $user = auth()->user();

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        \App\Models\AuditLog::logAction(
            'auth_force_password_change',
            'تم تحديث كلمة المرور الإلزامية للمستخدم (' . ($user->name ?? $user->email) . ')',
            'User',
            $user->id,
            null,
            ['user_id' => $user->id, 'email' => $user->email, 'role' => $user->role]
        );

        return redirect()->route(match ($user->role) {
            'admin' => 'admin.dashboard',
            'graduate' => 'graduate.dashboard',
            'career_guidance_officer' => 'career-guidance.dashboard',
            'partnership_officer' => 'partnership.dashboard',
            'training_coordinator' => 'training-coordinator.dashboard',
            'evaluation_followup' => 'evaluation-followup.dashboard',
            default => 'dashboard',
        })->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
