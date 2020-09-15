<?php

namespace App\Listeners;

use App\Profile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class BroadCastFriendshipRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Profile $event)
    {
        $profile = auth()->user()->profile;

        $content = [
            'message' => $event->name . ' sent you a friend request!',
            'from' => $profile->name,
            'to' => $event->id,
        ];

        $this->broadcastOn($content);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($message)
    {
        return new PrivateChannel('request.' . $message['to']);
    }
}
