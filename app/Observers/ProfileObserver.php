<?php

namespace App\Observers;

use App\Event;
use Illuminate\Support\Str;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Faker\Provider\it_CH\PhoneNumber;

class ProfileObserver
{
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved($model)
    {
        if ($model->load('searchabilityPrivacy')->searchabilityPrivacy['key'] !== 'hidden_from_search') {
            $this->elasticsearch->index([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
                'body' => [
                    // 'mappings' => [
                    // ],
                    'model_type' => $model->getSearchType(),
                    'id' => $model->id,
                    'name' => $model->name,
                    'user_id' => $model->user_id,
                    'username' => $model->username,
                    'bio' => $model->bio,
                    'gender' => ($model->gender !== null) ? $model->gender()->first() : null,
                    'date_of_birth' => $model->date_of_birth,
                    'image_path' => $model->image_path,
                    'firstname' => $model->firstname,
                    'lastname' => $model->lastname,
                    'active' => $model->active,
                    'location' => ($model->location !== null) ? $model->location()->with('country')->first() : null,
                    'educations' => $model->educations,
                    'languages' => $model->languages,
                    'searchability_privacy' => $model->load('searchabilityPrivacy')->searchabilityPrivacy['key'],
                    'friends' => ($model->getFriends()) ? $model->getFriends() : null,
                    'timezone' => ($model->timezone()->first()) ? $model->timezone()->with('timezoneValue')->first()['key'] : null,
                    'created_at' => $model->created_at->toDateString(),
                    'updated_at' => $model->updated_at->toDateString(),
                    'deleted_at' => $model->deleted_at ? $model->deleted_at->toDateString() : null,
                ],
            ]);
        } else if ($model->searchabilityPrivacy) {
            $this->elasticsearch->delete([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
            ]);
        }
    }
}
