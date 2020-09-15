<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TimezoneValue;
use App\Timezone;

class TimezonesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }

    /**
     * Display all items of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        // $timezones = TimezoneValue::all();
        $timezones = TimezoneValue::where('name', "!=", "")->get();

        // $temp = [];
        // foreach ($timezones as $timezone) {
            // if
            // $timezone->name = 'dddd';
            // array_push($temp, $timezone->name);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Timezones fetched.',
            'timezones' => $timezones,
        ], 200);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function show(Interest $interest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function edit(Interest $interest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interest $interest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interest $interest)
    {
        //
    }
}
