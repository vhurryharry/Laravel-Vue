<?php

use Faker\Generator as Faker;

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

$factory->define(Hootlex\Friendships\Models\Friendship::class, function (Faker $faker) {
    $sender = App\Profile::all()->random();
    $receiver = App\Profile::all()->random();

    // if ($sender->id !== $receiver->id) {
        return [
            'sender_type' => 'App\Profile',
            'sender_id' => $sender->id,
            'recipient_type' => 'App\Profile',
            'recipient_id' => $receiver->id,
            'status' => $faker->randomElement(['0', '1']),
        ];
    // } else {
    //     return;
    // }

});
