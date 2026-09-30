<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Message\StoreMessageRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\MessageResource;
use App\Models\Attachment;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use AuthorizesRequests;
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
            ->oldest()
            ->paginate(25);

        return MessageResource::collection($messages);
    }

    /**
     * Post a new root message or a thread reply
     */

   public function store(StoreMessageRequest $request, Channel $channel)
    {
        $this->authorize('postMessage', $channel);

        // Explicitly get input from request or validated data
        $parentId = $request->input('parent_id');
        $body = $request->input('body', '');
        $attachmentIds = $request->input('attachment_ids', []);

        $message = $channel->messages()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId ?: null, // Force null if empty string or falsy
            'body' => $body,
        ]);

        // Fix for HasMany: Associate attachments using foreign key update
        if (!empty($attachmentIds) && is_array($attachmentIds)) {
            Attachment::whereIn('id', $attachmentIds)
                ->whereNull('message_id') // Safety: only claim unattached files
                ->update(['message_id' => $message->id]);
        }

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
