<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            [
                'name' => 'Test User'
            ],
            [
                'email' => 'test@example.com',
                'password' =>  Hash::make('12345678')
            ]
        );

        Post::updateOrCreate(
            [
                'title' => 'First Post'
            ],
            [
                'content' => 'Content of the first post',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        Post::updateOrCreate(
            [
                'title' => 'Second Post'
            ],
            [
                'content' => 'Content of the second post',
                'is_published' => false,
            ]
        );
    }
}
