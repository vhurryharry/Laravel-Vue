<?php

namespace App\Observers;

use Hootlex\Friendships\Models\Friendship;
use Carbon\Carbon;
use App\Activity;

class FriendshipObserver
{
    /**
     * Handle the friendship "created" event.
     *
     * @param  \Hootlex\Friendships\Models\Friendship  $friendship
     * @return void
     */
    public function created(Friendship $friendship, $id = 0)
    {
        // $this->recordActivity($friendship, 'friendship_created');
    }

    public function saved(Friendship $friendship)
    {
    }

    /**
     * Handle the friendship "updated" event.
     *
     * @param  \Hootlex\Friendships\Models\Friendship  $friendship
     * @return void
     */
    public function updated(Friendship $friendship)
    {
    }

    protected function recordActivity($event, $type)
    {
        Activity::create([
            'profile_id' => $event->sender_id,
            'subject_id' => $event->recipient_id,
            'subject_type' => $event->recipient_type,
            'description' => $type
        ]);
    }

    /**
     * Handle the friendship "deleted" event.
     *
     * @param  \Hootlex\Friendships\Models\Friendship  $friendship
     * @return void
     */
    public function deleted(Friendship $friendship)
    {
        //
    }

    /**
     * Handle the friendship "restored" event.
     *
     * @param  \Hootlex\Friendships\Models\Friendship  $friendship
     * @return void
     */
    public function restored(friendship $friendship)
    {
        //
    }

    /**
     * Handle the friendship "force deleted" event.
     *
     * @param  \Hootlex\Friendships\Models\Friendship  $friendship
     * @return void
     */
    public function forceDeleted(friendship $friendship)
    {
        //
    }
}
