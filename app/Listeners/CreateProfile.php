<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Profile;
use Illuminate\Support\Str;
use Webpatser\Countries\Countries;
use Carbon\Carbon;
use App\RelationshipPrivacyValue;
use App\User;
use App\TimezoneValue;

class CreateProfile
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event, $id = 0)
    {

        $user = $this->createUsername($event->user, $id);

        $profile = Profile::create([
            'user_id' => $event->user->id,
            'username' => $user->username,
            'name' => $event->user->firstname . ' ' . $event->user->lastname,
            'firstname' => $event->user->firstname,
            'lastname' => $event->user->lastname,
            'image_path' => 'profiles/default.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $privacyValue = RelationshipPrivacyValue::where('key', 'visible_to_all')->first();

        $profile->searchabilityPrivacy()->associate($privacyValue);
        $profile->myFriendsPrivacy()->associate($privacyValue);
        $profile->myEventsPrivacy()->associate($privacyValue);
        $profile->myCommunitiesPrivacy()->associate($privacyValue);

        $timezoneValue = TimezoneValue::where('key', 'UTC')->first();

        $timezone = $profile->timezone()->create([
            'key' => $timezoneValue->key,
            'name' => '',
        ]);

        $timezone->timezoneValue()->associate($timezoneValue);
        $timezone->save();

        $profile->save();
    }

    protected function createUsername(User $user, $id = 0)
    {
        $username = str_slug($user->name);
        $allUsernames = $this->getRelatedUsernames($username, $id);
        if (!$allUsernames->contains('username', $username)) {
            $user->username = $username;
            $user->save();

            return $user;
        }
        for ($i = 1; $i <= 1000; $i++) {
            $newUsername = $username . '-' . $i;
            if (!$allUsernames->contains('username', $newUsername)) {
                $user->username = $newUsername;
                $user->save();

                return $user;
            }
        }
        throw new \Exception('Can not create a unique username');
    }

    protected function getRelatedUsernames($username, $id = 0)
    {
        return Profile::select('username')->where('username', 'like', $username . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
