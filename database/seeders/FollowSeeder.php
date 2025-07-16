<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $follower) {
            $following = $users->where('id', '!=', $follower->id)->random(rand(1, 5));
            foreach ($following as $followee) {
                DB::table('follows')->insertOrIgnore([
                    'follower_id' => $follower->id,
                    'followee_id' => $followee->id,
                ]);
            }
        }
    }
}

