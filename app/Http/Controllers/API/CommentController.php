<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index() {
        return Comment::with(['user', 'post'])->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        return Comment::create($validated);
    }

    public function show(Comment $comment) {
        return $comment->load(['user', 'post']);
    }

    public function update(Request $request, Comment $comment) {
        $comment->update($request->only('content'));
        return $comment;
    }

    public function destroy(Comment $comment) {
        $comment->delete();
        return response()->noContent();
    }
}
