<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use App\Profile;
use App\Chat;
use App\Message;
use App\Events\MessageSent;
use App\Events\ConversationCreated;
use App\MessageNotification;
use App\Events\ConversationUpdated;
use Camroncade\Timezone\Facades\Timezone;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class ConversationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }

    public function getConversation(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $conversation = Conversation::where('event_id', $request->event_id)->first()->load('members');

        foreach ($conversation->members as $member) {
            if ($member->id !== $profile->id) {
                $member->friendship_record = $profile->getFriendship($member);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversation fetched.',
            'conversation' => $conversation,
        ], 200);
    }

    public function getConversations(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        if ($request->view === 'events') {
            $eventsConversations = [];
            $events = $profile->events->load('conversation');

            foreach ($events as $key => $value) {
                if ($value->conversation) {
                    $conversation = Conversation::find($value->conversation->id)->load('members');
                    $conversation->event = $value;
                    array_push($eventsConversations, $conversation);
                }
            }

            foreach ($eventsConversations as $key => $conversation) {
                $latest = $conversation->getMessages($profile)->sortByDesc('created_at')->first();
                $conversation->latest = $latest;
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversations fetched.',
                'conversations' => $eventsConversations,
            ], 200);
        } else {
            $conversation = new Conversation();
            $conversations = $conversation->profileConversations($profile);
            $privateConversations = [];

            foreach ($conversations as $key => $value) {
                $conversation = Conversation::find($value->conversation_id);

                $conversationMembers = $conversation->members;

                foreach ($conversationMembers as $member) {
                    if ($member->id !== $profile->id) {
                        $member->friendship_record = $profile->getFriendship($member);
                    }
                }

                if ($conversation->event_id === null) {
                    array_push($privateConversations, $conversation);
                }
            }

            foreach ($privateConversations as $key => $conversation) {
                $latest = $conversation->getMessages($profile)->first();
                $conversation->latest = $latest;
                $unread = $conversation->unReadNotifications($profile);

                $conversation->unread = $unread ?: null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversations fetched.',
                'conversations' => $privateConversations,
                'unread' => isset($unread) ? $unread : null
            ], 200);
        }
    }

    public function getMessages(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $conversation = Conversation::find($request->conversation['id']);
        $messages = $conversation->getMessages($profile)->sortByDesc('created_at')->take($request->count);

        return response()->json([
            'success' => true,
            'message' => 'Messages fetched.',
            'messages' => $messages,
        ], 200);
    }

    public function createConversation(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $recipient = Profile::where('id', $request->id)->first();

        $conversation = new Conversation();
        $conversation = $conversation->between($profile, $recipient);

        $friends = $profile->isFriendWith($recipient);

        if ($friends) {
            if (!$conversation[0]) {
                $conversation = new Conversation();

                $recipient->friendship_record =  $profile->getFriendship($recipient);
                $members = [$profile, $recipient];
                $conversation = $conversation->start($members, null);

                event(new ConversationCreated([
                    'from' => $profile->id,
                    'conversation' => $conversation->load('members'),
                ]));

                event(new ConversationCreated([
                    'from' => $recipient->id,
                    'conversation' => $conversation->load('members'),
                ]));

                return response()->json([
                    'success' => true,
                    'message' => 'Conversation created.',
                    'conversation' => $conversation
                ], 200);
            } else if ($conversation[0]->event_id) {
                $conversation = new Conversation();
                $recipient->friendship_record =  $profile->getFriendship($recipient);

                $members = [$profile, $recipient];
                $conversation = $conversation->start($members, null);

                return response()->json([
                    'success' => true,
                    'message' => 'Conversation created.',
                    'conversation' => $conversation->load('members')
                ], 200);
            } else {
                $conversation = $conversation[0]->load('members');

                foreach ($conversation->members as $member) {
                    if ($member->id !== $profile->id) {
                        $member->friendship_record = $profile->getFriendship($member);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Conversation returned.',
                    'conversation' => $conversation
                ], 200);
            }
        } else {
            return response()->json([
                'success' => true,
                'message' => 'denied.',
            ], 400);
        }
    }

    public function sendEventMessage(Request $request)
    {
        $profile = auth()->user()->profile()->first();
        $conversation = Conversation::find($request->conversation['id']);


        $file = null;
        $extension = null;
        $message = new Message();

        if (isset($request->message['file']) && !empty($request->message['file'])) {
            $file = $request->message['file'];

            if ($request->type === "audio") {
                $filename = time();
                $extension = '.mp3';

                $file = file_get_contents($file);
                file_put_contents(storage_path('app/public/audio/messages/' . $filename . $extension), $file);
                $message = $message->send($conversation, $request->message['uid'], $request->message['body'], $filename, $extension, $profile->id, 'audio');
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
                $message = $message->send($conversation, $request->message['uid'], $request->message['body'], $file, $extension, $profile->id);
            }
        } else {
            $message = $message->send($conversation, $request->message['uid'], $request->message['body'], null, null, $profile->id);
        }

        event(new MessageSent([
            'conversationId' => $conversation->id,
            'message' => $message,
        ]));

        $latest = $conversation->getMessages($profile)->sortByDesc('created_at')->first();
        $conversation->latest = $latest;

        event(new ConversationUpdated([
            'conversationId' => $conversation->id,
            'conversation' => $conversation->load('members'),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Messages sent.',
            'message' => $message,
        ], 200);
    }

    public function sendMessage(Request $request)
    {

        $profile = auth()->user()->profile()->first();
        $recipient = Profile::find($request->recipient['id']);
        $conversation = Conversation::find($request->conversation['id']);

        $file = null;
        $extension = null;
        $message = new Message();

        if (isset($request->message['file']) && !empty($request->message['file'])) {
            $file = $request->message['file'];

            if ($request->type === "audio") {
                $filename = time();
                $extension = '.mp3';

                $file = file_get_contents($file);
                file_put_contents(storage_path('app/public/audio/messages/' . $filename . $extension), $file);
                $message = $message->send($conversation, $request->message['uid'], $request->message['body'], $filename, $extension, $profile->id, 'audio');
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
                $message = $message->send($conversation, $request->message['uid'], $request->message['body'], $file, $extension, $profile->id);
            }
        } else {
            $message = $message->send($conversation, $request->message['uid'], $request->message['body'], null, null, $profile->id);
        }

        event(new MessageSent([
            'conversationId' => $conversation->id,
            'message' => $message,
        ]));

        $notification = new MessageNotification();

        $notification = $notification->make($message, $conversation);

        $unread = $conversation->unReadNotifications($recipient);

        $conversation->unread = $unread ?: null;

        $latest = $conversation->getMessages($profile)->sortByDesc('created_at')->first();
        $conversation->latest = $latest;

        event(new ConversationUpdated([
            'conversationId' => $recipient->id,
            'conversation' => $conversation->load('members'),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Messages sent.',
            'message' => $message,
        ], 200);
    }

    public function markConversationAsRead(Request $request)
    {
        $profile = auth()->user()->profile()->first();

        $conversation = Conversation::where('id', $request->conversation['id'])->first()->load('members');

        $conversation->readAll($profile);


        foreach ($conversation->members as $member) {

            if ($member->id !== $profile->id) {
                $member->friendship_record = $profile->getFriendship($member);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversation updated.',
            'conversation' => $conversation,
        ], 200);
    }
}
