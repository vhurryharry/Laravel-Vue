<?php

namespace App;

use App\Search\Searchable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;
    
    protected $fillable = ['slug', 'body', 'content_path', 'type'];

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

    public function events()
    {
        return $this->belongsToMany('App\Event', 'event_posts', 'post_id', 'event_id')->withTimestamps();
    }

    public function communities()
    {
        return $this->belongsToMany('App\Community', 'community_posts', 'post_id', 'community_id')->withTimestamps();
    }

    public function report()
    {
        return $this->morphOne('App\Report', 'reportable');
    }
}
