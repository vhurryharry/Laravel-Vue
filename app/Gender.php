<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Gender extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];
    protected $table = 'gender_values';

    // public function timezoneValue(){
    //     return $this->hasMany(Timezone::class);
    // }
    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }
}


// <?php

// namespace App;

// use Illuminate\Database\Eloquent\Model;
// use Spatie\Translatable\HasTranslations;

// class Gender extends Model
// {
//     use HasTranslations;

//     // protected $fillable = ['name', 'key'];
//     // public $translatable = ['name'];
//     protected $table = ['gender_profile'];


//     public function profile()
//     {
//         return $this->belongsTo('App\Profile');
//     }
// }
