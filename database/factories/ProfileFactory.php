<?php

use App\Profile;
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

$factory->define(App\Profile::class, function (Faker $faker) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $userName = '';
    $fullName = str_slug($firstName . ' ' . $lastName);

    // = $this->getRelatedUsernames($username, 0);

    $allUsernames = Profile::select('username')->where('username', 'like', $fullName . '%')
        ->where('id', '<>', 0)
        ->get();

    if (!$allUsernames->contains('username', $fullName)) {
        $userName = $fullName;
    }

    for ($i = 1; $i <= 1000; $i++) {
        $newUsername = $fullName . '-' . $i;
        if (!$allUsernames->contains('username', $newUsername)) {
            $userName = $fullName;
        }
    }

    return [
        'name' => $fullName,
        'firstname' => $firstName,
        'lastname' => $lastName,
        'username' => $userName,
        'user_id' => App\User::all()->random()->id,
        'image_path' => 'profiles/default.png',
    ];
});
