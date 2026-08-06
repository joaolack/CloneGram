<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService 
{
    public function getOwnProfile(User $user): User {
        return $user;
    }

    public function getOtherProfile(User $user): User {
        return $user;
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

        return $user->fresh();
    }
}