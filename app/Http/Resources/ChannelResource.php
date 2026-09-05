<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'members_count' => $this->whenCounted('users'),
            'role' => $this->whenPivotLoaded('channel_user', function () {
                return $this->pivot->role;
            }),
            'last_read_at' => $this->whenPivotLoaded('channel_user', function () {
                return $this->pivot->last_read_at;
            }),
            'created_at' => $this->created_at->toIso8601String(),

        ];
    }
}
