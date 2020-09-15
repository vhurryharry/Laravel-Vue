<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Timezone extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key', 'timezone_id'];
    public $incrementing = true;

    public function resource()
    {
        return $this->morphTo('location_resource');
    }

    public function timezoneValue()
    {
        return $this->belongsTo(TimezoneValue::class, 'timezone_id');
    }
}
