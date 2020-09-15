<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;
use App\Profile;
use App\Event;
use App\Community;
use App\ActivityValue;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get($username)
    {
        // $profile = auth()->user()->profile()->first();
        $profile = Profile::where('username', '=', $username)->first();

        // $friendships = Activity::where('key', 'friendship_created')->where('profile_id', $profile->id)->orWhere('subject_id', $profile->id)->get();
        $friendships = Activity::where('key', 'friendship_created')
            ->where(function ($query) use ($profile) {
                $query->where('profile_id', $profile->id)->orWhere('subject_id', $profile->id);
            })->get();

        // $friendships = Activity::where('key', 'friendship_created')->where('profile_id', $profile->id)->get();
        foreach ($friendships as $activity) {
            if ($activity->subject_id !== $profile->id) {
                $target = Profile::where('id', $activity->subject_id)->first();
                $activityValue = ActivityValue::where('id', $activity->activity_id)->first();

                $activity->target = $target;
                $activity->activity_value = $activityValue;
            } else {
                $target = Profile::where('id', $activity->profile_id)->first();
                $activityValue = ActivityValue::where('id', $activity->activity_id)->first();

                $activity->target = $target;
                $activity->activity_value = $activityValue;
            }
        }

        $resources = Activity::where('profile_id', $profile->id)->get();


        $resourcesArray = [];

        foreach ($resources as $resource) {
            if ($resource->subject_type === "App\Event") {
                $target = Event::where('id', $resource->subject_id)->first();
                $activityValue = ActivityValue::where('id', $resource->activity_id)->first();

                $resource->target = $target;
                $resource->activity_value = $activityValue;
                array_push($resourcesArray, $resource);
            } else if ($resource->subject_type === "App\Community") {
                $target = Community::where('id', $resource->subject_id)->first();
                $activityValue = ActivityValue::where('id', $resource->activity_id)->first();

                $resource->target = $target;
                $resource->activity_value = $activityValue;
                array_push($resourcesArray, $resource);
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Activities fetched.',
            'friendship_activity' => $friendships,
            'resource_activity' => $resourcesArray
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
