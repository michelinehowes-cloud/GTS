<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // المدراء ومسؤولي الشراكات يمكنهم رؤية جميع الشركات
        if (in_array($user->role, ['admin', 'partnership_officer'])) {
            return true;
        }

        // مسؤولي الإرشاد المهني يمكنهم رؤية الشركات المعتمدة فقط
        if ($user->role === 'career_guidance_officer') {
            return $company->is_approved;
        }

        // الشركات ترى ملفها الخاص فقط
        if ($user->role === 'company') {
            return $user->company && $user->company->id === $company->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // المدراء يمكنهم تحديث جميع الشركات
        if ($user->role === 'admin') {
            return true;
        }

        // مسؤولي الشراكات يمكنهم تحديث جميع الشركات
        if ($user->role === 'partnership_officer') {
            return true;
        }

        // الشركات تحدث ملفها الخاص فقط
        if ($user->role === 'company') {
            return $user->company && $user->company->id === $company->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // المدراء فقط يمكنهم حذف الشركات
        return $user->role === 'admin';
    }

    /**
     * تحقق إذا كان المستخدم يمكنه الموافقة على الشركات
     */
    public function approve(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إدارة وثائق الشراكة
     */
    public function managePartnershipDocuments(User $user, Company $company): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه عرض التقارير
     */
    public function viewReports(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
    }
}
