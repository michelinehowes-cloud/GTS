<?php

namespace App\Policies;

use App\Models\JobOpportunity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobOpportunityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'partnership_officer',
            'career_guidance_officer',
            'graduate',
            'company'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobOpportunity $jobOpportunity): bool
    {
        // المدراء ومسؤولي الشراكات يرون جميع فرص العمل
        if (in_array($user->role, ['admin', 'partnership_officer'])) {
            return true;
        }

        // مسؤولي الإرشاد المهني يرون الفرص النشطة فقط
        if ($user->role === 'career_guidance_officer') {
            return $jobOpportunity->status === 'active';
        }

        // الخريجين يرون الفرص النشطة فقط
        if ($user->role === 'graduate') {
            return $jobOpportunity->status === 'active';
        }

        // الشركات ترى فرصها الخاصة فقط
        if ($user->role === 'company') {
            return $jobOpportunity->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer', 'company']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobOpportunity $jobOpportunity): bool
    {
        // المدراء يحدثون جميع الفرص
        if ($user->role === 'admin') {
            return true;
        }

        // مسؤولي الشراكات يحدثون جميع الفرص
        if ($user->role === 'partnership_officer') {
            return true;
        }

        // الشركات تحدث فرصها الخاصة فقط
        if ($user->role === 'company') {
            return $jobOpportunity->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobOpportunity $jobOpportunity): bool
    {
        // المدراء يحذفون جميع الفرص
        if ($user->role === 'admin') {
            return true;
        }

        // مسؤولي الشراكات يحذفون جميع الفرص
        if ($user->role === 'partnership_officer') {
            return true;
        }

        // الشركات تحذف فرصها الخاصة فقط
        if ($user->role === 'company') {
            return $jobOpportunity->created_by === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه التقديم على فرصة العمل
     */
    public function apply(User $user, JobOpportunity $jobOpportunity): bool
    {
        // الخريجين فقط يمكنهم التقديم
        if ($user->role !== 'graduate') {
            return false;
        }

        // الفرصة يجب أن تكون نشطة
        if ($jobOpportunity->status !== 'active') {
            return false;
        }

        // التحقق من عدم وجود ترشيح سابق
        $existingNomination = $jobOpportunity->nominations()
            ->where('graduate_id', $user->id)
            ->exists();

        return !$existingNomination;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إدارة الترشيحات
     */
    public function manageNominations(User $user, JobOpportunity $jobOpportunity): bool
    {
        // المدراء ومسؤولي الشراكات يديرون جميع الترشيحات
        if (in_array($user->role, ['admin', 'partnership_officer'])) {
            return true;
        }

        // مسؤولي الإرشاد المهني يديرون الترشيحات
        if ($user->role === 'career_guidance_officer') {
            return true;
        }

        // الشركات تدير ترشيحات فرصها الخاصة
        if ($user->role === 'company') {
            return $jobOpportunity->created_by === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه تحديث حالة الفرصة
     */
    public function updateStatus(User $user, JobOpportunity $jobOpportunity): bool
    {
        // المدراء ومسؤولي الشراكات يغيرون الحالة
        if (in_array($user->role, ['admin', 'partnership_officer'])) {
            return true;
        }

        // الشركات تغير حالة فرصها الخاصة
        if ($user->role === 'company') {
            return $jobOpportunity->created_by === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه استيراد البيانات
     */
    public function import(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه تصدير البيانات
     */
    public function export(User $user): bool
    {
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
    }
}
