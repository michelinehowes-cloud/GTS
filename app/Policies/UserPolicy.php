<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // المدراء يمكنهم رؤية جميع المستخدمين
        if ($user->role === 'admin') {
            return true;
        }

        // المستخدمون يمكنهم رؤية ملفهم الشخصي فقط
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // المدراء يمكنهم تحديث جميع المستخدمين
        if ($user->role === 'admin') {
            return true;
        }

        // المستخدمون يمكنهم تحديث ملفهم الشخصي فقط
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // المدراء يمكنهم حذف المستخدمين (ما عدا المدراء الآخرين)
        if ($user->role === 'admin' && $model->role !== 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إدارة الأدوار
     */
    public function manageRoles(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إدارة الصلاحيات
     */
    public function managePermissions(User $user): bool
    {
        return $user->role === 'admin';
    }
}
