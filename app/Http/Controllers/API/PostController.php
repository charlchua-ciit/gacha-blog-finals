<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() {
        return Post::with(['user', 'comments', 'likes', 'gameTags'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        return Post::create($validated);
    }

    public function show(Post $post) {
        return $post->load(['user', 'comments', 'likes', 'gameTags']);
    }

    public function update(Request $request, Post $post) {
        $post->update($request->only('content'));
        return $post;
    }

    public function destroy(Post $post) {
        $post->delete();
        return response()->noContent();
    }
}
