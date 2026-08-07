<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostService 
{

    public function paginate(): LengthAwarePaginator {
        return Post::query()
            ->with('user')
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

    public function get(Post $post): Post {
        return $post->load('user');
    }

    public function delete(Post $post): void {
        Storage::disk('public')->delete(
            $post->media_path
        );

        $post->delete();
    }

}