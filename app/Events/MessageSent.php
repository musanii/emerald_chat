<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(Message $message)
    {
        $this->message = (new MessageResource($message->load([
            'user',
            'attachments'
        ])))->resolve();
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('channel.' . $this->message['channel_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}