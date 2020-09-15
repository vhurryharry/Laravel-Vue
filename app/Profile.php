<?php

namespace App;

use App\Search\Searchable;
use App\Traits\Friendable;
use Illuminate\Database\Eloquent\Model;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;

class Profile extends Model implements ReacterableContract
{
    use Friendable, Searchable, Reacterable;

    // protected $table = 'profiles';
    protected $fillable = ['user_id', 'name', 'firstname', 'lastname', 'username', 'city', 'country', 'telephone', 'date_of_birth', 'image_path', 'bio'];
    protected $dates = ['date_of_birth'];

    // protected $touches = ['acceptFriendRequest'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function image()
    {
        return asset($this->image_path ?: 'images/profiles/default.jpeg');
    }

    public function eventOwner()
    {
        return $this->hasOne('App\Event');
    }

    public function events()
    {
        return $this->belongsToMany('App\Event', 'event_participants', 'profile_id', 'event_id')->withTimestamps();
    }

    public function relationshipPrivacy()
    {
        return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');
    }

    public function myCommunitiesPrivacy()
    {
        // return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');

        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_communities_privacy_id');
    }

    public function myEventsPrivacy()
    {
        // return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');

        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_events_privacy_id');
    }

    public function myFriendsPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_friends_privacy_id');
        // return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');
    }

    public function searchabilityPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'searchability_privacy_id');
    }

    public function myLikesPrivacy()
    {

        // return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');
        return $this->hasMany(RelationshipPrivacyValue::class, 'id');
    }

    public function communityOwner()
    {
        return $this->hasOne('App\Community');
    }

    public function communities()
    {
        return $this->belongsToMany('App\Community', 'community_participants', 'profile_id', 'community_id')->withTimestamps();
    }

    public function admins($community = null)
    {
        return $this->communities($community)->toBase()->update([
            'admin' => 1,
        ]);
    }

    public function invited()
    {
        return $this->morphToMany('App\Profile', 'invitations')->withTimestamps();
    }

    public function report()
    {
        return $this->morphOne('App\Report', 'reportable');
    }

    public function requested()
    {
        return $this->morphToMany('App\Profile', 'requests')->withTimestamps();
    }

    public function timezone()
    {
        return $this->morphOne('App\Timezone', 'timezone_resource');
    }

    public function location()
    {
        return $this->morphOne('App\Location', 'location_resource');
        // return $this->morphOne('App\Location', 'location_resource')->where('hidden', '!=', '1');
    }

    public function languages()
    {
        return $this->belongsToMany('App\Language');
    }

    public function educations()
    {
        return $this->belongsToMany('App\Education')->withPivot('hidden');
        // return $this->belongsToMany('App\Education')->withPivot('hidden')->where('education_profile.hidden', '!=', '1');

    }

    public function gender()
    {
        return $this->belongsToMany('App\Gender');
    }

    public function getGenderAttribute()
    {
        return $this->gender()->first();
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
