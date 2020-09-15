<?php

use App\Event;
use App\PrivacyValue;
use App\Profile;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Event::class, 350)->create()->each(function ($u) {
            // $u->privacy()->create([
            //     'name' => ucfirst($u->event_privacy),
            //     'key' => $u->event_privacy,
            // ]);

            // $items = ['public', 'private', 'secret'];

            $profile = Profile::all()->random(1);
            // $profile->eventOwner()->save($u);
            $u->participants()->attach($profile);
            $u->makeAdmin($profile);

            // $randomValue = Faker::randomElement(['public', 'private', 'secret']);
            // $randomValue = $items[array_rand($items)];

            $privacyValue = PrivacyValue::where('key', $u->event_privacy)->first();

            $privacy = $u->privacy()->create([
                'name' => $u->event_privacy,
                'key' => $privacyValue->key,
            ]);

            $privacy->privacyValue()->associate($privacyValue);

            $privacy->save();

            $reactionType = ReactionType::fromName('Like');

            $likeRandom = rand(1, 9);

            for ($i = 0; $i < $likeRandom; $i++) {
                $randomProfile = App\Profile::all()->random();
                $reacter = $randomProfile->getLoveReacter();

                // $resource = Event::where('slug', $u->slug)
                //     ->joinReactionCounterOfType($reactionType)
                //     ->first();
                $resource = Event::where('slug', $u->slug)->first();

                $reactant = $resource->getLoveReactant();

                $isReacted = $randomProfile
                    ->getLoveReacter()
                    ->isReactedToWithType($reactant, $reactionType);

                if (!$isReacted) {
                    $reacter->reactTo($reactant, $reactionType);
                }
            }


            $value = rand(1, 13);

            for ($i = 0; $i < $value; $i++) {
                $randomProfile = App\Profile::all()->random();

                if (!$u->participants()->exists()) {
                    $u->participants()->save($randomProfile);
                } else {
                    $u->load(['participants' => function ($query) use ($randomProfile, $u) {
                        if (!$query->find($randomProfile->id)) {
                            $u->participants()->save($randomProfile);
                        }
                    }]);
                }
            }
        });
    }
}
