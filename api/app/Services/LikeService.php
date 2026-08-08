<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LikeService
{
    public function like(User $user, Post $post): void {
        $alreadyLiked = $user
            ->likedPosts()
            ->whereKey($post->id)
            ->exists();

        if ($alreadyLiked) {
            throw ValidationException::withMessages([
                'post' => ['Você já curtiu esse post']
            ]);
        }

        $user->likedPosts()->attach($post->id);
    }

    public function unlike(User $user, Post $post): void {
        $user->likedPosts()->detach($post->id);
    }
}