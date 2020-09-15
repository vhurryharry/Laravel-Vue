<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Education extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key', 'hidden'];
    public $translatable = ['name'];
    protected $table = 'education_values';

    public function profiles()
    {
        return $this->belongsToMany('App\Profile');
    }
}
