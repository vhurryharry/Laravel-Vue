<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesSeeder::class);
        $this->call(InterestsTableSeeder::class);
        $this->call(EducationsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(TimezonesTableSeeder::class);
        $this->call(GendersTableSeeder::class);
        $this->call(PrivacyValuesTableSeeder::class);
        $this->call(RelationshipPrivacyValuesTableSeeder::class);
        $this->call(ActivityValuesTableSeeder::class);
        $this->call(LoveReactionTypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(CommunitiesTableSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(FriendshipsTableSeeder::class);
    }
}
