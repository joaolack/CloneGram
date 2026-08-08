<?php

namespace App\Services;

use App\Models\User;

class HomeService
{
    public function __construct(
        private PostService $postService
    ) {}

    public function getHome(User $user): array {
        $posts = $this->postService->paginate($user);

        $followedUserIds = $user
            ->following()
            ->pluck('users.id');

        $suggestions = User::query()
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $followedUserIds)
            ->inRandomOrder()
            ->limit(5)
            ->get();      
            
        return [
            'posts' => $posts,
            'suggestions' => $suggestions,
        ];
    }
}