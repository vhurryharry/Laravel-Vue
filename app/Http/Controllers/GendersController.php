<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gender;

class GendersController extends Controller
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
        // $genders = Gender::all();
        $genders = Gender::where('name', "!=", "")->get();

        // $temp = [];
        // foreach ($timezones as $timezone) {
        // if
        // $timezone->name = 'dddd';
        // array_push($temp, $timezone->name);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Genders fetched.',
            'genders' => $genders,
        ], 200);
    }
}
