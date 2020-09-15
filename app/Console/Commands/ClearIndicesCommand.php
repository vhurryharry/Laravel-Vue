<?php

namespace App\Console\Commands;

use App\Community;
use App\Event;
use App\Interest;
use App\Profile;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class ClearIndicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all indices in Elasticsearch';

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
        $this->output->writeln('Clearing indices...');

        $profilesIndex['index']  = 'profiles';
        $communitiesIndex['index']  = 'communities';
        $eventsIndex['index']  = 'events';
        $postsIndex['index']  = 'posts';
        $interestsIndex['index']  = 'interests';

        if ($this->search->indices()->exists($profilesIndex)) {
            $this->search->indices()->delete([
                'index' => ['profiles']
            ]);
        }

        if ($this->search->indices()->exists($communitiesIndex)) {
            $this->search->indices()->delete([
                'index' => ['communities']
            ]);
        }

        if ($this->search->indices()->exists($eventsIndex)) {
            $this->search->indices()->delete([
                'index' => ['events']
            ]);
        }

        if ($this->search->indices()->exists($postsIndex)) {
            $this->search->indices()->delete([
                'index' => ['posts']
            ]);
        }

        if ($this->search->indices()->exists($interestsIndex)) {
            $this->search->indices()->delete([
                'index' => ['interests']
            ]);
        }

        $this->output->writeln("nDone!");
    }
}
