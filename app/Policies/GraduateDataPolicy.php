<?php

namespace App\Policies;

use App\Models\GraduateData;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GraduateDataPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('graduates.view') || in_array($user->role, [
            'career_guidance_officer',
            'partnership_officer',
            'evaluation_followup'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GraduateData $graduateData): bool
    {
        // المدراء ومسؤولي الإرشاد وأصحاب الصلاحيات يرون جميع بيانات الخريجين
        if ($user->isAdmin() || $user->hasPermission('graduates.view')) {
            return true;
        }

        // مسؤولي الإرشاد المهني يرون جميع البيانات
        if ($user->role === 'career_guidance_officer') {
            return true;
        }

        // مسؤولي الشراكات والتقييم يرون البيانات لأغراض التوظيف والتقييم
        if (in_array($user->role, ['partnership_officer', 'evaluation_followup'])) {
            return true;
        }

        // الخريجين يرون بياناتهم الخاصة فقط
        if ($user->role === 'graduate') {
            return $graduateData->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('graduates.create') || $user->role === 'career_guidance_officer';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GraduateData $graduateData): bool
    {
        if ($user->isAdmin() || $user->hasPermission('graduates.edit')) {
            return true;
        }

        // مسؤولي الإرشاد المهني يحدثون جميع البيانات
        if ($user->role === 'career_guidance_officer') {
            return true;
        }

        // الخريجين يحدثون بياناتهم الخاصة فقط
        if ($user->role === 'graduate') {
            return $graduateData->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GraduateData $graduateData): bool
    {
        return $user->isAdmin() || $user->hasPermission('graduates.delete') || $user->role === 'career_guidance_officer';
    }

    /**
     * تحقق إذا كان المستخدم يمكنه استيراد بيانات الخريجين
     */
    public function import(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('graduates.import_export') || in_array($user->role, ['career_guidance_officer', 'partnership_officer']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه تصدير بيانات الخريجين
     */
    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('graduates.import_export') || in_array($user->role, ['career_guidance_officer', 'evaluation_followup']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه إنشاء ترشيحات للخريج
     */
    public function nominate(User $user, GraduateData $graduateData): bool
    {
        // مسؤولي الإرشاد المهني والشراكات يمكنهم الترشيح
        return in_array($user->role, ['career_guidance_officer', 'partnership_officer']);
    }

    /**
     * تحقق إذا كان المستخدم يمكنه عرض السيرة الذاتية
     */
    public function viewResume(User $user, GraduateData $graduateData): bool
    {
        // المدراء ومسؤولي الإرشاد والشراكات يرون السير الذاتية
        if (in_array($user->role, ['admin', 'career_guidance_officer', 'partnership_officer'])) {
            return true;
        }

        // الخريج يرى سيرته الذاتية
        if ($user->role === 'graduate') {
            return $graduateData->user_id === $user->id;
        }

        return false;
    }

    /**
     * تحقق إذا كان المستخدم يمكنه تحديث حالة التوظيف
     */
    public function updateEmploymentStatus(User $user, GraduateData $graduateData): bool
    {
        // مسؤولي الشراكات والإرشاد يحدثون حالة التوظيف
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
    }
}
