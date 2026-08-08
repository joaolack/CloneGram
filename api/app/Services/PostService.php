<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostService 
{

    public function paginate(User $user): LengthAwarePaginator {
        return Post::query()
            ->with('user')
            ->withCount(['likedBy', 'comments',])
            ->withExists(['likedBy as liked_by_me' => fn ($query) => $query->whereKey($user->id),
            ])
            ->latest()
            ->paginate(12);
    }

    public function create(User $user, array $data): Post {
        $file = $data['media'];

        $mimeType = $file->getMimeType() ?? '';
        $mediaType = str_starts_with($mimeType, 'video/')
            ? 'video'
            : 'image';
        $mediaPath = $file->store('posts', 'public');

        $post = $user->posts()->create([
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'caption' => $data['caption'] ?? null
        ]);

        return $post->load('user');
    }

    public function get(Post $post, User $user): Post {
        $post->load(['user', 'comments.user']);

        $post->loadCount(['likedBy', 'comments',]);

        $likedByMe = $post
            ->likedBy()
            ->whereKey($user->id)
            ->exists();

        $post->setAttribute('liked_by_me', $likedByMe);

        return $post;
    }

    public function delete(Post $post): void {
        Storage::disk('public')->delete(
            $post->media_path
        );

        $post->delete();
    }

}