<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function index() {
        return Follow::with(['follower', 'followee'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'follower_id' => 'required|exists:users,id|different:followee_id',
            'followee_id' => 'required|exists:users,id',
        ]);

        return Follow::firstOrCreate($validated);
    }

    public function destroy(Request $request) {
        $validated = $request->validate([
            'follower_id' => 'required|exists:users,id',
            'followee_id' => 'required|exists:users,id',
        ]);

        Follow::where('follower_id', $validated['follower_id'])
              ->where('followee_id', $validated['followee_id'])
              ->delete();

        return response()->noContent();
    }
}

