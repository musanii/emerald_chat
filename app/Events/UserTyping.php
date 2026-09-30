<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Use ShouldBroadcastNow so typing indicators don't get stuck in queue workers
class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $channelId;
    public int $userId;
    public string $userName;

    public function __construct(int $channelId, User $user)
    {
        $this->channelId = $channelId;
        $this->userId = $user->id;
        $this->userName = $user->name;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('channel.' . $this->channelId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}