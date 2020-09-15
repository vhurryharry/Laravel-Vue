<?php

namespace App\Observers;

use App\Community;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Activity;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;
use App\ActivityValue;

class CommunityObserver
{

    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved(Community $community)
    {
        if ($community->privacy()->with('privacyValue')->first()['key'] !== 'secret') {
            $this->elasticsearch->index([
                'index' => $community->getSearchIndex(),
                'type' => $community->getSearchType(),
                'id' => $community->id,
                'body' => [
                    'model_type' => $community->getSearchType(),
                    'id' => $community->id,
                    'name' => $community->name,
                    'slug' => $community->slug,
                    'profile_id' => $community->profile_id,
                    'likes_total' =>  ($community->loveReactant) ? $community->loveReactant()->first()->getReactionTotal() : null,
                    // 'community_privacy' => $community->community_privacy,
                    'privacy' => ($community->privacy()->first()) ? $community->privacy()->with('privacyValue')->first()['key'] : null,
                    'body' => $community->body,
                    'image_path' => $community->image_path,
                    'participants' => $community->participants()->get(),
                    'interests' => ($community->interests()->get() !== null) ? $community->interests()->get() : null,
                    'created_at' => $community->created_at->toDateString(),
                    'updated_at' => $community->updated_at->toDateString(),
                ],
            ]);
        } else if ($community->privacy) {
            $this->elasticsearch->delete([
                'index' => $community->getSearchIndex(),
                'type' => $community->getSearchType(),
                'id' => $community->id,
            ]);
        }
        // }
    }
    /**
     * Handle the community "created" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function created(Community $community, $id = 0)
    {
        $this->createSlug($community, $id);

        $activityValue = ActivityValue::where('key', 'created_community')->first();

        $this->recordActivity($community, $activityValue);
    }

    protected function recordActivity($community, $type)
    {
        $activity = Activity::create([
            'profile_id' => $community->profile_id,
            'subject_id' => $community->id,
            'subject_type' => 'App\Community',
            'key' => $type->key
        ]);

        $activity->activityValue()->associate($type);
        $activity->save();
    }

    protected function createSlug(Community $community, $id = 0)
    {
        $slug = str_slug($community->name);
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        if (!$allSlugs->contains('slug', $slug)) {
            $community->slug = $slug;
            $community->save();

            return $community;
        }

        for ($i = 1; $i <= 1000; $i++) {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $community->slug = $newSlug;
                $community->save();

                return $community;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Community::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }

    /**
     * Handle the community "updated" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function updated(Community $community)
    { }

    /**
     * Handle the community "deleted" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function deleted(Community $community)
    {
        $this->elasticsearch->delete([
            'index' => $community->getSearchIndex(),
            'type' => $community->getSearchType(),
            'id' => $community->id,
        ]);
    }

    /**
     * Handle the community "restored" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function restored(Community $community)
    {
        //
    }

    /**
     * Handle the community "force deleted" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function forceDeleted(Community $community)
    {
        //
    }
}
