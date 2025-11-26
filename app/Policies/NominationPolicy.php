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
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
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
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']) ||
            ($user->id === $nomination->nominator->id); // Allow nominator to view their own nominations
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'career_guidance_officer']);
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
        // Admin and partnership_officer can update any nomination
        // Career_guidance_officer can update their own nominations if status is pending
        return in_array($user->role, ['admin', 'partnership_officer']) ||
            ($user->role === 'career_guidance_officer' && $user->id === $nomination->nominator->id && $nomination->status === 'pending');
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
        // Admin, partnership_officer, and career_guidance_officer can update nomination status
        return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
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
