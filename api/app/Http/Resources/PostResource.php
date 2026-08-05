<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'caption' => $this->caption,
            'media_url' => Storage::url($this->media_path),
            'media_type' => $this->media_type,
            'author' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->likedBy()->count(),
            'created_at' => $this->created_at,
        ];
    }
}
