<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nomination;
use Illuminate\Auth\Access\HandlesAuthorization;

class NominationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('nominations.manage') || in_array($user->role, ['partnership_officer', 'career_guidance_officer']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Nomination $nomination)
    {
        return $user->isAdmin() || $user->hasPermission('nominations.manage') ||
            in_array($user->role, ['partnership_officer', 'career_guidance_officer']) ||
            ($user->id === $nomination->nominator_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->hasPermission('nominations.manage') || in_array($user->role, ['career_guidance_officer', 'partnership_officer']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Nomination $nomination)
    {
        return $user->isAdmin() || $user->hasPermission('nominations.manage') ||
            in_array($user->role, ['partnership_officer']) ||
            ($user->role === 'career_guidance_officer' && $user->id === $nomination->nominator_id && $nomination->status === 'pending');
    }

    /**
     * Determine whether the user can update the status of the model.
     * This is specifically for changing the 'status' and 'final_status' fields.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateStatus(User $user, Nomination $nomination)
    {
        return $user->isAdmin() || $user->hasPermission('nominations.manage') ||
            in_array($user->role, ['partnership_officer', 'career_guidance_officer']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Nomination $nomination)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Nomination $nomination)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nomination  $nomination
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Nomination $nomination)
    {
        return $user->role === 'admin';
    }
}
