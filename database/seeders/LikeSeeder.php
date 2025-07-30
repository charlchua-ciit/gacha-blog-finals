<?php

namespace Database\Seeders;

use App\Models\Like;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    public function run()
    {
        // Create likes without duplicates
        for ($i = 0; $i < 30; $i++) {
            $userId = \App\Models\User::inRandomOrder()->first()->id;
            $postId = \App\Models\Post::inRandomOrder()->first()->id;
            
            // Use firstOrCreate to avoid duplicates
            Like::firstOrCreate([
                'user_id' => $userId,
                'post_id' => $postId
            ]);
        }
    }
}

