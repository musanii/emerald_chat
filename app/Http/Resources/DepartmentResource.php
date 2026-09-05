<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'channels_count' => $this->whenCounted('channels'),
            'channels' => ChannelResource::collection($this->whenLoaded('channels')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
