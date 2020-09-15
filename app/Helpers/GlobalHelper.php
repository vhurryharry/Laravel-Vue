<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class GlobalHelper
{
    public function bladeHelper()
    {
        if (auth()->user()) {
            $profile = auth()->user()->profile->load('searchabilityPrivacy')->load('myFriendsPrivacy')->load('myEventsPrivacy')->load('myCommunitiesPrivacy')->load('location.country')->load('educations')->load('languages')->load('timezone.timezoneValue');
            $profile->gender = $profile->gender;

            $profile->friends = $profile->getFriends()->load('location.country');

            foreach ($profile->friends as $friend) {
                $friend->friendship_record = $friend->getFriendship($profile);
            }

            return $profile;
        } else {
            return null;
        }
    }

    public function getLocale()
    {
        $locale = App::getLocale();
        return $locale;
    }

    public static function instance()
    {
        return new GlobalHelper();
    }
}
