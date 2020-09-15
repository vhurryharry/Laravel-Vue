<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as R;
use App\Event;
use App\Request;
use App\Profile;
use App\Community;
use App\Events\ProfileRequested;

class RequestsController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
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

    public function requestInvitation(R $request)
    {
        $profile = auth()->user()->profile()->first();

        if ($request->event) {
            $event = Event::where('slug', '=', $request->event['slug'])->first();
            $event->request($profile->id);

            $content = [
                'message' => $profile->name . ' ' . __('shared.requested_an_invitation_to') . ' ' . $event->title,
                'from' => $profile->name,
                'to' => $event->profile()->first()->id,
            ];

            event(new ProfileRequested($content));
        } else if ($request->community) {
            $community = Community::where('slug', '=', $request->community['slug'])->first();
            $community->request($profile->id);

            $content = [
                'message' => $profile->name . ' ' . __('shared.requested_an_invitation_to') . ' ' . $community->title,
                'from' => $profile->name,
                'to' => $community->profile()->first()->id,
            ];

            event(new ProfileRequested($content));
        }

        return response()->json([
            'success' => true,
            'message' => 'Request sent.',
        ], 200);
    }

    public function acceptRequest(R $request)
    {
        $profile = auth()->user()->profile()->first();

        $requestId = $request->all()['request_id'];
        $resourceType = $request->all()['requests_type'];
        $requesterId = $request->all()['requester_id'];

        $request = Request::where('requests_id', $requestId);
        $resource = $resourceType::find($requestId);
        $requester = Profile::find($requesterId);

        $resource->participants()->attach($requester);
        $resource->requested()->detach($requester);
        $resource->save();

        return response()->json([
            'success' => true,
            'message' => 'Request accepted.',
            'resource' => $resource,
        ], 200);
    }

    public function refuseRequest(R $request)
    {
        $profile = auth()->user()->profile()->first();
        // $sender = Profile::where('username', $request->username)->first();

        $requesterId = $request->all()['requester_id'];
        $resourceType = $request->all()['requests_type'];
        $requestId = $request->all()['request_id'];
        $resource = $resourceType::find($requestId);
        $requester = Profile::find($requesterId);

        // $request = Request::where('sender_id', $requesterId)
        //     ->where('requests_type', $resourceType)
        //     ->where('requests_id', $requestId)->first();


        $resource->requested()->detach($requester);


        // $profile->denyFriendRequest($sender);

        return response()->json([
            'success' => true,
            'message' => 'Request refused.',
        ], 200);
    }

    public function get()
    {
        $profile = auth()->user()->profile()->first();
        $requestsArray = [];

        foreach ($profile->events as $key => $event) {
            $admin = $event->participants()->where([
                ['profile_id', '=', $profile->id],
                ['admin', '=', 1],
            ])->exists();

            if ($admin) {
                $requests = Request::where('requests_id', $event->id)->get();

                if ($requests->count()) {
                    foreach ($requests as $key => $request) {
                        $profileRequesting = Profile::where('id', $request->sender_id)->first();
                        $request->profile = $profileRequesting;
                        $request->event = $event;

                        $request->message = $profileRequesting->name . __('shared.requested_an_invitation_to') . $event->title . '.';

                        array_push($requestsArray, $request);
                    }
                }
                // if ($request) {
                //     $profileRequesting = Profile::where('id', $request->sender_id)->first();
                //     $request->profile = $profileRequesting;
                //     $request->event = $event;

                //     array_push($requests, $request);
                // }
            }
        }

        foreach ($profile->communities as $key => $community) {
            $admin = $community->participants()->where([
                ['profile_id', '=', $profile->id],
                ['admin', '=', 1],
            ])->exists();

            if ($admin) {
                $requests = Request::where('requests_id', $community->id)->get();

                if ($requests->count()) {
                    foreach ($requests as $key => $request) {
                        $profileRequesting = Profile::where('id', $request->sender_id)->first();
                        $request->profile = $profileRequesting;
                        $request->community = $community;
                        $request->message = $profileRequesting->name . __('shared.requested_an_invitation_to') . $community->name . '.';


                        array_push($requestsArray, $request);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Requests fetched.',
            'requests' => $requestsArray
            // 'communities' => $communities,
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
    public function store(R $request)
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
    public function update(R $request, $id)
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
