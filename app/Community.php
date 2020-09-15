<?php

namespace App;

use App\Search\Searchable;
use Illuminate\Database\Eloquent\Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Community extends Model implements ReactableContract
{
    use Searchable, Reactable;

    protected $fillable = ['slug', 'profile_id', 'name', 'community_privacy', 'body', 'image_path'];
    protected $touches = ['participants'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    // protected $dispatchesEvents = [
    //     'created' => CommunityCreated::class,
    // ];

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

    public function report()
    {
        return $this->morphOne('App\Report', 'reportable');
    }

    public function interests()
    {
        return $this->belongsToMany('App\Interest', 'community_interest', 'community_id', 'interest_id');
    }

    public function events()
    {
        return $this->belongsToMany('App\Event', 'community_event');
    }

    public function unsubscribe($profileId = null)
    {
        $this->participants()->where('profile_id', '=', $profileId)->delete();
    }

    public function participants()
    {
        return $this->belongsToMany('App\Profile', 'community_participants', 'community_id', 'profile_id')->with('friends')->with('location')->withTimestamps();
    }

    public function posts()
    {
        return $this->belongsToMany('App\Post', 'community_posts', 'community_id', 'post_id')->withTimestamps();
    }

    public function makeAdmin($profile = null)
    {
        return $this->participants($profile)->toBase()->update([
            'admin' => 1,
        ]);
    }

    public function admins()
    {
        return $this->belongsToMany('App\Profile', 'community_participants', 'community_id', 'profile_id')->withTimestamps();
    }

    public function privacy()
    {
        return $this->morphOne('App\Privacy', 'privacy_resource');
    }

    public function invite($profile = null, $communityName = null)
    {
        $profile = $profile ?: Auth::user()->profile()->first();

        return $this->invited()->attach(
            $profile,
            [
                'sender_id' => auth()->user()->profile()->first()->id,
                'message' => auth()->user()->profile()->first()->name . ' invited you to join ' . $communityName . '.'
            ]
        );
    }

    public function invited()
    {
        return $this->morphToMany('App\Profile', 'invitations')->withTimestamps();
    }

    public function request($profile = null)
    {
        $profile = $profile ?: Auth::user()->profile()->first();

        return $this->requested()->attach($profile, ['sender_id' => auth()->user()->profile()->first()->id]);
    }

    public function requested()
    {
        return $this->morphToMany('App\Profile', 'requests')->withTimestamps();
    }


    public function location()
    {
        return $this->morphOne('App\Location', 'location_resource');
    }
}
