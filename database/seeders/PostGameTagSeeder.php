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

        if ($posts->count() > 0 && $tags->count() > 0) {
            foreach ($posts as $post) {
                $tagIds = $tags->random(rand(1, min(3, $tags->count())))->pluck('id');
                foreach ($tagIds as $tagId) {
                    DB::table('post_game_tags')->insertOrIgnore([
                        'post_id' => $post->id,
                        'tag_id' => $tagId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

