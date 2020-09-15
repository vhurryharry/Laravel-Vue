<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(App\Community::class, function (Faker $faker) {
    return [
        'name' => $faker->bs,
        'body' => $faker->text(),
        'profile_id' => App\Profile::all()->random()->id,
        'image_path' => 'communities/default.png',

        'community_privacy' => $faker->randomElement(['public', 'private', 'secret']),

        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
