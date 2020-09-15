<?php

namespace App\Policies;

use App\User;
use App\Privacy;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivacyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Privacy.
     *
     * @param  \App\User  $user
     * @param  \App\Privacy  $Privacy
     * @return mixed
     */
    public function view(User $user, Privacy $Privacy)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can create Privacys.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can update the Privacy.
     *
     * @param  \App\User  $user
     * @param  \App\Privacy  $Privacy
     * @return mixed
     */
    public function update(User $user, Privacy $Privacy)
    {
        return true;

        return $user->profile->id === $Privacy->profile_id;
    }

    /**
     * Determine whether the user can delete the Privacy.
     *
     * @param  \App\User  $user
     * @param  \App\Privacy  $Privacy
     * @return mixed
     */
    public function delete(User $user, Privacy $Privacy)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can restore the Privacy.
     *
     * @param  \App\User  $user
     * @param  \App\Privacy  $Privacy
     * @return mixed
     */
    public function restore(User $user, Privacy $Privacy)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can permanently delete the Privacy.
     *
     * @param  \App\User  $user
     * @param  \App\Privacy  $Privacy
     * @return mixed
     */
    public function forceDelete(User $user, Privacy $Privacy)
    {
        return true;
        //
    }
}
