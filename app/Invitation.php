<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invitation extends Model
{
    protected $guarded = [];
    // protected $fillable = ['profile_id', 'resource_id', 'sender_id', 'response', 'message', 'date_sent', 'date_accepted', 'date_refused'];
}
