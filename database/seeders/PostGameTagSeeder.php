<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\GameTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostGameTagSeeder extends Seeder
{
    public function run()
    {
        $posts = Post::all();
        $tags = GameTag::all();

        foreach ($posts as $post) {
            $tagIds = $tags->random(rand(1, 3))->pluck('id');
            foreach ($tagIds as $tagId) {
                DB::table('post_game_tags')->insertOrIgnore([
                    'post_id' => $post->id,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }
}

