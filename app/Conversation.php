<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MessageNotification;

class Conversation extends Model
{
    protected $fillable = ['data', 'private'];

    public function event()
    {
        return $this->belongsTo('App\Event')->withTimestamps();
    }

    public function members()
    {
        return $this->belongsToMany('App\Profile', 'conversation_members', 'conversation_id', 'profile_id')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany('App\Message', 'conversation_id')->with('sender');
    }

    /**
     * Get messages for a conversation.
     *
     * @param Profile  $profile
     * @param array $paginationParams
     * @param bool  $deleted
     *
     * @return Message
     */
    public function getMessages($profile, $paginationParams = null, $deleted = false)
    {
        return $this->getConversationMessages($profile, $paginationParams, $deleted);
    }

    private function getConversationMessages($profile, $paginationParams, $deleted)
    {
        $messages = $this->messages()->get();

        return $messages;
    }

    public function start($participants, $data = [])
    {
        $conversation = $this->create();

        if ($participants) {
            $conversation->addMembers($participants);
        }
        return $conversation;
    }

    /**
     * Add profile to conversation.
     *
     * @param int $profileId
     *
     * @return void
     */
    public function addMembers($profileIds = null, $single = null)
    {

        if ($profileIds) {
            foreach ($profileIds as $profile) {
                $this->members()->attach($profile);
            }
        } else if ($single) {
            return $this->members()->attach($single);
        }

    }
    /**
     * Gets conversations for a specific profile.
     *
     * @param Profile | int $profile
     *
     * @return array
     */
    public function profileConversations($profile)
    {
        $profileId = is_object($profile) ? $profile->getKey() : $profile;

        return $this->join('conversation_members', 'conversation_members.conversation_id', '=', 'conversations.id')
            ->where('conversation_members.profile_id', $profileId)
            ->where('conversations.event_id', '=', null)
            ->get()->load('members');
        // ->get('conversations.updated_at');
        // ->pluck('conversations.id');
    }

    /**
     * Get Private Conversation between two users.
     *
     * @param int | User $userOne
     * @param int | User $userTwo
     *
     * @return Conversation
     */
    public function between($userOne, $userTwo)
    {
        $conversation1 = $this->profileConversations($userOne);
        $conversation2 = $this->profileConversations($userTwo);

        $common_conversations = $this->getConversationsInCommon($conversation1, $conversation2);
        if (sizeof($common_conversations) === 0) {
            return null;
        } else {
            return $common_conversations;
        }

        // return $this->conversation->findOrFail($common_conversations);
    }

    /**
     * Gets the conversations in common.
     *
     * @param array $conversation1 The conversations for user one
     * @param array $conversation2 The conversations for user two
     *
     * @return Conversation The conversations in common.
     */
    private function getConversationsInCommon($conversation1, $conversation2)
    {
        return $conversation1->intersect($conversation2);
    }

    private function notifications($profile, $readAll)
    {
        $notifications = MessageNotification::where('profile_id', $profile->getKey())
            ->where('conversation_id', $this->id);

        if ($readAll) {
            return $notifications->update(['is_seen' => 1]);
        }

        return $notifications->get();
    }

    /**
     * Gets the notifications.
     *
     * @param Profile $profile The profile
     *
     * @return Notifications The notifications.
     */
    public function getNotifications($profile, $readAll = false)
    {
        return $this->notifications($profile, $readAll);
    }

    /**
     * Marks all the messages in a conversation as read.
     *
     * @param $profile
     */
    public function readAll($profile)
    {
        return $this->getNotifications($profile, true);
    }

    /**
     * Get unread notifications.
     *
     * @param Profile $profile
     *
     * @return void
     */
    public function unReadNotifications($profile)
    {
        $notifications = 'App\MessageNotification'::where([['profile_id', '=', $profile->getKey()], ['conversation_id', '=', $this->id], ['is_seen', '=', 0]])->get();

        return $notifications;
    }
}
