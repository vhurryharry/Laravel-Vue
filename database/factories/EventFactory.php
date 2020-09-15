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

$factory->define(App\Event::class, function (Faker $faker) {
    $bs = $faker->bs;

    return [
        'title' => $bs,
        'name' => $bs,
        'body' => $faker->text(),
        'profile_id' => App\Profile::all()->random()->id,
        'image_path' => 'events/default.png',
        'event_privacy' => $faker->randomElement(['public', 'private', 'secret']),
        'event_type' => $faker->randomElement(['one-time', 'endless']),
        'created_by' => $faker->randomElement(['community', 'profile']),
        'start_date' => $faker->dateTimeBetween($startDate = 'now', $endDate = '+2 months', $timezone = 'Asia/Muscat'),
        'end_date' => $faker->dateTimeBetween($startDate = '+2 weeks', $endDate = '+3 months', $timezone = 'Asia/Muscat'),
        'starts_at' => strtotime($faker->time),
        'ends_at' => strtotime($faker->time),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
