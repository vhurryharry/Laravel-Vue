<?php

namespace App\Observers;

use App\Post;
use Illuminate\Support\Str;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function saved(Post $post)
    {
        $this->elasticsearch->index([
            'index' => $post->getSearchIndex(),
            'type' => $post->getSearchType(),
            'id' => $post->id,
            'body' => [
                'model_type' => $post->getSearchType(),
                'id' => $post->id,
                'slug' => $post->slug,
                'profile' => $post->profile,
                'events' => $post->events,
                'body' => $post->body,
                'communities' => $post->communities,
                'created_at' => $post->created_at->toDateString(),
                'updated_at' => $post->updated_at->toDateString(),
                'deleted_at' => $post->deleted_at ? $post->deleted_at->toDateString() : null,
            ],
        ]);
        // }
        // $this->output->write('.');
    }

    /**
     * Handle the event "created" event.
     *
     * @param  \App\event  $event
     * @return void
     */
    public function created(Post $post, $id = 0)
    {
        $slug = str_slug(strtok($post->body, " "));
        $allSlugs = $this->getRelatedSlugs($slug, $id);
        if (!$allSlugs->contains('slug', $slug)) {
            $post->slug = $slug;
            $post->save();

            return $post;
        }

        for ($i = 1; $i <= 1000; $i++) {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $post->slug = $newSlug;
                $post->save();

                return $post;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Post::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
