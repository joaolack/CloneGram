<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->when(
                $request->user()?->is($this->resource),
                $this->email
            ),
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_path
                ?  Storage::disk('public')->url($this->avatar_path)
                : null,
            'created_at' => $this->created_at,
        ];
    }
}
