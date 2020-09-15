<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ActivityValue extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];
    protected $table = 'activities_values';

    // public function timezoneValue(){
    //     return $this->hasMany(Timezone::class);
    // }

    public function timezone()
    {
        return $this->hasOne(Activity::class, 'id');
    }

}
