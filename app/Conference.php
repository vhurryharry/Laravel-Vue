<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $fillable = ['profile_id', 'event_id'];
    public $incrementing = true;

    public function members()
    {
        return $this->belongsToMany('App\Profile', 'conferences');
    }
}
