<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelPolicy
{


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Channel $channel): bool
    {

        //Public channels in the user's department are accessible to all department members
        if ($channel->type === 'public' && $channel->department_id === $user->department_id) {
            return true;
        }

        //private channels require explicit membership in the pivot table
        return $channel->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can post  messages to the channel.
     */
    public function postMessage(User $user, Channel $channel): bool
    {
        return $this->view($user, $channel);
    }

    /**
     * Determine whether the user can manage/delete the channel.
     */

    public function delete(User $user, Channel $channel): bool
    {
        return $channel->users()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }
}
