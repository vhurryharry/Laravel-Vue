<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use App\Event;
use App\Profile;
use App\Conference;
use App\Events\HallwayUpdated;
use stdClass;
use App\Events\ParticipantConnected;
use App\Events\ParticipantDisconnected;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class ConferencesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }

    public function hallway()
    {
        return view('pages.conferences');
    }

    public function eventHallway($slug)
    {
        return view('pages.conferences', compact('slug'));
    }

    public function updateHallway(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $request->slug)->first();

        event(new HallwayUpdated([
            'conferenceId' => $event->id,
            'hallway' => $request->hallway,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Hallway updated.',
        ], 200);
    }

    public function enterConference(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $request->slug)->first();

        $exists = Conference::where('profile_id', $profile->id)->first();


        if (!$exists) {
            Conference::create([
                'profile_id' => $profile->id,
                'event_id' => $event->id
            ]);
        } else {
            $exists->delete();

            Conference::create([
                'profile_id' => $profile->id,
                'event_id' => $event->id
            ]);
        }

        $conferenceMembers = Conference::where('event_id', $event->id)->get();

        foreach ($conferenceMembers as $conference) {
            $member = Profile::where('id', $conference->profile_id)->first();
            $member->circles = [];
            $conference->member = $member;
            $conference->circles = [];
        }

        event(new ParticipantConnected([
            'conferenceId' => $event->id,
            'member' => $profile,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'conference fetched.',
            'conferenceMembers' => $conferenceMembers,
        ], 200);
    }

    public function leaveConference(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $request->slug)->first();

        $exists = Conference::where('profile_id', $profile->id)->first();

        if ($exists) {
            $exists->delete();
        }

        event(new ParticipantDisconnected([
            'conferenceId' => $event->id,
            'member' => $profile,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Participant disconnected.',
        ], 200);
    }

    public function pollOnlineParticipants()
    {
    }

    public function getCircle(string $slug)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $slug)->with('participants.location.country')->with('privacy')->with('posts')->first();

        $posts = $event->posts->load('profile.location.country');

        $friends = [];
        foreach ($event->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent= $profile->hasSentFriendRequestTo($participant);
            $participant->friend_count = $participant->getFriendsCount();

            if ($participant->id === $profile->id) {
                $event->member = true;
            }

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            } else {
                $participant->is_friend = false;
            }
        }

        $event->friends = $friends;
        $event->members = new stdClass();

        $eventId = $event->id;

        $profilesThatCanBeInvited = Profile::whereDoesntHave('events', function ($participants) use ($eventId) {
            return $participants->where('event_id', '=', $eventId);
        })->paginate(15);

        foreach ($profilesThatCanBeInvited as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->is_friend = $profile->isFriendWith($participant);
        }

        return response()->json([
            'success' => true,
            'message' => 'event fetched.',
            'event' => $event->load('profile')->load('privacy.privacyValue'),
            'profiles_that_can_be_invited' => $profilesThatCanBeInvited,
            'posts' => $posts
        ], 200);
    }

    public function generateAccessToken(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $accountSid = Config::get('twilio.account_id');
        $apiKeySid = Config::get('twilio.video_sid');
        $apiKeySecret = Config::get('twilio.video_secret');

        $identity = $profile->username;

        $token = new AccessToken(
            $accountSid,
            $apiKeySid,
            $apiKeySecret,
            3600,
            $identity
        );

        $grant = new VideoGrant();
        $grant->setRoom($request->circle_name);
        $token->addGrant($grant);
        $authToken = $token->toJWT();
        

        return response()->json([
            'success' => true,
            'message' => '.',
            'token' => $authToken,
        ]);
    }
}
