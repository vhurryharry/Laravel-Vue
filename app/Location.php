<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Location extends Model
{

    use HasTranslations;

    // protected $primaryKey = 'location_resource_id';
    protected $fillable = ['name', 'key', 'hidden', 'country_id'];
    public $translatable = ['name'];
    public $incrementing = true;

    // public function profile()
    // {
    //     return $this->belongTo('App\Profile');
    // }

    public function resource()
    {
        return $this->morphTo('location_resource');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // public function location_resource()
    // {
    //     return $this->morphTo('location_resource');
    // }
}
