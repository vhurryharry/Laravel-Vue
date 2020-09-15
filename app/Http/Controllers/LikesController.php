<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Model;
use App\Event;
use App\Community;

class LikesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }

    public function like(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $reacter = $profile->getLoveReacter();
        $reactionType = ReactionType::fromName('Like');

        if ($request->type === 'event') {
            $resource = Event::where('slug', $request->slug)->first();
        } else if ($request->type === 'community') {
            $resource = Community::where('slug', $request->slug)
                ->joinReactionCounterOfType($reactionType)
                ->first();
        }

        $reactant = $resource->getLoveReactant();

        $isReacted = $profile
            ->getLoveReacter()
            ->isReactedToWithType($reactant, $reactionType);

        $friends = [];

        foreach ($resource->participants as $participant) {
            $participant->has_friend_request_from = $profile->hasFriendRequestFrom($participant);
            $participant->friend_request_sent = $profile->hasSentFriendRequestTo($participant);

            if ($profile->isFriendWith($participant)) {
                $participant->is_friend = true;
                $friends[$participant->id] = $participant;
            }
        }

        $resource->mutuals = $friends;

        if (!$isReacted) {
            $reacter->reactTo($reactant, $reactionType);

            return response()->json([
                'success' => true,
                'message' => $resource->name . ' liked.',
                'resource' => $resource->load('privacy')->load('participants')->load('loveReactant.reactions.reactant')
            ], 200);
        } else {
            $reacter->unreactTo($reactant, $reactionType);

            return response()->json([
                'success' => true,
                'message' => $resource->name . ' unliked.',
                'resource' => $resource->load('privacy')->load('participants')->load('loveReactant.reactions.reactant')
            ], 200);
        }
    }

    public function removeLike(Request $request)
    {
    }
}
