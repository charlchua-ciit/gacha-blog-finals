<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index() {
        return Like::with(['user', 'post'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        return Like::firstOrCreate($validated);
    }

    public function show(Like $like) {
        return $like->load(['user', 'post']);
    }

    public function destroy(Like $like) {
        $like->delete();
        return response()->noContent();
    }
}

