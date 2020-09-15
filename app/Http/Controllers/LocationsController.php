<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrCreateLocationRequest;
use App\Location;
use Illuminate\Http\Request;
use App\Country;

class LocationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('verified')->except(['get']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function updateOrCreateLocation(UpdateOrCreateLocationRequest $request)
    {

        $profile = auth()->user()->profile()->first();

        $location = Location::where('location_resource_id', '=', $profile->id)->first();
        $country = Country::where('iso_3166_2', $request->key)->first();
        // $timezoneValue = TimezoneValue::where('key', $request->timezone['key'])->first();

        // $profile->location()->attach($location);

        if (!$request->key) {
            $profile->location()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Location updated.',
                'profile' => $profile,
            ], 201);
        }

        if ($location) {
            $profile->location()->delete();
        }

        $location = $profile->location()->create([
            'key' => $request->key,
            'name' => $request->key,
        ]);
        // $location->save();

        $location->country()->associate($country);
        // $country->locations()->save($location);
        $location->save();
        $profile->touch();

        // $timezoneValue->timezone()->save($timezone);
        // $timezone->save();

        // if ($location === null) {
        //     $location = $profile->location()->create([
        //         'name' => $request->key,
        //         'key' => $request->key,
        //     ]);

        //     $country->location()->save($location);
        //     $location->save();
        // } else {
        //     if ($request->name) {
        //         $location->name = $request->name;
        //         $location->key = $request->key;
        //         $location->save();
        //         $profile->touch();
        //     } else {
        //         $location->delete();

        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Location cleared.',
        //         ], 200);
        //     }
        // }


        return response()->json([
            'success' => true,
            'message' => 'Location updated.',
            'location' => $location,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function get()
    {
        $countries = Country::where('name', "!=", "")->get();

        return response()->json([
            'success' => true,
            'message' => 'Countries fetched.',
            'countries' => $countries,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
