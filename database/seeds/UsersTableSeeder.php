<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'email' => 'orokashu@gmail.com',
            'password' => bcrypt('12341234'),
            'verified' => true,
        ]);
    }
}
