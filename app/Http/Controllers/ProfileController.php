<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;
use App\Events\FriendshipRequested;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileController extends Controller
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
        $profile = auth()->user()->profile()->first();

        $profiles = Profile::all()->except($profile->id)->load('communities')->load('educations')->load('location')->take(15);

        foreach ($profiles as $key2 => $profileToCheck) {
            $outerMutuals = $profile->getMutualFriendsCount($profileToCheck);
            $hasFriendRequestFrom = $profile->hasFriendRequestFrom($profileToCheck);
            $friendRequestSent = $profile->hasSentFriendRequestTo($profileToCheck);
            $areFriends = $profile->isFriendWith($profileToCheck);

            $profileToCheck->mutual_count = $outerMutuals;
            $profileToCheck->has_friend_request_from = $hasFriendRequestFrom;
            $profileToCheck->friend_request_sent = $friendRequestSent;
            $profileToCheck->friend = $areFriends;
        }

        return response()->json([
            'success' => true,
            'message' => 'Profiles fetched successfully.',
            'profiles' => $profiles,
        ], 201);
    }

    public function profiles(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        if ($request->count === null) {
            $profiles = Profile::all()->except($profile->id)->load('communities')->load('educations')->load('location.country')->take(15);
        } else {
            $profiles = Profile::all()->except($profile->id)->load('communities')->load('educations')->load('location.country')->take($request->count);
        }

        foreach ($profiles as $key2 => $profileToCheck) {
            $outerMutuals = $profile->getMutualFriendsCount($profileToCheck);
            $hasFriendRequestFrom = $profile->hasFriendRequestFrom($profileToCheck);
            $friendRequestSent = $profile->hasSentFriendRequestTo($profileToCheck);
            $areFriends = $profile->isFriendWith($profileToCheck);

            $profileToCheck->mutual_count = $outerMutuals;
            $profileToCheck->has_friend_request_from = $hasFriendRequestFrom;
            $profileToCheck->friend_request_sent = $friendRequestSent;
            $profileToCheck->friend = $areFriends;
        }

        return response()->json([
            'success' => true,
            'message' => 'Profiles fetched successfully.',
            'profiles' => $profiles,
        ], 201);
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
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function show(string $username)
    {
        $authenticatedProfile = auth()->user()->profile()->first();
        $profile = Profile::where('username', '=', $username)->first()->load('location.country')->load('educations')->load('languages');
        $owner = false;

        $isFriend = $authenticatedProfile->isFriendWith($profile);
        $isBlocked = $authenticatedProfile->hasBlocked($profile);

        $hasFriendRequestFrom = $authenticatedProfile->hasFriendRequestFrom($profile);
        $friendRequestSent = $authenticatedProfile->hasSentFriendRequestTo($profile);
        $friends = $profile->getFriends()->load('location.country')->load('educations');
        $friendsArray = [];

        if ($authenticatedProfile->id !== $profile->id) {
            foreach ($friends as $key => $friend) {
                $friendship = $profile->getFriendship($friend);
                $privacyValue = $profile->load('myFriendsPrivacy')->myFriendsPrivacy->key;

                if ($privacyValue === 'visible_to_all') {
                } else if ($privacyValue === 'visible_only_to_friends') {
                    if (!$isFriend) {
                        $friends->forget($key);
                    }
                } else if ($privacyValue === 'visible_to_extended_friends') {
                    if (!$isFriend && $authenticatedProfile->getMutualFriendsCount($profile) === 0) {
                        $friends->forget($key);
                    }
                } else if ($privacyValue === 'hidden') {
                    $friends->forget($key);
                } else {
                    $friends->forget($key);
                }
            }
        }

        $friendCount = $profile->getFriendsCount();

        if ($profile->username === $authenticatedProfile->username) {
            $owner = true;
        }

        $myCommunities = $profile->communities->load('privacy')->load('participants')->take(15)
            ->load('loveReactant.reactions.reactant');


        foreach ($myCommunities as $key => $community) {
            $communityFriends = [];
            foreach ($community->participants as $participant) {
                $participant->has_friend_request_from = $authenticatedProfile->hasFriendRequestFrom($participant);
                $participant->friend_request_sent = $authenticatedProfile->hasSentFriendRequestTo($participant);

                if ($authenticatedProfile->isFriendWith($participant)) {
                    $participant->is_friend = true;
                    $communityFriends[$participant->id] = $participant;
                }
            }
            $community->mutuals = $communityFriends;
        }

        return view('pages.individual-profile', compact('profile', 'owner', 'friendCount', 'friends', 'isFriend', 'friendRequestSent', 'hasFriendRequestFrom', 'myCommunities', 'isBlocked'));
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
    public function update(Request $request, $username)
    {
        $profile = Profile::where('username', '=', $username)->first();

        $this->authorize('update', $profile);

        $profile->date_of_birth = $request->date_of_birth;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.',
            'profile' => $profile,
            'dateOfBirth' => $profile->date_of_birth,
        ], 201);
    }

    public function requestFriendship(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $recipient = Profile::where('username', $request->username)->first();

        $isBlocked = $recipient->hasBlocked($profile);
        if (!$isBlocked) {
            $profile->befriend($recipient);

            $content = [
                'message' => $profile->name . __('components/profiles.toast.sent_you_a_friend_request'),
                'from' => $profile->name,
                'to' => $recipient->id,
                'profile' => $profile
            ];

            event(new FriendshipRequested($content));
        }

        return response()->json([
            'success' => true,
            'message' => 'Friend request sent.',
        ], 201);
    }

    public function getBlockedProfiles(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $blockedProfiles = $profile->getBlockedFriendships()->load('recipient');

        return response()->json([
            'success' => true,
            'message' => 'Blocked profiles fetched.',
            'blocked_profiles' => $blockedProfiles,
        ], 201);
    }

    public function blockProfile(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $recipient = Profile::where('username', $request->username)->first();

        $profile->blockFriend($recipient);

        return response()->json([
            'success' => true,
            'message' => 'Profile blocked.',
        ], 201);
    }

    public function unblockProfile(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $recipient = Profile::where('username', $request->username)->first();

        $profile->unblockFriend($recipient);

        return response()->json([
            'success' => true,
            'message' => 'Profile blocked.',
        ], 201);
    }

    public function acceptFriendship(Request $request)
    { }

    public function refuseFriendship(Request $request)
    { }

    public function ignoreFriendship(Request $request)
    { }

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
