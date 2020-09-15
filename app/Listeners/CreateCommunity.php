<?php

namespace App\Listeners;

use App\Events\CommunityCreated;

class CreateCommunity
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    // public $community;

    public function __construct()
    {
        // $this->event = $community->community;
    }

    /**
     * Handle the event.
     *
     * @param  CreateCommunity  $community
     * @return void
     */
    public function handle(CommunityCreated $community)
    {

    }
}
