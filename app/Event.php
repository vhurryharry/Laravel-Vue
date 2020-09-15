<?php

namespace App;

use App\Search\Searchable;
use Illuminate\Database\Eloquent\Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Event extends Model implements ReactableContract
{
    use Searchable, Reactable;

    public $timestamps = true;

    protected $fillable = ['slug', 'name', 'profile_id', 'title', 'event_type', 'start_date', 'end_date', 'starts_at', 'ends_at', 'timezone', 'event_privacy', 'body', 'image_path', 'created_by', 'created_at', 'updated_at'];
    protected $dates = ['start_date', 'end_date', 'starts_at', 'ends_at'];
    protected $touches = ['participants'];

    // protected $casts = [
    //     'start_date' => 'datetime:yyyy-MM-dd', // Change your format
    //     'start_at' => 'datetime:yyyy-MM-dd', // Change your format
    // ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    // protected $dispatchesEvents = [
    //     'created' => EventCreated::class,
    // ];

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

    public function report()
    {
        return $this->morphOne('App\Report', 'reportable');
    }

    public function community()
    {
        return $this->belongsToMany('App\Community', 'community_event');
    }

    public function unsubscribe($profileId = null)
    {
        $this->participants()->where('profile_id', '=', $profileId)->delete();
    }

    public function participants()
    {
        return $this->belongsToMany('App\Profile', 'event_participants', 'event_id', 'profile_id')->withTimestamps();
    }

    // public function conference()
    // {
    //     return $this->belongTo('App\Conference');
    // }

    public function posts()
    {
        return $this->belongsToMany('App\Post', 'event_posts', 'event_id', 'post_id')->withTimestamps();
    }

    public function makeAdmin($profile = null)
    {
        return $this->participants($profile)->toBase()->update([
            'admin' => 1,
        ]);
    }

    public function admins()
    {
        return $this->belongsToMany('App\Profile', 'event_participants', 'event_id', 'profile_id')->withTimestamps();
    }

    // public function participantsCount()
    // {
    //     return $this->belongsToMany('App\Profile', 'event_participants', 'event_id', 'profile_id')
    //         ->selectRaw('profile_id, count(*) as count')
    //         ->groupBy('profile_id');
    // }

    public function privacy()
    {
        return $this->morphOne('App\Privacy', 'privacy_resource');
    }

    public function timezone()
    {
        return $this->morphOne('App\Timezone', 'timezone_resource');
    }

    public function invite($profile = null, $eventName = null)
    {
        $profile = $profile ?: Auth::user()->profile()->first();

        return $this->invited()->attach(
            $profile,
            [
                'sender_id' => auth()->user()->profile()->first()->id,
                'message' => auth()->user()->profile()->first()->name . ' invited you to join ' . $eventName . '.'
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

    public function conversation()
    {
        return $this->hasOne('App\Conversation');
    }
}
