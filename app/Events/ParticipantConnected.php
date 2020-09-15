<?php

namespace App\Events;

use App\Conference;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PublicChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantConnected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return ['my-channel'];

        // $conference = Conference::find($this->event['conferenceId']);

        return new PrivateChannel('conferences.' . $this->event['conferenceId']);
    }

    public function broadcastAs()
    {
        return 'App\Events\ParticipantConnected';
    }
}
