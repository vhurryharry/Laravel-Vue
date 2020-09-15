<?php

namespace App\Listeners;

use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessLike implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded  $event
     */
    public function handle(ReactionHasBeenAdded $event)
    {

    }
}
