<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventParticipants extends Model
{
    // protected $primaryKey = 'event_id';

    protected $guarded = [];
    public $incrementing = true;

    public function privacy()
    {
        return $this->morphOne('App\RelationshipPrivacy', 'privacy_resource');
    }
}
