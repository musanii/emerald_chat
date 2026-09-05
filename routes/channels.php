<?php

use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('channel.{channelId}', function (User $user, int $channelId) {
    $channel = Channel::find($channelId);

    if (!$channel) {
        return false;
    }

    //verify user can access channel via ChannelPolicy logic

    if ($user->can('view', $channel)) {
        return [

            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    return false;
});
