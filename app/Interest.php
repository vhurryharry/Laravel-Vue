<?php

namespace App;

use App\Search\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Interest extends Model
{
    use Searchable, HasTranslations;
    // protected $primaryKey = ['community_id', 'interest_id'];

    protected $fillable = ['name', 'key'];
    public $incrementing = true;
    public $translatable = ['name'];

    public function communities()
    {
        return $this->belongsToMany('App\Community', 'community_interest', 'interest_id', 'community_id');
    }
}
