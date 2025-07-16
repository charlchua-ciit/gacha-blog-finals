<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\UserSeeder;
use Database\Seeders\FollowSeeder;
use Database\Seeders\GameTagSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\PostGameTagSeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\LikeSeeder;
use Database\Seeders\NotificationSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            FollowSeeder::class,
            GameTagSeeder::class,
            PostSeeder::class,
            PostGameTagSeeder::class,
            CommentSeeder::class,
            LikeSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}

