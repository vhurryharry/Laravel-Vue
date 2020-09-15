<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PrivacyValue extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'key'];
    public $translatable = ['name'];

    public function privacy()
    {
        return $this->hasMany(Privacy::class, 'id');
    }
}
