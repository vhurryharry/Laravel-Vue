<?php

use App\Country;
use App\Education;
use App\Event;
use App\RelationshipPrivacyValue;
use App\TimezoneValue;
use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Profile::class, 500)->create()->each(function ($profile) {

            $privacyValue = RelationshipPrivacyValue::where('key', 'visible_to_all')->first();

            // $profile->privacy()->save($privacyValue);
            $profile->searchabilityPrivacy()->associate($privacyValue);
            $profile->myFriendsPrivacy()->associate($privacyValue);
            $profile->myEventsPrivacy()->associate($privacyValue);
            $profile->myCommunitiesPrivacy()->associate($privacyValue);
            // $profile->myLikesPrivacy()->associate($privacyValue);

            $timezoneValue = TimezoneValue::where('key', 'UTC')->first();

            $timezone = $profile->timezone()->create([
                'key' => $timezoneValue->key,
                'name' => '',
            ]);

            $timezone->timezoneValue()->associate($timezoneValue);
            $timezone->save();

            $randomCountry = Country::all()->random();

            $country = Country::where('iso_3166_2', $randomCountry->iso_3166_2)->first();

            $location = $profile->location()->create([
                'key' => $country->iso_3166_2,
                'name' => $country->name,
            ]);

            $location->country()->associate($country);
            $location->save();
            $profile->touch();

            $value = rand(1, 4);

            for ($i = 0; $i < $value; $i++) {
                $randomEducation = Education::all()->random();

                $profile->load(['educations' => function ($query) use ($randomEducation, $profile) {
                    if (!$query->find($randomEducation->id)) {
                        $profile->educations()->save($randomEducation);
                    }
                }]);
            }

            $profile->save();

            // $this->output->write('.');
        });
    }
}
