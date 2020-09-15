<?php

namespace App\Observers;

use App\Event;
use Illuminate\Support\Str;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Activity;
use App\ActivityValue;
use App\PrivacyValue;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class EventObserver
{
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved($model)
    {
        $reactionType = ReactionType::where('name', 'Like')->first();

        if ($model->privacy()->with('privacyValue')->first()['key'] !== 'secret') {
            $loveReactant = Event::where('slug', $model->slug)->with('loveReactant.reactions.reactant')
            ->joinReactionCounterOfType($reactionType)
            ->first();

            $this->elasticsearch->index([
                'index' => $model->getSearchIndex(),
                'id' => $model->id,
                'body' => [
                    'mappings' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'start_date' => [
                                'type' => 'date',
                                "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                            ],
                            'end_date' => [
                                'type' => 'date',
                                "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                            ],
                            'starts_at' => [
                                'type' => 'date',
                                "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                            ],
                            'ends_at' => [
                                'type' => 'date',
                                "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                            ],
                        ]
                    ],
                    'model_type' => $model->getSearchType(),
                    'id' => $model->id,
                    'name' => $model->title,
                    'title' => $model->title,
                    'slug' => $model->slug,
                    'profile_id' => $model->profile_id,
                    'privacy' => ($model->privacy) ? $model->privacy()->with('privacyValue')->first()['key']  : null,
                    'event_type' => $model->event_type,
                    'body' => $model->body,
                    'participants' => $model->participants()->get(),
                    'love_reactant' => $loveReactant->loveReactant,
                    'start_date' => ($model->start_date) ? $model->start_date->timestamp : null,
                    'end_date' => ($model->end_date) ? $model->end_date->timestamp : null,
                    'starts_at' => ($model->starts_at) ? $model->starts_at->timestamp : null,
                    'ends_at' => ($model->ends_at) ? $model->ends_at->timestamp : null,
                    'timezone' => ($model->timezone()->first()) ? $model->timezone()->with('timezoneValue')->first()['key'] : null,
                    'image_path' => $model->image_path,
                    'created_by' => $model->created_by,
                    'created_at' => $model->created_at->toDateString(),
                    'updated_at' => $model->updated_at->toDateString(),
                ],
            ]);
        } else if ($model->privacy) {
            $this->elasticsearch->delete([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
            ]);
        }
    }

    /**
     * Handle the event "created" event.
     *
     * @param  \App\event  $event
     * @return void
     */
    public function created(Event $event, $id = 0)
    {
        $this->createSlug($event, $id);


        $activityValue = ActivityValue::where('key', 'created_event')->first();
        $this->recordActivity($event, $activityValue);
    }

    public function updated(Event $event)
    {
        // $this->recordActivity($event, 'updated');
    }

    protected function recordActivity($event, $type)
    {
        $activity = Activity::create([
            'profile_id' => $event->profile_id,
            'subject_id' => $event->id,
            'subject_type' => 'App\Event',
            'key' => $type->key,
        ]);

        $activity->activityValue()->associate($type);
        $activity->save();
    }

    protected function createSlug(Event $event, $id = 0)
    {
        $slug = str_slug($event->name);
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.

        $allSlugs = $this->getRelatedSlugs($slug, $id);
        // If we haven't used it before then we are all good.
        
        if (!$allSlugs->contains('slug', $slug)) {
            $event->slug = $slug;
            $event->save();

            return $event;
            // return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 1000; $i++) {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                // return $newSlug;
                $event->slug = $newSlug;
                $event->save();

                return $event;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }


    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Event::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }


    /**
     * Handle the community "deleted" event.
     *
     * @param  \App\Community  $community
     * @return void
     */
    public function deleted(Event $event)
    {
        $this->elasticsearch->delete([
            'index' => $event->getSearchIndex(),
            'type' => $event->getSearchType(),
            'id' => $event->id,
        ]);
    }
}
