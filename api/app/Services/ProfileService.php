<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService 
{
    public function getOwnProfile(User $user): User {
        return $user
            ->loadCount([
                'posts',
                'followers',
                'following'
            ])
            ->load(['posts' => fn ($query) => $query->latest(),
            ]);
    }

    public function getOtherProfile(User $authenticatedUser, User $profileUser): User {
        $profileUser->loadCount([
            'posts',
            'followers',
            'following'
        ])
        ->load([
            'posts' => fn ($query) => $query->latest(),
        ]);

        if (! $authenticatedUser->is($profileUser)) {
            $isFollowing = $authenticatedUser
                ->following()
                ->whereKey($profileUser->id)
                ->exists();
            
            $profileUser->setAttribute(
                'is_following',
                $isFollowing
            );
        }

        return $profileUser;
    }

    public function update(User $user, array $data): User {
        $oldAvatarPath = $user->avatar_path;

        if (isset($data['avatar'])) {
            $data['avatar_path'] = $data['avatar']
                ->store('avatars', 'public');
            
            unset($data['avatar']);
        }

        $user->update($data);

        if (
            isset($data['avatar_path'])
            && $oldAvatarPath
            && $oldAvatarPath !== $data['avatar_path']
        ) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        return $this->getOwnProfile($user->fresh());
    }
}