@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<individual-profile
:profile="{{ $profile }}"
:owner="{{ json_encode($owner) }}"
:is_friend="{{ json_encode($isFriend) }}"
:friend_request_sent="{{ json_encode($friendRequestSent) }}"
:has_friend_request_from="{{ json_encode($hasFriendRequestFrom) }}"
:friends="{{ $friends }}"
:friend_count="{{ json_encode($friendCount) }}"
:my_communities="{{ json_encode($myCommunities) }}"
:is_blocked="{{ json_encode($isBlocked) }}"

></individual-profile>
@endsection
