<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        $this->seedFollows($users);
        $this->seedLikes($users, $posts);
        $this->seedComments($users, $posts);
    }

    private function seedFollows($users): void
    {
        foreach ($users as $user) {
            $usersToFollow = $users
                ->where('id', '!=', $user->id)
                ->random(2);

            $user->following()->syncWithoutDetaching(
                $usersToFollow->pluck('id')->toArray()
            );
        }
    }

    private function seedLikes($users, $posts): void
    {
        foreach ($posts as $post) {
            $usersWhoLiked = $users->random(
                random_int(1, 4)
            );

            $post->likedBy()->syncWithoutDetaching(
                $usersWhoLiked->pluck('id')->toArray()
            );
        }
    }

    private function seedComments($users, $posts): void
    {
        foreach ($posts as $post) {
            $numberOfComments = random_int(1, 3);

            for ($i = 0; $i < $numberOfComments; $i++) {
                $user = $users->random();

                $post->comments()->create([
                    'user_id' => $user->id,
                    'content' => fake()->sentence(),
                ]);
            }
        }
    }
}
