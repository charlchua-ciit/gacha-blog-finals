<?php

namespace Database\Seeders;

use App\Models\GameTag;
use Illuminate\Database\Seeder;

class GameTagSeeder extends Seeder
{
    public function run()
    {
        GameTag::factory(10)->create();
    }
}
