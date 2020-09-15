<?php

namespace App\Listeners;

use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessUnlike implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved  $event
     */
    public function handle(ReactionHasBeenRemoved $event)
    {

    }
}
