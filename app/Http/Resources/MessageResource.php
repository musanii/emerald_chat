<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'channel_id' => $this->channel_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'replies_count' => $this->whenCounted('replies'),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(fn($file) => [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'file_size' => $file->file_size,
                    'mime_tye' => $file->mime_type,
                    'url' => asset('storage/' . $file->file_path),
                ]);
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
