<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = ['name', 'activity_id'];
    public $incrementing = true;

    public function subject()
    {
        return $this->morphTo();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function activityValue()
    {
        return $this->belongsTo(activityValue::class, 'activity_id');
    }
}
