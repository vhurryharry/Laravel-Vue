<?php

namespace App\Providers;

use App\Community;
use App\Observers\CommunityObserver;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use App\Event;
use App\Profile;
use App\Post;
use App\Observers\EventObserver;
use App\Observers\ProfileObserver;
use App\Observers\PostObserver;
use Hootlex\Friendships\Models\Friendship;
use App\Observers\FriendshipObserver;
use App\Timezone;
use App\Observers\TimezoneObserver;
use App\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Redis::enableEvents();

        Community::observe(CommunityObserver::class);
        Event::observe(EventObserver::class);
        Profile::observe(ProfileObserver::class);
        Post::observe(PostObserver::class);
        Post::observe(PostObserver::class);
        Friendship::observe(FriendshipObserver::class);
        Timezone::observe(TimezoneObserver::class);
        User::observe(UserObserver::class);
        $this->bindSearchClient();

        \Spatie\NovaTranslatable\Translatable::defaultLocales(['en', 'ar']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    { }

    private function bindSearchClient()
    {
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts(config('services.search.hosts'))
                ->build();
        });
    }
}
