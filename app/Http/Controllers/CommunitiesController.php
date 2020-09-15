<?php

namespace App\Http\Controllers;

use App\Community;
use App\Events\CommunityCreated;
use App\Events\ProfileInvited;
use App\Http\Requests\CreateCommunityRequest;
use App\Interest;
use App\Privacy;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use App\Invitation;
use App\Request as RequestResource;
use App\PrivacyValue;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class CommunitiesController extends Controller
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
        $interests = Interest::all();

        return view('pages.communities', compact('interests'));
    }

    public function joinCommunity(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $community = Community::where('slug', $request->slug)->with('participants.location.country')->with('privacy.privacyValue')->with('posts')->first();

        if ($community->privacy->privacyValue->key === 'public') {
            $community->participants()->attach($profile);
            $community->member = true;

            $invitation = Invitation::where('invitations_id', $community->id);

            if ($invitation) {
                $community->invited()->detach($profile);
                // $community->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Community joined.',
                'community' => $community->load('participants.location.country'),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'error',
            ], 400);
        }
    }

    public function leaveCommunity(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $community = Community::where('slug', $request->slug)->with('participants.location.country')->with('privacy.privacyValue')->with('posts')->first();

        $community->participants()->detach($profile);
        $community->member = false;

        return response()->json([
            'success' => true,
            'message' => 'Community left.',
            'community' => $community,
        ], 200);
    }

    public function getPopularCommunities()
    {

        $reactionType = ReactionType::where('name', 'Like')->first();

        $communities = Community::with('privacy.privacyValue')->with('participants')
            ->with('loveReactant.reactions.reactant')
            ->joinReactionCounterOfType($reactionType)
            ->orderBy('count', 'desc')
            ->where('count', '!=', 0)
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Popular communities fetched.',
            'communities' => $communities,
        ], 200);
    }

    public function all()
    {
        $profile = auth()->user()->profile()->first();
        $reactionType = ReactionType::where('name', 'Like')->first();

        $communities = Community::where('profile_id', '!=', $profile->id)->with('privacy.privacyValue')->with('participants')->with('loveReactant.reactions.reactant')
            ->joinReactionCounterOfType($reactionType)
            ->take(15)
            ->get();

        foreach ($communities as $key => $community) {

            if ($community->privacy && $community->privacy->privacyValue->key === 'secret') {
                $communities->forget($key);
            } else {
                foreach ($community->participants as $participant) {
                    $member = $community->participants()->where([
                        ['profile_id', '=', $profile->id],
                        ['community_id', '=', $community->id],
                    ])->exists();

                    if ($member) {
                        $communities->forget($key);
                    } else {
                        $privacyValue = $participant->load('myFriendsPrivacy')->myFriendsPrivacy['key'];

                        $isFriend = $profile->isFriendWith($participant);
                        if ($privacyValue === 'visible_to_all') {
                        } else if ($privacyValue === 'visible_only_to_friends') {
                            if (!$isFriend) {
                                $communities->forget($key);
                            }
                        } else if ($privacyValue === 'visible_to_extended_friends') {
                            if (!$isFriend && $authenticatedProfile->getMutualFriendsCount($profile) === 0) {
                                $communities->forget($key);
                            }
                        } else if ($privacyValue === 'hidden') {
                            $communities->forget($key);
                        } else {
                            $communities->forget($key);
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Communities fetched.',
            'communities' => $communities,
        ], 200);
    }


    public function myCommunities()
    {
        $profile = auth()->user()->profile()->first();
        $reactionType = ReactionType::where('name', 'Like')->first();

        $myCommunities = $profile->communities()->with('privacy.privacyValue')->with('participants.location.country')
            ->with('loveReactant.reactions.reactant')
            ->joinReactionCounterOfType($reactionType)
            ->take(30)
            ->get();

        foreach ($myCommunities as $community) {
            $friends = [];

            foreach ($community->participants as $key => $participant) {
                $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
                $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
                $participant->friend_count = $participant->getFriendsCount();

                if ($profile->isFriendWith($participant)) {
                    $participant->is_friend = true;
                    $friends[$participant->id] = $participant;
                }
                // }
            }
            $community->mutuals = $friends;
        }

        return response()->json([
            'success' => true,
            'message' => 'My communities fetched.',
            'communities' => $myCommunities,
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

        $communitySlug = $request->all()['community_slug'];
        $community = Community::where('slug', '=', $communitySlug)->first();

        $this->authorize('update', $community);

        foreach ($request->all()['guest_list'] as $guestProfile) {
            $guestProfile = Profile::where('id', $guestProfile['id'])->first();

            $isBlocked = $profile->hasBlocked($guestProfile);

            if (!$isBlocked) {

                $invitation = Invitation::where('profile_id', $guestProfile['id'])
                    ->where('invitations_id', $community->id)
                    ->where('invitations_type', 'App\Community')->first();

                if (!$invitation) {
                    $community->invite($guestProfile['id'], $community->name);
                }

                $content = [
                    'message' => __('components/profiles.received_an_invitation') . $community->name . '!',
                    'from' => $guestProfile['name'],
                    'to' => $guestProfile['id'],
                ];

                event(new ProfileInvited($content));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invitations sent.',
            'community' => $community->load('participants')->load('privacy')->load('loveReactant.reactions.reactant')
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCommunityRequest $request)
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

            $image_url = "community-" . time() . $extension;
            $path = storage_path('/app/public/images/communities/' . $image_url);
            $image = $image->save($path);
        }

        $community = $user->communityOwner()->create([
            'name' => $request->name,
            'body' => $request->body,
            'community_privacy' => $request->community_privacy,
            'image_path' => 'communities/default.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'image_path' => ($request->all()['image']) ? "communities/" . $image->filename . $extension : 'communities/default.png',
        ]);

        $user->communities()->attach($community);
        $community->makeAdmin($user);

        foreach ($request->interests as $interest) {
            $interest = Interest::find($interest['id']);
            $community->interests()->save($interest);
        }

        event(new CommunityCreated([
            'from' => $user->id,
            'community' => $community->load('privacy')->load('participants'),
        ]));

        $privacyValue = PrivacyValue::where('key', $request->community_privacy)->first();


        $privacy = $community->privacy()->create([
            'name' => '',
            'key' => $privacyValue->key,
        ]);

        $privacy->privacyValue()->associate($privacyValue);
        $privacy->profile()->associate($user);
        $privacy->save();

        $community->save();

        return response()->json([
            'success' => true,
            'message' => 'community created successfully.',
            'community' => $community->slug,
            'community_object' => $community->load('profile'),
        ], 200);
    }

    public function makeAdmin(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $adminProfile = Profile::find($request->all()['profile_id']);
        $community = Community::where('id', $request->all()['community_id'])->with('interests')->with('participants')->with('privacy.privacyValue')->with('posts')->first();

        $owner = $profile->communityOwner()->where([
            ['slug', '=', $community->slug],
        ])->exists();

        if ($owner) {
            $community->participants()->updateExistingPivot($adminProfile->id, ['admin' => '1']);
            $community->owner = $owner;
            $community->admin = $owner;

            foreach ($community->participants as $participant) {
                $admin = $community->participants()->where([
                    ['profile_id', '=', $participant->id],
                    ['admin', '=', 1],
                ])->exists();

                if ($admin) {
                    $participant->admin = true;
                } else {
                    $participant->admin = false;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile made admin.',
                'community' => $community->load('profile'),
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Error.',
            ], 403);
        }
    }

    public function removeAdmin(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $adminProfile = Profile::find($request->all()['profile_id']);
        $community = Community::where('id', $request->all()['community_id'])->with('interests')->with('participants')->with('privacy.privacyValue')->with('posts')->first();

        $owner = $profile->communityOwner()->where([
            ['slug', '=', $community->slug],
        ])->exists();


        if ($owner) {

            $community->participants()->updateExistingPivot($adminProfile->id, ['admin' => '0']);
            $community->owner = $owner;

            foreach ($community->participants as $participant) {
                $admin = $community->participants()->where([
                    ['profile_id', '=', $participant->id],
                    ['admin', '=', 1],
                ])->exists();

                if ($admin) {
                    $participant->admin = true;
                } else {
                    $participant->admin = false;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile removed as administrator.',
                'community' => $community->load('profile'),
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Error.',
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string $communityName
     * @return \Illuminate\Http\Response
     */
    public function show(string $slug)
    {
        $profile = auth()->user()->profile()->first();
        $community = Community::where('slug', $slug)->first();

        $invited = $community->invited()->where([
            ['profile_id', '=', $profile->id],
            ['invitations_id', '=', $community->id],
        ])->exists();

        $member = $community->participants()->where([
            ['profile_id', '=', $profile->id],
            ['community_id', '=', $community->id],
        ])->exists();


        if ($community->privacy->privacyValue->key === 'secret' && !$invited && !$member) {
            $interests = Interest::all();

            return view('pages.communities', compact('interests'));
        }

        return view('pages.community', compact('slug'));
    }

    public function updateCommunity(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $slug = $request->all()['slug'];
        $name = $request->all()['name'];
        $privacy = $request->all()['community_privacy'];

        $body = $request->all()['body'];
        $interests = $request->all()['interests'];
        $imageSame = false;

        $community = Community::where('slug', $slug)->first();

        $community->interests()->detach();

        if ($request->all()['image']) {
            $file = $request->all()['image'];

            if ($file !== $community->image_path) {
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

                $image_url = "community-" . time() . $extension;
                $path = storage_path('/app/public/images/communities/' . $image_url);
                $image = $image->save($path);
            } else {
                $imageSame = true;
            }
        }

        foreach ($interests as $interest) {
            $interest = Interest::find($interest['id']);
            $community->interests()->save($interest);
        }

        $community->privacy()->delete();

        $privacyValue = PrivacyValue::where('key', $privacy)->first();

        $privacy = $community->privacy()->create([
            'name' => '',
            'key' => $privacyValue->key,
        ]);

        $privacy->privacyValue()->associate($privacyValue);
        $privacy->save();

        if (!$imageSame) {
            $community->fill([
                'name' => $name,
                'community_privacy' => $privacyValue->key,
                'body' => $body,
                'image_path' => ($request->all()['image']) ? "communities/" . $image->filename . $extension : 'communities/default.png',
            ]);
        } else {
            $community->fill([
                'name' => $name,
                'community_privacy' => $privacyValue->key,
                'body' => $body,
            ]);
        }

        $community->save();

        $admin = $community->participants()->where([
            ['profile_id', '=', $profile->id],
            ['admin', '=', 1],
        ])->exists();

        $friends = [];

        foreach ($community->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->load('location.country');

            if ($participant->id === $profile->id) {
                $community->member = true;
            }

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            }
        }

        $community->friends = $friends;

        $community->admin = $admin;
        // }

        return response()->json([
            'success' => true,
            'message' => 'Community updated.',
            'community' => $community->load('privacy.privacyValue')->load('interests'),
        ], 200);
    }

    public function getCommunity(string $slug)
    {
        $profile = auth()->user()->profile()->first();
        $community = Community::where('slug', $slug)->with('interests')->with('participants')->with('privacy.privacyValue')->with('posts')->first();

        $posts = $community->posts->load('profile.location.country');

        $owner = $profile->communityOwner()->where([
            ['slug', '=', $community->slug],
        ])->exists();

        $community->owner = $owner;

        $invited = $community->invited()->where([
            ['profile_id', '=', $profile->id],
            ['invitations_id', '=', $community->id],
        ])->exists();

        $member = $community->participants()->where([
            ['profile_id', '=', $profile->id],
            ['community_id', '=', $community->id],
        ])->exists();

        if ($community->privacy->privacyValue->key === 'secret' && !$invited && !$member) {
            return response()->json([
                'success' => false,
                'message' => 'error.',
            ], 400);
        }

        $admin = $community->participants()->where([
            ['profile_id', '=', $profile->id],
            ['admin', '=', 1],
        ])->exists();

        $community->admin = $admin;

        $friends = [];

        foreach ($community->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->load('location.country');

            if ($participant->id === $profile->id) {
                $community->member = true;
            }

            $admin = $community->participants()->where([
                ['profile_id', '=', $participant->id],
                ['admin', '=', 1],
            ])->exists();

            if ($admin) {
                $participant->admin = true;
            }

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            }
        }

        $community->friends = $friends;

        $communityId = $community->id;

        $profilesThatCanBeInvited = Profile::whereDoesntHave('communities', function ($participants) use ($communityId) {
            return $participants->where('community_id', '=', $communityId);
        })->paginate(15);

        foreach ($profilesThatCanBeInvited as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);
            $participant->is_friend = $profile->isFriendWith($participant);
            $participant->load('location.country');

            $admin = $community->participants()->where([
                ['profile_id', '=', $participant->id],
                ['admin', '=', 1],
            ])->exists();

            if ($admin) {
                $participant->admin = true;
            }
        }

        $request = RequestResource::where('sender_id', $profile->id)
            ->where('requests_id', $community->id)
            ->where('requests_type', 'App\Community')->first();

        $invitation = Invitation::where('profile_id', $profile->id)
            ->where('invitations_id', $community->id)
            ->where('invitations_type', 'App\Community')->first();


        if ($request) {
            $community->membership_requested = $request;
        }

        if ($invitation) {
            $community->invitation_sent = $invitation;
        }

        return response()->json([
            'success' => true,
            'message' => 'Community fetched.',
            'community' => $community->load('profile'),
            'profiles_that_can_be_invited' => $profilesThatCanBeInvited,
            'posts' => $posts
        ], 200);
    }

    public function getProfilesNotInCommunity()
    {
    }

    public function storePostMessage(Request $request, $slug)
    {
        $profile = auth()->user()->profile()->first();
        $community = Community::where('slug', $slug)->first();

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
                        // $filename = $file;
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

                // $message = $message->send($conversation, $request->message['uid'], $request->message['body'], $file, $extension, $profile->id);
            }
        } else {
            $post = $profile->posts()->create([
                'body' => $body,
            ]);
        }

        $community->posts()->attach($post);

        return response()->json([
            'success' => true,
            'message' => 'Post created.',
            // 'community' => $community->load('posts'),
            'post' => $post->load('profile.location.country'),
        ], 200);
    }


    public function upcomingEvents(String $slug)
    {

        $profile = auth()->user()->profile()->first();

        $community = Community::where('slug', $slug)->with('interests')->with('participants')->with('privacy.privacyValue')->with('posts')->first();

        $events = $community->events()->where('start_date',  '>=', Carbon::now())->with('profile')->with('participants')->with('privacy.privacyValue')->with('loveReactant.reactions.reactant')->get();

        foreach ($events as $key => $event) {
            $member = $event->participants()->where([
                ['profile_id', '=', $profile->id],
                ['event_id', '=', $event->id],
            ])->exists();

            if (!$member && $event->privacy && $event->privacy->privacyValue->key === 'secret') {
                $events->forget($key);
            }
        }
        // $events = $community->events->where('start_date',  '>=', Carbon::now())->get();
        // App\Request::where('id',4)->with(['quotes'=>function($query){ $query->where('status','=','3');}]) ->with('sourceStable','destinationStable') ->get();
        return response()->json([
            'success' => true,
            'message' => 'Upcoming events fetched.',
            'events' => $events,
        ], 200);
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
