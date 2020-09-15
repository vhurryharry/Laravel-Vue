<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class GenderValue extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];

    // public function timezoneValue(){
    //     return $this->hasMany(Timezone::class);
    // }
    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

}
