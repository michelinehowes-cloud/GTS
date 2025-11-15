<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'training_coordinator',
            'graduate',
            'evaluation_followup',
            'career_guidance_officer',
            'partnership_officer'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Training $training): bool
    {
        // المدراء ومنسقي التدريب يرون جميع التدريبات
        if (in_array($user->role, ['admin', 'training_coordinator', 'evaluation_followup'])) {
            return true;
        }

        // الخريجين يرون التدريبات المتاحة فقط
        if ($user->role === 'graduate') {
            return $training->is_active && $training->application_deadline > now();
        }

        // مسؤولي الإرشاد المهني والشراكات يرون التدريبات النشطة
        if (in_array($user->role, ['career_guidance_officer', 'partnership_officer'])) {
            return $training->is_active;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'training_coordinator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Training $training): bool
    {
        // المدراء يحدثون جميع التدريبات
        if ($user->role === 'admin') {
            return true;
        }

        // منسقي التدريب يحدثون تدريباتهم فقط
        if ($user->role === 'training_coordinator') {
            return $training->coordinator_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Training $training): bool
    {
        // المدراء يحذفون جميع التدريبات
        if ($user->role === 'admin') {
            return true;
        }

        // منسقي التدريب يحذفون تدريباتهم فقط
        if ($user->role === 'training_coordinator') {
            return $training->coordinator_id === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه التقديم على التدريب
     */
    public function apply(User $user, Training $training): bool
    {
        // الخريجين فقط يمكنهم التقديم
        if ($user->role !== 'graduate') {
            return false;
        }

        // التدريب يجب أن يكون متاحاً
        if (!$training->is_active || $training->application_deadline < now()) {
            return false;
        }

        // التحقق من عدم وجود طلب سابق
        $existingApplication = $training->applications()
            ->where('user_id', $user->id)
            ->exists();

        return !$existingApplication;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إدارة طلبات التدريب
     */
    public function manageApplications(User $user, Training $training): bool
    {
        // المدراء يديرون جميع الطلبات
        if ($user->role === 'admin') {
            return true;
        }

        // منسقي التدريب يديرون طلبات تدريباتهم
        if ($user->role === 'training_coordinator') {
            return $training->coordinator_id === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه عرض التقارير
     */
    public function viewReports(User $user): bool
    {
        return in_array($user->role, ['admin', 'training_coordinator', 'evaluation_followup']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه تحميل التقارير
     */
    public function downloadReports(User $user, Training $training): bool
    {
        // المدراء ومنسقي التدريب ومسؤولي التقييم يحملون التقارير
        return in_array($user->role, ['admin', 'training_coordinator', 'evaluation_followup']);
    }
}
