<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    public function getByPost(Post $post): Collection {
        return $post->comments()->with('user')->oldest()->get();
    }

    public function create(User $user, Post $post, array $data): Comment {
        $comment = $post
            ->comments()
            ->create([
                'user_id' => $user->id,
                'content' => $data['content'],
            ]);

        return $comment->load('user');
    }
}