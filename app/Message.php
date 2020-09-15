<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Camroncade\Timezone\Facades\Timezone;

use Illuminate\Support\Carbon;

class Message extends Model
{

    protected $fillable = ['body', 'profile_id', 'type', 'uid', 'delivered', 'content_path', 'created_at', 'updated_at'];
    protected $touches = ['conversation'];

    public function sender()
    {
        return $this->belongsTo('App\Profile', 'profile_id');
    }

    public function conversation()
    {
        return $this->belongsTo('App\Conversation', 'conversation_id');
    }

    /**
     * Adds a message to a conversation.
     *
     * @param Conversation $conversation
     * @param string       $body
     * @param int          $profileId
     * @param string       $type
     *
     * @return Message
     */
    public function send(Conversation $conversation, $uid, $body, $file, $extension, $profileId, $type = 'text')
    {
        // $profile = auth()->user();
        $filename = null;
        if ($file) {
            if (isset($file->filename)) {
                $filename = $file->filename;
            } else {
                $filename = $file;
            }
        }

        $message = $conversation->messages()->create([
            'uid' => $uid,
            'body' => $body,
            'profile_id' => $profileId,
            'type' => $type,
            'delivered' => true,
            // 'content_path' => ($file) ? "messages/" . $file->filename . $extension : null,
            'content_path' => ($file) ? "messages/" . $filename . $extension : null,

            // 'created_at' => Timezone::convertFromUTC(Carbon::now(), 'UTC',  'Y-m-d H:i:s'),
            // 'updated_at' => Timezone::convertFromUTC(Carbon::now(), 'UTC',  'Y-m-d H:i:s')
        ]);

        // $messageWasSent = Chat::sentMessageEvent();
        $message->load('sender');
        // $this->raise(new $messageWasSent($message));

        return $message;
    }

    public function unreadCount($profile)
    {
        return MessageNotification::where('profile_id', $profile->getKey())
            ->where('is_seen', 0)
            ->count();
    }

    /**
     * Return user notification for specific message.
     *
     * @param $user
     *
     * @return Notification
     */
    public function getNotification($profile)
    {
        return MessageNotification::where('profile_id', $profile->getKey())
            ->where('message_id', $this->id)
            ->select(['message_notification.*', 'message_notification.updated_at as read_at'])
            ->first();
    }

    /**
     * Marks message as read.
     *
     * @param User $user
     *
     * @return void
     */
    public function markRead($profile)
    {
        $this->getNotification($profile)->markAsRead();
    }
}
