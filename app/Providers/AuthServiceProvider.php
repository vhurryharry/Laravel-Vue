<?php

namespace App\Providers;

use App\Community;
use App\Event;
use App\Interest;
use App\Policies\CommunityPolicy;
use App\Policies\EventPolicy;
use App\Policies\InterestPolicy;
use App\Policies\PrivacyPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\UserPolicy;
use App\Privacy;
use App\Profile;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Profile::class => ProfilePolicy::class,
        Event::class => EventPolicy::class,
        Community::class => CommunityPolicy::class,
        Privacy::class => PrivacyPolicy:: class,
        Interest::class => InterestPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Passport::routes();
    }
}
