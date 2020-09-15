<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = true;

    protected $fillable = ['category', 'status', 'date', 'description', 'sender_id', 'resource_id', 'resource_type'];
    protected $dates = ['date'];

    public function resource()
    {
        return $this->morphTo('resource');
    }

    public function reportable()
    {
        return $this->morphTo();
    }
}
