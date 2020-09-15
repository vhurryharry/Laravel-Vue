<?php

namespace App\Http\Controllers;

use App\Community;
use App\Event;
use App\Events\EventCreated;
use App\Events\ProfileInvited;
use App\Http\Requests\CreateEventRequest;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Conversation;
use Illuminate\Support\Carbon;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Intervention\Image\Facades\Image;
use App\Post;
use App\Request as RequestResource;
use App\Invitation;
use Camroncade\Timezone\Facades\Timezone;
use Storage;
use App\Timezone as AppTimezone;
use App\TimezoneValue;
use App\Http\Requests\EditEventRequest;
use App\Conference;
use DateTimeZone;
use DateTimeImmutable;
use App\EventParticipants;
use App\PrivacyValue;
use App\RelationshipPrivacyValue;

class EventsController extends Controller
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
        return view('pages.events');
    }

    public function joinEvent(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $event = Event::where('slug', $request->slug)->with('participants')->with('privacy.privacyValue')->with('posts')->with('timezone.timezoneValue')->first();

        if ($event->privacy->privacyValue->key === 'public') {
            $event->participants()->attach($profile);
            $event->member = true;

            $invitation = Invitation::where('invitations_id', $event->id);

            if ($invitation) {
                $event->invited()->detach($profile);
            }
            return response()->json([
                'success' => true,
                'message' => 'Event joined.',
                'event' => $event->load('participants.location.country'),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'error',
            ], 400);
        }
    }

    public function leaveEvent(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $event = Event::where('slug', $request->slug)->with('participants.location.country')->with('privacy.privacyValue')->with('posts')->with('timezone.timezoneValue')->first();

        $event->participants()->detach($profile);
        $event->member = false;

        return response()->json([
            'success' => true,
            'message' => 'Event left.',
            'event' => $event,
        ], 200);
    }

    public function all()
    {
        $profile = auth()->user()->profile()->first();
        $reactionType = ReactionType::where('name', 'Like')->first();

        $events = Event::where('profile_id', '!=', $profile->id)->with('privacy.privacyValue')->with('participants')->with('loveReactant.reactions.reactant')->with('timezone.timezoneValue')
            ->joinReactionCounterOfType($reactionType)
            ->take(15)
            ->get();

        foreach ($events as $key => $event) {
            if ($event->privacy && $event->privacy->privacyValue->key === 'secret') {
                $events->forget($key);
            } else {
                foreach ($event->participants as $participant) {
                    $member = $event->participants()->where([
                        ['profile_id', '=', $profile->id],
                        ['event_id', '=', $event->id],
                    ])->exists();

                    if ($member) {
                        $events->forget($key);
                    } else {
                        $privacyValue = $participant->load('myFriendsPrivacy')->myFriendsPrivacy['key'];
                        $isFriend = $profile->isFriendWith($participant);
                        if ($privacyValue === 'visible_to_all') {
                        } else if ($privacyValue === 'visible_only_to_friends') {
                            if (!$isFriend) {
                                $events->forget($key);
                            }
                        } else if ($privacyValue === 'visible_to_extended_friends') {
                            if (!$isFriend && $authenticatedProfile->getMutualFriendsCount($profile) === 0) {
                                $events->forget($key);
                            }
                        } else if ($privacyValue === 'hidden') {
                            $events->forget($key);
                        } else {
                            $events->forget($key);
                        }
                    }
                }
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Fetched events.',
            'events' => $events,
        ], 200);
    }

    public function myEvents()
    {
        $profile = auth()->user()->profile()->first();
        $reactionType = ReactionType::where('name', 'Like')->first();

        $myEvents = $profile->events()->with('privacy.privacyValue')->with('participants.location.country')
            ->with('loveReactant.reactions.reactant')
            ->with('timezone.timezoneValue')
            ->joinReactionCounterOfType($reactionType)
            ->get();

        foreach ($myEvents as $event) {
            $friends = [];

            foreach ($event->participants as $key => $participant) {
                $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
                $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
                $participant->friend_count = $participant->getFriendsCount();

                if ($profile->isFriendWith($participant)) {
                    $participant->is_friend = true;
                    $friends[$participant->id] = $participant;
                }
                // }
            }
            $event->mutuals = $friends;
        }

        return response()->json([
            'success' => true,
            'message' => 'Events fetched.',
            'events' => $myEvents,
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

    public function inviteProfiles(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $eventSlug = $request->event_slug;
        $event = Event::where('slug', '=', $eventSlug)->first();
        $conversation = $event->conversation;

        foreach ($request->guest_list as $guestProfile) {
            $guestProfile = Profile::where('id', $guestProfile['id'])->first();

            $isBlocked = $profile->hasBlocked($guestProfile);

            if (!$isBlocked) {
                $invitation = Invitation::where('profile_id', $guestProfile['id'])
                    ->where('invitations_id', $event->id)
                    ->where('invitations_type', 'App\Event')->first();

                if (!$invitation) {
                    $invitation = $event->invite($guestProfile['id'], $event->title);
                }

                $conversationMember = $conversation->members->contains($guestProfile['id']);

                if (!$conversationMember) {
                    $conversation->addMembers(null, Profile::find($guestProfile['id']));
                }

                $content = [
                    'message' => __('components/profiles.received_an_invitation') . $event->title . '!',
                    'from' => $guestProfile->name,
                    'to' => $guestProfile->id,
                ];

                event(new ProfileInvited($content));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitations sent.',
            'event' => $event->load('participants')->load('privacy')->load('loveReactant.reactions.reactant')
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEventRequest $request)
    {
        $user = auth()->user()->profile()->first();

        if ($request->all()['image']) {
            $file = $request->all()['image'];
            $image = Image::make(file_get_contents($file));

            $mime = $image->mime();
            if ($mime == 'image/jpeg') {
                $extension = '.jpg';
            } else if ($mime == 'image/png') {
                $extension = '.png';
            } else if ($mime == 'image/gif') {
                $extension = '.gif';
            } else {
                $extension = '';
            }

            $image_url = "event-" . time() . $extension;
            $path = storage_path('/app/public/images/events/' . $image_url);
            $image = $image->save($path);
        }

        $startDate = Carbon::parse($request->start_date)->timestamp;

        if ($request->event_type === "endless") {
            $endDate = null;
        } else {
            $endDate = Carbon::parse($request->end_date)->timestamp;
        }

        $timezoneTime = Timezone::convertFromUTC(Carbon::parse($request->starts_at), $request->timezone,  'Y-m-d H:i:s');

        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = Carbon::parse($request->ends_at);

        $event = $user->eventOwner()->create([
            'name' => $request->title,
            'title' => $request->title,
            'body' => $request->body,
            'event_type' => $request->event_type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'starts_at' => $startsAt,
            'ends_at' => $request->ends_at ? $endsAt : null,
            'event_privacy' => $request->event_privacy,
            'created_by' => ($request->created_by) ? 'community' : 'profile',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'image_path' => ($request->all()['image']) ? "events/" . $image->filename . $extension : 'events/default.png',
        ]);

        $timezoneValue = TimezoneValue::where('key', $request->timezone)->first();

        $timezone = $event->timezone()->create([
            'key' => $request->timezone,
            'name' => $request->timezone,
        ]);

        $timezone->timezoneValue()->associate($timezoneValue);
        $timezone->save();

        $event->participants()->attach($user);
        $event->makeAdmin($user);

        $privacyValue = RelationshipPrivacyValue::where('key', $user->load('myFriendsPrivacy')->myFriendsPrivacy->key)->first();

        $conversation = $event->conversation()->create();
        $conversation->addMembers(null, $user);

        if ($request->created_by !== null) {
            $community = Community::where('slug', $request->created_by['slug'])->first();
            $community->events()->attach($event);

            $communityMembers = $community->participants->except($user->id);

            foreach ($communityMembers as $member) {
                $event->invite($member['id']);

                $content = [
                    'message' => __('components/profiles.received_an_invitation') . $event->title . '!',
                    'from' => $user->name,
                    'to' => $member['id'],
                ];

                event(new ProfileInvited($content));
            }
        }

        event(new EventCreated([
            'from' => $user->id,
            'event' => $event->load('privacy'),
        ]));

        $privacyValue = PrivacyValue::where('key', $request->event_privacy)->first();
        $privacy = $event->privacy()->create([
            'name' => '',
            'key' => $privacyValue->key,
        ]);

        $privacy->privacyValue()->associate($privacyValue);
        $privacy->profile()->associate($user);
        $privacy->save();

        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully.',
            'event' => $event->slug,
            'event_object' => $event->load('participants'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {

        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $slug)->first();

        $invited = $event->invited()->where([
            ['profile_id', '=', $profile->id],
            ['invitations_id', '=', $event->id],
        ])->exists();

        $member = $event->participants()->where([
            ['profile_id', '=', $profile->id],
            ['event_id', '=', $event->id],
        ])->exists();

        if ($event->privacy->privacyValue->key === 'secret' && !$invited && !$member) {
            return view('pages.events');
        }

        return view('pages.event', compact('slug'));
    }

    public function updateEvent(EditEventRequest $request)
    {
        $profile = auth()->user()->profile()->first();
        $slug = $request->all()['slug'];
        $event = Event::where('slug', $slug)->first();
        $privacy = $request->all()['event_privacy'];
        $imageSame = false;

        if ($request->all()['image']) {
            $file = $request->all()['image'];

            if ($file !== $event->image_path) {
                $image = Image::make(file_get_contents($file));

                $mime = $image->mime();

                if ($mime == 'image/jpeg') {
                    $extension = '.jpg';
                } else if ($mime == 'image/png') {
                    $extension = '.png';
                } else if ($mime == 'image/gif') {
                    $extension = '.gif';
                } else {
                    $extension = '';
                }

                $image_url = "event-" . time() . $extension;
                $path = storage_path('/app/public/images/events/' . $image_url);
                $image = $image->save($path);
            } else {
                $imageSame = true;
            }
        }
        $event->privacy()->delete();

        $privacyValue = PrivacyValue::where('key', $privacy)->first();

        $privacy = $event->privacy()->create([
            'name' => '',
            'key' => $privacyValue->key,
        ]);

        $privacy->privacyValue()->associate($privacyValue);
        $privacy->save();

        $startDate = Carbon::parse($request->start_date);
        if ($request->event_type === "endless") {
            $endDate = null;
            $endsAt = null;
        } else {
            $endDate = Carbon::parse($request->end_date);
            $endsAt = Carbon::parse($request->ends_at);
        }


        $startsAt = Carbon::parse($request->starts_at);

        if (!$imageSame) {
            $event->fill([
                'title' => $request->all()['title'],
                'event_privacy' => $request->all()['event_privacy'],
                'event_type' => $request->all()['event_type'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'body' => $request->all()['body'],
                'image_path' => ($request->all()['image']) ? "events/" . $image->filename . $extension : 'events/default.png',
            ]);
        } else {
            $event->fill([
                'title' => $request->all()['title'],
                'event_privacy' => $request->all()['event_privacy'],
                'event_type' => $request->all()['event_type'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'body' => $request->all()['body'],
            ]);
        }

        $timezoneValue = TimezoneValue::where('key', $request->timezone)->first();
        $event->timezone()->delete();

        $timezone = $event->timezone()->create([
            'key' => $timezoneValue->key,
            'name' => $timezoneValue->key,
        ]);

        $timezone->timezoneValue()->associate($timezoneValue);
        $timezone->save();

        $event->save();

        $admin = $event->participants()->where([
            ['profile_id', '=', $profile->id],
            ['admin', '=', 1],
        ])->exists();

        $friends = [];
        foreach ($event->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->load('location.country');

            if ($participant->id === $profile->id) {
                $event->member = true;
            }

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            }
        }

        $event->friends = $friends;

        if ($admin) {
            $event->admin = true;
        }


        $timezoneKey = ($profile->timezone) ? $profile->timezone->timezoneValue->key : 'UTC';
        $now = Carbon::now()->tz($timezoneKey);


        $event->begun = false;
        $event->ended = false;

        if ($event->event_type === 'endless') {
            if ($now->gte($event->starts_at->shiftTimezone($timezoneKey))) {
                $event->begun = true;
            }
        } else {
            if ($now->gte($event->starts_at) && $now->lte($event->end_date)) {
                $event->begun = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event updated.',
            'event' => $event->load('privacy.privacyValue')->load('timezone.timezoneValue'),
        ], 200);
    }

    public function getEvent(string $slug)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $slug)->with('participants')->with('privacy.privacyValue')->with('posts')->with('timezone.timezoneValue')->first();
        $posts = $event->posts->load('profile.location.country');

        $invited = $event->invited()->where([
            ['profile_id', '=', $profile->id],
            ['invitations_id', '=', $event->id],
        ])->exists();

        $member = $event->participants()->where([
            ['profile_id', '=', $profile->id],
            ['event_id', '=', $event->id],
        ])->exists();

        if ($event->privacy->privacyValue->key === 'secret' && !$invited && !$member) {
            return response()->json([
                'success' => false,
                'message' => 'error.',
            ], 400);
        }

        $timezoneKey = ($profile->timezone) ? $profile->timezone->timezoneValue->key : 'UTC';
        $now = Carbon::now()->tz($timezoneKey);

        $event->begun = false;
        $event->ended = false;
        $diff = $now->diffInHours($event->starts_at->shiftTimezone($timezoneKey));

        if ($event->event_type === 'endless') {
            if ($now->gte($event->starts_at->shiftTimezone($timezoneKey))) {
                $event->begun = true;
            }
        } else {
            if ($now->gte($event->starts_at) && $now->lte($event->end_date)) {
                $event->begun = true;
            }
        }

        $admin = $event->participants()->where([
            ['profile_id', '=', $profile->id],
            ['admin', '=', 1],
        ])->exists();

        $friends = [];
        foreach ($event->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->load('location.country');

            if ($participant->id === $profile->id) {
                $event->member = true;
            }

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            }
        }

        $event->friends = $friends;

        if ($admin) {
            $event->admin = true;
        }

        $eventId = $event->id;

        $profilesThatCanBeInvited = Profile::whereDoesntHave('events', function ($participants) use ($eventId) {
            return $participants->where('event_id', '=', $eventId);
        })->paginate(15);

        foreach ($profilesThatCanBeInvited as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->is_friend = $profile->isFriendWith($participant);
        }

        $request = RequestResource::where('sender_id', $profile->id)
            ->where('requests_id', $event->id)
            ->where('requests_type', 'App\Event')->first();

        $invitation = Invitation::where('profile_id', $profile->id)
            ->where('invitations_id', $event->id)
            ->where('invitations_type', 'App\Event')->first();


        if ($request) {
            $event->membership_requested = $request;
        }

        if ($invitation) {
            $event->invitation_sent = $invitation;
        }

        $conferenceMembers = Conference::where('event_id', $event->id)->get();

        foreach ($conferenceMembers as $conference) {
            $member = Profile::where('id', $conference->profile_id)->first();
            $conference->member = $member;
        }

        return response()->json([
            'success' => true,
            'message' => 'event fetched.',
            'event' => $event->load('profile'),
            'profiles_that_can_be_invited' => $profilesThatCanBeInvited,
            'members_in_hall' => $conferenceMembers,
            'posts' => $posts
        ], 200);
    }

    public function storePostMessage(Request $request, $slug)
    {
        $profile = auth()->user()->profile()->first();
        $event = Event::where('slug', $slug)->first();

        $body = $request->all()['message'];

        if (isset($request->file) && !empty($request->file)) {
            $file = $request->file;

            if ($request->type === "audio") {
                $filename = time();
                $extension = '.mp3';

                $file = file_get_contents($file);
                file_put_contents(storage_path('app/public/audio/messages/' . $filename . $extension), $file);

                if ($file) {
                    if (isset($file->filename)) {
                        $filename = null;
                        $filename = $file->filename;
                    } else {
                    }
                }

                $post = $profile->posts()->create([
                    'type' => 'audio',
                    'body' => $body,
                    'content_path' => ($file) ? "messages/" . $filename . $extension : null,
                ]);
            } else {
                $file = Image::make(file_get_contents($file));

                $mime = $file->mime();

                if ($mime == 'image/jpeg') {
                    $extension = '.jpg';
                } else if ($mime == 'image/png') {
                    $extension = '.png';
                } else if ($mime == 'image/gif') {
                    $extension = '.gif';
                } else {
                    $extension = '';
                }

                $file_url = "message-" . time() . $extension;
                $path = storage_path('/app/public/images/messages/' . $file_url);
                $file = $file->save($path);

                $filename = null;

                if ($file) {
                    if (isset($file->filename)) {
                        $filename = $file->filename;
                    } else {
                        $filename = $file;
                    }
                }

                $post = $profile->posts()->create([
                    'type' => 'image',
                    'body' => $body,
                    'content_path' => ($file) ? "messages/" . $filename . $extension : null,
                ]);
            }
        } else {
            $post = $profile->posts()->create([
                'body' => $body,
            ]);
        }

        $event->posts()->attach($post);

        return response()->json([
            'success' => true,
            'message' => 'Post created.',
            'post' => $post->load('profile.location.country'),
        ], 200);
    }


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
