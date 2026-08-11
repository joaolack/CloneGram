<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = collect(glob(database_path('seeders/assets/posts/*')))
            ->filter(fn (string $path) => is_file($path))
            ->values();

        if ($assets->isEmpty()) {
            return;
        }

        $users = User::all();
        $assetPool = collect();

        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                if ($assetPool->isEmpty()) {
                    $assetPool = $assets->shuffle()->values();
                }

                $assetPath = $assetPool->shift();
                $extension = pathinfo($assetPath, PATHINFO_EXTENSION);
                $mediaPath = 'posts/'.Str::random(40).'.'.$extension;

                Storage::disk('public')->put(
                    $mediaPath,
                    file_get_contents($assetPath)
                );

                $user->posts()->create([
                    'media_path' => $mediaPath,
                    'media_type' => in_array(strtolower($extension), ['mp4', 'mov'])
                        ? 'video'
                        : 'image',
                    'caption' => fake()->sentence(),
                ]);
            }
        }
    }
}
