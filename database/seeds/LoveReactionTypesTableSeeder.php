<?php

use Illuminate\Database\Seeder;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class LoveReactionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ReactionType::class, 1)->create();
    }
}
