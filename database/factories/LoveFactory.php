<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\User;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

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

$factory->define(ReactionType::class, function (Faker $faker) {
    return [
        'name' => 'Like',
        'weight' => '1',
    ];
});
