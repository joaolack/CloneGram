<?php

namespace App\Services;

use App\Models\User;
Use Illuminate\Validation\ValidationException;

class FollowService 
{
    public function follow(User $follower, User $followed): void {
        if($follower->is($followed)) {
            throw ValidationException::withMessages([
                'followed' => 'Você não pode seguir a si mesmo'
            ]);
        }

        $alreadyFollowing = $follower
            ->following()
            ->whereKey($followed->id)
            ->exists();

        if($alreadyFollowing) {
            throw ValidationException::withMessages([
                'followed' => 'Você já está seguindo este usuário'
            ]);
        }

        $follower->following()->attach($followed->id);
    }

    public function unfollow(User $follower, User $followed): void {
        $follower->following()->detach($followed->id);
    }
}