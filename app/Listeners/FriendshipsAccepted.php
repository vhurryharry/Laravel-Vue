<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Hootlex\Friendships\Models\Friendship;
use App\Activity;
use App\ActivityValue;
use App\PrivacyValue;
use App\RelationshipPrivacyValue;

class FriendshipsAccepted
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $profile = auth()->user()->profile()->first();

        $activityValue = ActivityValue::where('key', 'friendship_created')->first();

        $activity = Activity::create([
            'profile_id' => $event->sender_id,
            'subject_id' => $event->recipient_id,
            'subject_type' => $event->recipient_type,
            'key' => $activityValue->key,
        ]);

        $activity->activityValue()->associate($activityValue);
        $activity->save();

    }
}
