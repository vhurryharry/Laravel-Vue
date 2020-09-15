<?php

namespace App\Http\Controllers;

use App\Invitation;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Event;
use App\Community;
use App\EventParticipants;
use App\Events\FriendshipAccepted;

class InvitationsController extends Controller
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

    public function get()
    {
        $profile = Auth::user()->profile()->first();
        $eventInvitations = Invitation::where('profile_id', $profile->id)->where('invitations_type', 'App\Event')->get();
        $communityInvitations = Invitation::where('profile_id', $profile->id)->where('invitations_type', 'App\Community')->get();

        foreach ($eventInvitations as $key => $invitation) {
            $profileInviting = Profile::where('id', $invitation->sender_id)->first();
            $event = Event::where('id', $invitation->invitations_id)->first();
            $invitation->message = $profileInviting->name . __('shared.invited_you_to_join') . $event->name . '.';

            $invitation->profile = $profileInviting;

            if ($event) {
                $invitation->event = $event;
            }
        }

        foreach ($communityInvitations as $key => $invitation) {
            $profileInviting = Profile::where('id', $invitation->sender_id)->first();
            $community = Community::where('id', $invitation->invitations_id)->first();
            $invitation->message = $profileInviting->name . __('shared.invited_you_to_join') . $community->name . '.';

            $invitation->profile = $profileInviting;

            if ($community) {
                $invitation->community = $community;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitations fetched.',
            'eventInvitations' => $eventInvitations,
            'communityInvitations' => $communityInvitations,
            // 'communities' => $communities,
        ], 200);
    }

    public function acceptInvitation(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $invitationId = $request->all()['invitation_id'];
        $resourceType = $request->all()['invitations_type'];

        $invitation = Invitation::where('invitations_id', $invitationId);
        $resource = $resourceType::find($invitationId);

        $resource->participants()->attach($profile->id);


        $resource->invited()->detach($profile);
        $resource->save();

        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted.',
            'resource' => $resource,
        ], 200);
    }

    public function refuseInvitation(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $invitationId = $request->all()['invitation_id'];
        $resourceType = $request->all()['invitations_type'];
        $resource = $resourceType::find($invitationId);

        $resource->invited()->detach($profile);

        return response()->json([
            'success' => true,
            'message' => 'Invitation accepted.',
            'resource' => $resource,
        ], 200);
    }

    public function getFriendRequests(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $requests = $profile->getFriendRequests()->load('sender');

        foreach ($requests as $request) {
            $request->message = $request->sender->name . __('components/profiles.toast.sent_you_a_friend_request');
        }

        return response()->json([
            'success' => true,
            'message' => 'Requests fetcheds.',
            'requests' => $requests,
        ], 200);
    }

    public function acceptFriendRequest(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $sender = Profile::where('username', $request->username)->first();

        $profile->acceptFriendRequest($sender);
        $profile->save();
        $sender->touch();
        
        $content = [
            'to' => $sender->id,
            'profile' => $profile
        ];

        event(new FriendshipAccepted($content));

        return response()->json([
            'success' => true,
            'message' => 'Friend request accepted.',
        ], 200);
    }

    public function cancelFriendRequest(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $sender = Profile::where('username', $request->username)->first();

        $request = $profile->hasSentFriendRequestTo($sender);

        if ($request) {
            $friendship = $profile->getFriendship($sender);
            $friendship->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Friend request cancelled.',
        ], 200);
    }

    public function refuseFriendRequest(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $sender = Profile::where('username', $request->username)->first();

        $profile->denyFriendRequest($sender);

        return response()->json([
            'success' => true,
            'message' => 'Friend request refused.',
        ], 200);
    }

    public function unfriend(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $friend = Profile::where('username', $request->username)->first();

        $profile->unfriend($friend);

        return response()->json([
            'success' => true,
            'message' => 'Profile unfriended.',
        ], 200);
    }

    public function getInvitationsForResource(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $invitations = Invitation::where('invitations_id', $request->resource['id'])->get();
        $invitedProfiles = [];

        foreach ($invitations as $key => $invitation) {
            $invitedProfile = Profile::where('id', $invitation->profile_id)->first();
            $invitedProfiles[$invitedProfile->id] = $invitedProfile;
            $invitedProfile->friend_count = $invitedProfile->getFriendsCount();
            $invitedProfile->load('location.country');
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitations fetched.',
            'profiles' => $invitedProfiles,
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
