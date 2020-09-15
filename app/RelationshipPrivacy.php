<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelationshipPrivacy extends Model
{
    protected $table = 'r_privacies';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'key'];
    public $incrementing = true;

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function resource()
    {
        return $this->morphTo('privacy_resource');
    }

    public function searchabilityPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'searchability_privacy_id');
    }

    public function myFriendsPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_friends_privacy_id');
    }

    public function myEventsPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_events_privacy_id');
    }

    public function myCommunitiesPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_communities_privacy_id');
    }

    public function myLikesPrivacy()
    {
        return $this->belongsTo(RelationshipPrivacyValue::class, 'my_likes_privacy_id');
    }
}
