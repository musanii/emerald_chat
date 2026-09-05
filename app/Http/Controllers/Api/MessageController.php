<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Fetch root messages for a specific channel(paginated)
     */

    public function index(Channel $channel)
    {
        $this->authorize('view', $channel);

        $messages = $channel->messages()
            ->whereNull('parent_id')
            ->with(['user', 'attachments'])
            ->withCount('replies')
            ->latest()
            ->paginate(25);

        return MessageResource::collection($messages);
    }

    /**
     * Post a new root message or a thread reply
     */

    public function store(StoreMessageRequest $request, Channel $channel)
    {
        $this->authorize('postMessage', $channel);

        $message = $channel->messages()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ]);
        $message->load(['user', 'attachments']);
        broadcast(new MessageSent($message))->toOthers();

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Fetch thread replies for a parent message.
     */

    public function thread(Channel $channel, Message $message)
    {
        //Ensure the parent message belongs to the given channel
        abort_if($message->channel_id !== $channel->id, 404);

        $replies = $message->replies()
            ->with(['user', 'attachments'])
            ->oldest()
            ->get();

        return MessageResource::collection($replies);
    }
}
