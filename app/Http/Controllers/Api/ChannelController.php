<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Channel\StoreChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    /**
     * Get channels the user has joined or public channel's in user's department
     */
    public function index(Request $request)
    {
        $channels = Channel::where('department_id', $request->user()->department_id)
            ->where(function ($query) use ($request) {
                $query->where('type', 'public')
                    ->orWhereHas('users', fn($q) => $q->where('user_id', $request->user()->id));
            })
            ->withCount('users')
            ->get();

        return ChannelResource::collection($channels);
    }


    /**
     * Create a new channel and attach creator as channel owner.
     */

    public function store(StoreChannelRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $channel = Channel::create($data);

        //attach creator to pivot table as owner
        $channel->users()->attach($request->user()->id, ['role' => 'owner']);

        return (new ChannelResource($channel->loadCount('users')))
            ->response()
            ->setStatusCode(201);
    }


    /**
     * Join a channel
     */

    public function join(Request $request, Channel $channel)
    {
        if ($channel->users()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Already a member of this channel.'], 400);
        }
        $channel->users()->attach($request->user()->id,  ['role' => 'member']);
        return response()->json(['message' => 'Successfully joined the channel.']);
    }
}
