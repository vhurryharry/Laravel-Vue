<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\User;

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

$factory->define(App\User::class, function (Faker $faker) {
    $firstname = $faker->unique(true)->firstName;
    $lastname = $faker->unique(true)->lastName;
    $name = $firstname . ' ' . $lastname;

    $username = Str::slug($firstname . "-" . $lastname);
    $userRows = User::whereRaw("username REGEXP '^{$username}(-[0-9]*)?$'")->get();
    $countUser = count($userRows) + 1;

    $username = ($countUser > 1) ? "{$username}-{$countUser}" : $username;

    return [
        'name' => $name,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'username' => $username,
        'email' => $faker->unique(true)->safeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('12341234'), // secret
        'phone_number' => $faker->unique()->e164PhoneNumber,
        'remember_token' => str_random(10),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
