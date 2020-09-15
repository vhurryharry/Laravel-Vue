<?php

use Faker\Generator as Faker;

$factory->define(App\Interest::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'key' => strtolower($faker->firstName),
    ];
});
