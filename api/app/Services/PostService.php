<?php

class PostService 
{
    public function create(User $user, array $data): Post {
        $file = $data['media'];
        $isVideo = str_starts_with($file->getMimeType(), 'video/');
        $path = $file->store('posts', 'public');

        return $user->posts()->create([
            'media_path' => $path,
            'media_type' => $isVideo ? 'video' : 'image',
            'caption' => $data['caption'] ?? null,
        ]);
    }
}