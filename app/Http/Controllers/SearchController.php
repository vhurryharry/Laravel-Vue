<?php

namespace App\Http\Controllers;

use App\Interest;
use Elasticsearch\Client;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Location;
use App\Education;
use App\Country;
use App\Profile;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    private $search;

    public function __construct(Client $client)
    {
        $this->search = $client;
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.search');
    }

    public function search2()
    {
        $items = $this->search->search([
            'index' => ['profiles', 'communities'],
            'type' => ['profiles', 'communities'],
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
                'from' => 0,
                'size' => 15,
            ],
        ]);

        $collection = $this->buildCollection($items);

        return $collection;
    }

    public function all()
    {
        $match = Input::get("match");
        $fields = Input::get("fields");
        $query = Input::get("query");

        $filters = null;

        if (isset(Input::get("filters")['interests'])) {
            $filters['interests'] = Input::get("filters")['interests'];
        }

        $all = ['communities', 'events'];

        return $this->search($query, $all, $all, $filters);
    }




    public function communities()
    {
        $match = Input::get("match");
        $fields = Input::get("fields");
        $query = Input::get("query");

        $filters = null;

        if (isset(Input::get("filters")['interests'])) {
            $filters['interests'] = Input::get("filters")['interests'];
        } else {
            $filters['interests'] = null;
        }

        if (isset(Input::get("filters")['type'])) {
            $filters['type'] = Input::get("filters")['type'];
        } else {
            $filters['type'] = null;
        }

        if (isset(Input::get("filters")['ranges'])) {
            $filters['ranges'] = Input::get("filters")['ranges'];
        } else {
            $filters['ranges'] = null;
        }

        return $this->search($query, 'communities', 'communities', $filters);
    }

    public function events()
    {
        $match = Input::get("match");
        $fields = Input::get("fields");
        $query = Input::get("query");

        $filters = null;

        if (Input::get("filters")) {
            if (isset(Input::get("filters")['interests'])) {
                $filters['interests'] = Input::get("filters")['interests'];
            }

            if (isset(Input::get("filters")['ranges'])) {
                $filters['ranges'] = Input::get("filters")['ranges'];
            }
        }

        return $this->search($query, 'events', 'events', $filters);
    }


    public function posts()
    {
        $match = Input::get("match");
        $fields = Input::get("fields");
        $query = Input::get("query");

        $filters = null;

        return $this->search($query, 'posts', 'posts', $filters);
    }


    public function query()
    {
        $query = Input::get("query");
        $interests['interests'] = Input::get("interests");

        return Input::get("interests");
    }

    public function interests()
    {
        $query = Input::get("q");
        $filters = null;

        $interests = Interest::has('communities')->withCount('communities')->get();
        // return $this->search($query, 'interests', 'interests', $filters);

        return $interests;
    }

    public function educations()
    {
        $q = Input::get("q");

        $educations = Education::has('profiles')->withCount('profiles')->get();

        return $educations;
    }

    public function locations()
    {
        $q = Input::get("q");


        $locations = Location::all()->load('country')->groupBy(function ($item, $key) {
            return substr($item['key'], -3);
        });

        $groupCount = $locations->map(function ($item, $key) {
            return collect($item)->count();
        });


        return response()->json([
            'success' => true,
            'message' => 'Locations fetched.',
            'locations' => $locations,
            'count' => $groupCount
        ], 200);
    }

    public function profiles()
    {
        $authenticatedProfile = auth()->user()->profile()->first();

        $match = Input::get("match");
        $fields = Input::get("fields");
        $query = Input::get("query");
        $page = Input::get("page");

        $filters = null;

        if (isset(Input::get("filters")['location'])) {
            $filters['location'] = Input::get("filters")['location'];
        }

        if (isset(Input::get("filters")['education'])) {
            $filters['education'] = Input::get("filters")['education'];
        }

        $result = $this->search($query, 'profiles', 'profiles', $filters, null, false, null, 30, ($page) ? $page : 0);

        foreach ($result as $key => $profile) {
            if ($profile['searchability_privacy'] && $profile['searchability_privacy'] === 'visible_to_extended_friends') {
                $profile = Profile::find($profile['id']);

                $isFriend = $authenticatedProfile->isFriendWith($profile);

                if (!$isFriend && $authenticatedProfile->getMutualFriendsCount($profile) === 0) {
                    $result->forget($key);
                }
            }
        }

        return $result;
    }

    private function search($query, $index = null, $type = null, $filters = null, $not_filters = null, $aggs = false, $sort = null, $size = 30, $page_num = 1)
    {
        $searchParameters = $this->constructSearchQuery($query, $index, $type, $filters, null, false, null, $size, $page_num);

        $items = $this->searchOnElasticsearch($searchParameters);

        return $this->buildCollection($items);
    }

    private static function constructSearchQuery($query, $index, $type, $filters, $not_filters, $aggs, $sort, $size, $page_num)
    {

        $now = Carbon::now();
        $searchParameters = [];
        $aggregates = [];
        $filter = [];
        $profile = auth()->user()->profile()->first();

        $match['bool']['must'] = [];
        if ($query === null) {
            $match['bool']['filter']['match_all'] = (object) [];
        } else {

            $match['bool']['filter']['regexp'] = [
                'name' => ".*" . $query . ".*",
            ];
        }

        if (isset($filters['location'])) {
            array_push(
                $match['bool']['must'],
                [
                    'bool' => [
                        'filter' => [
                            'match' => [
                                'location.key' => $filters['location']
                            ]
                        ]
                    ]
                ]
            );
        }

        if (isset($filters['education'])) {
            array_push(
                $match['bool']['must'],
                [
                    'bool' => [
                        'filter' => [
                            'match' => [
                                'educations.key' => $filters['education']
                            ]
                        ]
                    ]
                ]
            );
        }

        if (isset($filters['interests'])) {
            array_push(
                $match['bool']['must'],
                [
                    'bool' => [
                        'filter' => [
                            'match' => [
                                'interests.key' => $filters['interests']
                            ]
                        ]
                    ]
                ]
            );
        }

        if (isset($filters['type'])) {
            if ($filters['type'] === 'popular') {
                $searchParameters['body'] = [
                    'sort' => [
                        'love_reactant.count' => [
                            'order' => 'desc'
                        ]
                    ],
                ];
            } else {
                array_push(
                    $match['bool']['must'],
                    [
                        'bool' => [
                            'filter' => [
                                'match' => [
                                    'privacy' => $filters['type']
                                ]
                            ]
                        ]
                    ]
                );
            }
        }

        if (isset($filters['ranges'])) {
            foreach ($filters['ranges'] as $range) {
                if ($range === "this-month") {
                    $startOfMonth = Carbon::parse(new Carbon('first day of this month'))->timestamp;
                    $endOfMonth = Carbon::parse(new Carbon('last day of this month'))->timestamp;

                    array_push(
                        $match['bool']['must'],
                        [
                            'bool' => [
                                'filter' => [
                                    'range' => [
                                        "start_date" => [
                                            "gte" => $startOfMonth,
                                            "lte" => $endOfMonth,
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    );
                }

                if ($range === "this-week") {
                    $startOfWeek = Carbon::now()->copy()->tz($profile->timezone->timezoneValue->key)->startOfWeek()->timestamp;
                    $endOfWeek = Carbon::now()->copy()->tz($profile->timezone->timezoneValue->key)->endOfWeek()->timestamp;

                    array_push(
                        $match['bool']['must'],
                        [
                            'bool' => [
                                'filter' => [
                                    'range' => [
                                        "start_date" => [
                                            "gte" => $startOfWeek,
                                            "lte" => $endOfWeek,
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    );
                }

                if ($range === "today") {
                    $startOfDay = Carbon::now()->copy()->startOfDay()->timestamp;
                    $endOfDay = Carbon::now()->copy()->endOfDay()->timestamp;

                    array_push(
                        $match['bool']['must'],
                        [
                            'bool' => [
                                'filter' => [
                                    'range' => [
                                        "start_date" => [
                                            "gte" => $startOfDay,
                                            "lte" => $endOfDay,
                                        ],
                                    ],
                                ],
                            ],
                        ]
                    );
                }

                if ($range === "now") {
                    $now = Carbon::now()->copy()->tz($profile->timezone->timezoneValue->key)->timestamp;

                    $startOfDay = Carbon::now()->copy()->startOfDay()->timestamp;
                    $endOfDay = Carbon::now()->copy()->endOfDay()->timestamp;
                    array_push(
                        $match['bool']['must'],
                        [
                            'bool' => [
                                'filter' => [
                                    [
                                        'range' => [
                                            'starts_at' => [
                                                "lte" => $now,
                                            ],
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            'ends_at' => [
                                                "gte" => $now,
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    );
                }

                if ($range === "endless") {
                    array_push(
                        $match['bool']['must'],
                        [
                            'bool' => [
                                'filter' => [
                                    'match' => [
                                        'event_type' => 'endless'
                                    ]
                                ]
                            ]
                        ]
                    );
                }
            }
        }

        // switch ($type) {
        //     case 'profiles':
        //         $searchParameters['type'] = "profiles";

        //         break;
        //     case 'communities':
        //         $searchParameters['type'] = "communities";

        //         break;
        //     case 'interests':
        //         $searchParameters['type'] = 'interests';

        //         break;
        //     case 'events':
        //         $searchParameters['index'] = 'events';

        //         break;
        //     default:
        //         $searchParameters['type'] = ['communities', 'events'];

        //         break;
        // }

        switch ($index) {
            case 'profiles':
                $searchParameters['index'] = "profiles";

                break;
            case 'communities':
                $searchParameters['index'] = "communities";

                break;
            case 'interests':
                $searchParameters['index'] = 'interests';

                break;
            case 'events':
                $searchParameters['index'] = 'events';

                break;

            case 'posts':
                $searchParameters['index'] = 'posts';

                break;
            default:
                $searchParameters['index'] = ['communities', 'events'];

                break;
        }

        if ($index !== 'interests') {
            // dump($page_num);
            $searchParameters['body'] = [
                'query' => $match,
                'from' => $page_num,
                'size' => 30,
            ];
        } else {

            $searchParameters['body'] = [
                'query' => $match,
                'from' => intval($page_num) * 10,
                'size' => 1000,
                'aggs' => [
                    'terms_aggregation' => [
                        'terms' => [
                            'field' => 'interests.key',
                        ],
                        'aggs' => [
                            'grouped_result' => [
                                'top_hits' => [
                                    'size' => 99
                                ]
                            ]
                        ]
                    ],
                ]
            ];
        }

        return $searchParameters;
    }

    private function searchOnElasticsearch($parameters): array
    {
        return $this->search->search($parameters);
    }

    private function buildCollection(array $items)
    {
        /**
         * The data comes in a structure like this:
         *
         * [
         *      'hits' => [
         *          'hits' => [
         *              [ '_source' => 1 ],
         *              [ '_source' => 2 ],
         *          ]
         *      ]
         * ]
         *
         * And we only care about the _source of the documents.
         */

        // dump($items);

        $hits = array_pluck($items['hits']['hits'], '_source') ?: [];

        $sources = array_map(function ($source) {
            // The hydrate method will try to decode this
            // field but ES gives us an array already.
            // $source['tags'] = json_encode($source['tags']);
            return $source;
        }, $hits);

        return $collection = collect($sources);

        // dd( Community::hydrate($sources));
        // We have to convert the results array into Eloquent Models.
        // return Community::hydrate($sources);
    }
}
