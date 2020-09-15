<?php

use App\Community;
use App\PrivacyValue;
use App\Profile;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class CommunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Community::class, 125)->create()->each(function ($u) {
            $profile = Profile::all()->random(1);
            $u->participants()->attach($profile);
            $u->makeAdmin($profile);

            $privacyValue = PrivacyValue::where('key', $u->community_privacy)->first();

            $privacy = $u->privacy()->create([
                'name' => $u->community_privacy,
                'key' => $privacyValue->key,
            ]);

            $privacy->privacyValue()->associate($privacyValue);

            $privacy->save();

            $reactionType = ReactionType::fromName('Like');

            $likeRandom = rand(1, 9);

            for ($i = 0; $i < $likeRandom; $i++) {
                $randomProfile = App\Profile::all()->random();
                $reacter = $randomProfile->getLoveReacter();

                $resource = Community::where('slug', $u->slug)
                    ->joinReactionCounterOfType($reactionType)
                    ->first();

                $reactant = $resource->getLoveReactant();

                $isReacted = $randomProfile
                    ->getLoveReacter()
                    ->isReactedToWithType($reactant, $reactionType);

                if (!$isReacted) {
                    $reacter->reactTo($reactant, $reactionType);
                }
            }

            $value = rand(1, 4);
            $members = rand(1, 13);

            for ($i = 0; $i < $value; $i++) {
                $randomInterest = App\Interest::all()->random();

                if (!$u->interests()->exists()) {
                    $u->interests()->save($randomInterest);
                } else {
                    $u->load(['interests' => function ($query) use ($randomInterest, $u) {
                        if (!$query->find($randomInterest->id)) {
                            $u->interests()->save($randomInterest);
                        }
                    }]);
                }
            }

            for ($i = 0; $i < $members; $i++) {
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
