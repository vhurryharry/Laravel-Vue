<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{
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

    public function privacyValue()
    {
        return $this->belongsTo(PrivacyValue::class, 'privacy_id');
    }
}
