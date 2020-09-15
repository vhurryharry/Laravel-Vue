<?php

namespace App\Console\Commands;

use App\Community;
use App\Event;
use App\Interest;
use App\Post;
use App\Profile;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all data to elasticsearch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $search)
    {
        parent::__construct();

        $this->search = $search;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


        $this->output->writeln('Indexing events...');

        $params = [
            'index' => 'events',
            'body' => [
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'start_date' => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                        ],
                        'end_date' => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                        ],
                        'starts_at' => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                        ],
                        'ends_at' => [
                            "type" => "date",
                            "format" => "yyyy-MM-dd'T'HH:mm:ss||yyyy-MM-dd||epoch_millis",
                        ],
                    ]
                ]
            ]
        ];

        $this->search->indices()->create($params);

        $reactionType = ReactionType::where('name', 'Like')->first();



        foreach (Event::cursor() as $model) {
            if ($model->privacy()->with('privacyValue')->first()['key'] !== 'secret') {
                if ($model->start_date !== null) {
                    $startDate = Carbon::createFromTimestampMs($model->start_date->timestamp)->format('Y-m-d\TH:i:s');
                } else {
                    $startDate = null;
                }

                $loveReactant = Event::where('slug', $model->slug)->with('loveReactant.reactions.reactant')
                ->joinReactionCounterOfType($reactionType)
                ->first();

                // $loveReactant = $model
                    // ->with('loveReactant.reactions.reactant')->get();

                    // $this->output->writeln($loveReactant->loveReactant->reactions);

            // break;

                $this->search->index([
                    'index' => $model->getSearchIndex(),
                    // 'type' => $model->getSearchType(),
                    'id' => $model->id,

                    'body' => [
                        'model_type' => $model->getSearchType(),
                        'id' => $model->id,
                        'name' => $model->title,
                        'title' => $model->title,
                        'slug' => $model->slug,
                        'profile_id' => $model->profile_id,
                        'privacy' => ($model->privacy) ? $model->privacy()->with('privacyValue')->first()['key'] : null,
                        'event_type' => $model->event_type,
                        'body' => $model->body,
                        // 'love_reactant' => ($model->with('loveReactant.reactions.reactant')) ? $model->with('loveReactant.reactions.reactant') : [],
                        'love_reactant' => ($model->loveReactant) ? $loveReactant->loveReactant  : null,
                        'participants' => $model->participants,
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
            }

            $this->output->write('.');
        }
        $this->output->writeln('');


        $this->output->writeln('Indexing communities...');

        foreach (Community::cursor() as $model) {
            if ($model->privacy()->with('privacyValue')->first()['key'] !== 'secret') {
                $this->search->index([
                    'index' => $model->getSearchIndex(),
                    'type' => $model->getSearchType(),
                    'id' => $model->id,
                    'body' => [
                        'model_type' => $model->getSearchType(),
                        'id' => $model->id,
                        'name' => $model->name,
                        'slug' => $model->slug,
                        'profile_id' => $model->profile_id,
                        'privacy' => ($model->privacy()->first()) ? $model->privacy()->with('privacyValue')->first()['key'] : null,
                        'love_reactant' => ($model->loveReactant) ? $model->loveReactant()->first()->getReactionTotal() : null,
                        'body' => $model->body,
                        'image_path' => $model->image_path,
                        'participants' => $model->participants()->get(),
                        'interests' => ($model->interests()->get() !== null) ? $model->interests()->get() : null,
                        'created_at' => $model->created_at->toDateString(),
                        'updated_at' => $model->updated_at->toDateString(),
                    ],
                ]);
            }

            foreach ($model->interests()->get() as $interest) {
                // dump($tempInterest);

                // $interest = Interest::find($tempInterest->interest_id);
                $id = (string) Str::uuid();
                // dump($interest);
                $this->search->index([
                    'index' => 'interests',
                    'type' => 'interests',
                    'id' => $id,
                    'body' => [
                        'model_type' => 'interests',
                        'id' => $id,
                        'name' => $interest->name,
                        'key' => $interest->key,
                    ]
                ]);
            }

            $this->output->write('.');
        }

        $this->output->writeln('');

        $this->output->writeln('Indexing profiles...');

        foreach (Profile::cursor() as $model) {
            if ($model->load('searchabilityPrivacy')->searchabilityPrivacy['key'] !== 'hidden_from_search') {
                $this->search->index([
                    'index' => $model->getSearchIndex(),
                    'type' => $model->getSearchType(),
                    'id' => $model->id,
                    'body' => [
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
                        // 'body' => $model->body,
                        'searchability_privacy' => $model->load('searchabilityPrivacy')->searchabilityPrivacy['key'],
                        'location' => ($model->location !== null) ? $model->location()->with('country')->first() : null,
                        'educations' => $model->educations,
                        'languages' => $model->languages,
                        'friends' => ($model->getFriends()) ? $model->getFriends() : null,
                        'timezone' => ($model->timezone()->first()) ? $model->timezone()->with('timezoneValue')->first()['key'] : null,
                        'created_at' => $model->created_at->toDateString(),
                        'updated_at' => $model->updated_at->toDateString(),
                        'deleted_at' => $model->deleted_at ? $model->deleted_at->toDateString() : null,
                    ],
                ]);
            }
            $this->output->write('.');
        }

        $this->output->writeln('');
        $this->output->writeln('Indexing posts...');

        foreach (Post::cursor() as $model) {
            // if ($model->load('searchabilityPrivacy')->searchabilityPrivacy['key'] !== 'hidden_from_search') {
            $this->search->index([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
                'body' => [
                    'model_type' => $model->getSearchType(),
                    'id' => $model->id,
                    'slug' => $model->slug,
                    'profile' => $model->profile,
                    'events' => $model->events,
                    'communities' => $model->communities,
                    'body' => $model->body,
                    'created_at' => $model->created_at->toDateString(),
                    'updated_at' => $model->updated_at->toDateString(),
                    'deleted_at' => $model->deleted_at ? $model->deleted_at->toDateString() : null,
                ],
            ]);
            // }
            $this->output->write('.');
        }
        $this->output->writeln('');

        $this->output->writeln("nDone!");
    }
}
