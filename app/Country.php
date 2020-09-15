<?php

namespace App;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    protected $table = 'countries';

    public function locations()
    {
        return $this->hasMany('App\Location', 'id');
    }
}
