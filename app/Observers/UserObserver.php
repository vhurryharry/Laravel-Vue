<?php

namespace App\Observers;
use App\User;

class UserObserver
{
    /**
     * Handle the user "created" User.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(user $user, $id = 0)
    {
        // $this->createUsername($user, $id);
    }

    protected function createUsername(User $user, $id = 0)
    {
        $username = str_slug($user->name);
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allUsernames = $this->getRelatedUsernames($username, $id);
        // If we haven't used it before then we are all good.
        if (!$allUsernames->contains('username', $username)) {
            $user->username = $username;
            $user->save();

            return $user;
            // return $username;
        }
        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 1000; $i++) {
            $newUsername = $username . '-' . $i;
            if (!$allUsernames->contains('username', $newUsername)) {
                // return $newUsername;
                $user->username = $newUsername;
                $user->save();

                return $user;
            }
        }
        throw new \Exception('Can not create a unique username');
    }


    protected function getRelatedUsernames($username, $id = 0)
    {
        return User::select('username')->where('username', 'like', $username . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
