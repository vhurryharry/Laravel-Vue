<?php

namespace App\Policies;

use App\User;
use App\Interest;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Interest.
     *
     * @param  \App\User  $user
     * @param  \App\Interest  $Interest
     * @return mixed
     */
    public function view(User $user, Interest $Interest)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can create Interests.
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
     * Determine whether the user can update the Interest.
     *
     * @param  \App\User  $user
     * @param  \App\Interest  $Interest
     * @return mixed
     */
    public function update(User $user, Interest $Interest)
    {
        return true;

        return $user->profile->id === $Interest->profile_id;
    }

    /**
     * Determine whether the user can delete the Interest.
     *
     * @param  \App\User  $user
     * @param  \App\Interest  $Interest
     * @return mixed
     */
    public function delete(User $user, Interest $Interest)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can restore the Interest.
     *
     * @param  \App\User  $user
     * @param  \App\Interest  $Interest
     * @return mixed
     */
    public function restore(User $user, Interest $Interest)
    {
        return true;
        //
    }

    /**
     * Determine whether the user can permanently delete the Interest.
     *
     * @param  \App\User  $user
     * @param  \App\Interest  $Interest
     * @return mixed
     */
    public function forceDelete(User $user, Interest $Interest)
    {
        return true;
        //
    }
}
