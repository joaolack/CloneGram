<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'media_url' => Storage::disk('public')->url($this->media_path),
            'media_type' => $this->media_type,
            'author' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->whenCounted('likedBy'),
            'comments_count' => $this->whenCounted('comments'),
            'liked_by_me' => $this->when(
                $this->resource->getAttribute('liked_by_me') !== null,
                (bool) $this->resource->getAttribute('liked_by_me')
            ),
            'comments' => CommentResource::collection(
                $this->whenLoaded('comments')
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
