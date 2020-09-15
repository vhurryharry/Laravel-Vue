<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TimezoneValue extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];

    public function timezone()
    {
        return $this->hasMany(Timezone::class, 'id');
    }
}
