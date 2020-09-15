<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Conversation;
use App\Message;

class MessageNotification extends Model
{
    use SoftDeletes;

    protected $fillable = ['profile_id', 'message_id', 'conversation_id'];
    protected $table = 'message_notification';
    protected $dates = ['deleted_at'];

    /**
     * Creates a new notification.
     *
     * @param Message      $message
     * @param Conversation $conversation
     */
    public static function make(Message $message, Conversation $conversation)
    {
        self::createCustomNotifications($message, $conversation);
    }

    public function unReadNotifications($profile)
    {
        return MessageNotification::where([
            ['profile_id', '=', $profile->getKey()],
            ['is_seen', '=', 0],
        ])->get();
    }

    public static function createCustomNotifications($message, $conversation)
    {
        $notification = [];

        foreach ($conversation->members as $profile) {
            $is_sender = ($message->profile_id == $profile->getKey()) ? 1 : 0;

            $notification[] = [
                'profile_id' => $profile->getKey(),
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'is_seen' => $is_sender,
                'is_sender' => $is_sender,
                'created_at' => $message->created_at,
            ];
        }

        self::insert($notification);
    }

    public function markAsRead()
    {
        $this->is_seen = 1;
        $this->update(['is_seen' => 1]);
        $this->save();
    }
}
