<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityParticipants extends Model
{
    protected $guarded = [];
    public $incrementing = true;

    public function privacy()
    {
        return $this->morphOne('App\Privacy', 'privacy_resource');
    }
}
