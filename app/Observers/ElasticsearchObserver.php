<?php

namespace App\Observers;

use Elasticsearch\Client;
use Illuminate\Support\Facades\Log;

class ElasticsearchObserver
{
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function updated($model)
    {

    }

    public function saving($model)
    {

    }

    public function deleted($model)
    {
        $this->elasticsearch->delete([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            'id' => $model->id,
        ]);
    }
}
