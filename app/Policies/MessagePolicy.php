<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{

    /**
     * Determine whether the user can update the message.
     */
    public function update(User $user, Message $message): bool
    {
        //Users can only edit their own messages
        return $user->id === $message->user_id;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, Message $message): bool
    {
        // Authors can delete their own messages
        if ($user->id === $message->user_id) {
            return true;
        }

        //Channel owners/admins can delete any message in their channel

        return $message->channel->users()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Message $message): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Message $message): bool
    {
        return false;
    }
}
