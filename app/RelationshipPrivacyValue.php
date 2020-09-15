<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class RelationshipPrivacyValue extends Model
{
    use HasTranslations;

    protected $table = 'r_privacies_values';

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];

    public function privacy()
    {
        return $this->hasMany(RelationshipPrivacy::class, 'id');
    }

    public function myFriendsPrivacy()
    {
        // return $this->belongsTo('App\Profile', 'id');
        return $this->hasMany('App\Profile', 'id');
    }

    public function myEventsPrivacy()
    {
        return $this->hasMany('App\Profile', 'id');
    }

    public function myCommunitiesPrivacy()
    {
        return $this->hasMany('App\Profile', 'id');
    }
}
