<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Language extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key', 'hidden'];
    public $translatable = ['name'];
    protected $table = 'language_values';

    public function profiles()
    {
        return $this->belongsToMany('App\Profile');
    }
}
