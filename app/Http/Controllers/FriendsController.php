<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class FriendsController extends Controller
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
        return view('pages.friends');
    }


    public function all()
    {
        $profile = auth()->user()->profile()->first();
        $friends = $profile->getFriends()->load('location.country');


        $profiles = Profile::all()->except($profile->id)->load('educations')->load('location.country')->take(15);

        // User::whereNotIn('id', $user->following->lists('id')) // exclude already followed
        //     ->where('id', '<>', $user->id) // and the user himself
        //     ->take(..)->get();

        // $profiles->forget($key);

        // TODO REFACTOR
        foreach ($profiles as $key2 => $profileToCheck) {
            $outerMutuals = $profile->getMutualFriendsCount($profileToCheck);
            $hasFriendRequestFrom = $profile->hasFriendRequestFrom($profileToCheck);
            $friendRequestSent = $profile->hasSentFriendRequestTo($profileToCheck);

            $profileToCheck->mutual_count = $outerMutuals;
            $profileToCheck->has_friend_request_from = $hasFriendRequestFrom;
            $profileToCheck->friend_request_sent = $friendRequestSent;

            foreach ($friends as $key => $friend) {
                $mutuals = $profile->getMutualFriendsCount($friend);
                $friend->mutual_count = $mutuals;

                if ($friend['id'] === $profileToCheck['id']) {
                    $profiles->pull($key2);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profiles fetched.',
            'friends' => $friends,
            'profiles' => $profiles,
        ], 200);
    }

    public function getFriends()
    {
        $profile = auth()->user()->profile()->first();

        $friends = $profile->getFriends()->load('location.country');

        foreach ($friends as $friend) {
            $friend->friendship_record = $friend->getFriendship($profile);
            $friend->friends_count = $friend->getFriendsCount();
        }
        return response()->json([
            'success' => true,
            'message' => 'Friends fetched.',
            'friends' => $friends,
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
