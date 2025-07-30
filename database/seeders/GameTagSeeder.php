<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameTag;

class GameTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Genshin Impact',
            'Honkai: Star Rail',
            'Blue Archive',
            'Nikke: Goddess of Victory',
            'Arknights',
            'Fate/Grand Order',
            'Azur Lane',
            'Epic Seven',
            'Punishing: Gray Raven',
            'Reverse: 1999',
        ];

        foreach ($tags as $tag) {
            GameTag::create(['tag_name' => $tag]);
        }
    }
}
