<?php

namespace Database\Seeders;

use App\Models\posts\Post;
use App\Models\user\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostsSeeder extends Seeder
{
    private const POSTS_COUNT = 80; // fixed number

    public function run(): void
    {
        // // Ensure user #1 exists
        // User::query()->firstOrCreate(
        //     ['id' => 1],
        //     [
        //         'name' => 'Admin User',
        //         'email' => 'admin@example.com',
        //         'password' => Hash::make('password'),
        //     ]
        // );

        // Create posts (child rows auto-created by PostFactory->afterCreating)
        Post::factory()
            ->count(self::POSTS_COUNT)
            ->create([
                'user_id' => 1, // all owned by user 1
            ]);
    }
}
