<?php

namespace App\Observers;

use App\Timezone;
use Illuminate\Support\Str;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\TimezoneValue;

class TimezoneObserver
{
    // private $elasticsearch;

    // public function __construct(Client $elasticsearch)
    // {
    //     $this->elasticsearch = $elasticsearch;
    // }

    /**
     * Handle the event "created" event.
     *
     * @param  \App\Timezone  $Timezone
     * @return void
     */
    public function created(Timezone $timezone)
    {
            // $timezoneValue = TimezoneValue::where('key', $timezone->key)->first();
            // $timezone->timezoneValue()->save($timezoneValue);
    }

}
