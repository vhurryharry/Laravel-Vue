<?php

use App\Profile;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
 */

// Broadcast::channel('App.User.*', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('conferences.{conferenceId}', function ($user, $conversationId) {
    return [
        'name' => $user->name,
        'username' => $user->username
    ];
    // return true;
    // return $user->id === Profile::find($conversationId)->id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return true;
    // return $user->id === Profile::find($conversationId)->id;
});

Broadcast::channel('conversation.{profileId}', function ($user, $conversationId) {
    return true;
    // return $user->id === Profile::find($conversationId)->id;
});

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    return true;
    // return $user->id === Profile::find($conversationId)->id;
});

Broadcast::channel('invitation.{fromId}', function ($user, $toId) {
    return true;

    // return $user->id === Profile::find($toId)->id;
});

// Broadcast::channel('events.{fromId}', function ($user, $fromId) {
//     return $user->id === Profile::find($fromId)->id;
// });

// Broadcast::channel('communities.{fromId}', function ($user, $fromId) {
//     return $user->id === Profile::find($fromId)->id;
// });

Broadcast::channel('request.{fromId}', function ($user, $fromId) {
    return true;
    // return $user->id === Profile::find($fromId)->id;
});

Broadcast::channel('requests.{fromId}', function ($user, $fromId) {
    return true;
    // return $user->id === Profile::find($fromId)->id;
});
