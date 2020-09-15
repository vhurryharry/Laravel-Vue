<?php

use Illuminate\Database\Seeder;

class FriendshipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Hootlex\Friendships\Models\Friendship::class, 2500)->create()->each(function ($u) {
            // $u->profile()->save(factory(App\Profile::class)->make());
        });
    }
}
