<?php

namespace App\Providers;

use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\UserCreated' => [
            'App\Listeners\CreateProfile',
        ],
        'friendships.sent' => [],
        'friendships.accepted' => [
            'App\Listeners\FriendshipsAccepted',
        ],
        // ReactionHasBeenAdded::class => [
        //     'App\Listeners\ProcessLike',
        // ],
        // ReactionHasBeenRemoved::class => [
        //     'App\Listeners\ProcessUnlike',
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
