<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index() {
        return Comment::with(['user', 'post'])->get();
    }

    public function store(Request $request) {
        $user = Auth::user();
        
        if (!$user) {
            Log::warning('Unauthorized comment attempt', [
                'post_id' => $request->post_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'post_id' => 'required|exists:posts,id',
                'content' => 'required|string|max:500',
            ]);

            $post = Post::findOrFail($validated['post_id']);

            $comment = Comment::create([
                'user_id' => $user->id,
                'post_id' => $validated['post_id'],
                'content' => $validated['content'],
            ]);

            $comment->load('user');

            $commentsCount = Comment::where('post_id', $validated['post_id'])->count();

            Log::info('Comment created', [
                'user_id' => $user->id,
                'username' => $user->username,
                'post_id' => $validated['post_id'],
                'post_author' => $post->user->username ?? 'Unknown',
                'comment_id' => $comment->id,
                'content_length' => strlen($validated['content'])
            ]);

            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => $commentsCount
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Comment validation failed', [
                'user_id' => $user->id,
                'errors' => $e->errors(),
                'input' => $request->only(['post_id', 'content'])
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creating comment', [
                'user_id' => $user->id,
                'post_id' => $request->post_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to create comment'], 500);
        }
    }

    public function show(Comment $comment) {
        return $comment->load(['user', 'post']);
    }

    public function update(Request $request, Comment $comment) {
        try {
            $user = Auth::user();
            
            if (!$user || $user->id !== $comment->user_id) {
                Log::warning('Unauthorized comment update attempt', [
                    'user_id' => $user->id ?? 'guest',
                    'comment_id' => $comment->id,
                    'comment_owner' => $comment->user_id
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'content' => 'required|string|max:500',
            ]);

            $comment->update($validated);

            Log::info('Comment updated', [
                'user_id' => $user->id,
                'comment_id' => $comment->id,
                'post_id' => $comment->post_id
            ]);

            return $comment->load('user');
        } catch (\Exception $e) {
            Log::error('Error updating comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to update comment'], 500);
        }
    }

    public function destroy(Comment $comment) {
        try {
            $user = Auth::user();
            
            if (!$user || $user->id !== $comment->user_id) {
                Log::warning('Unauthorized comment deletion attempt', [
                    'user_id' => $user->id ?? 'guest',
                    'comment_id' => $comment->id,
                    'comment_owner' => $comment->user_id
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $commentId = $comment->id;
            $postId = $comment->post_id;
            
            $comment->delete();

            Log::info('Comment deleted', [
                'user_id' => $user->id,
                'comment_id' => $commentId,
                'post_id' => $postId
            ]);

            return response()->noContent();
        } catch (\Exception $e) {
            Log::error('Error deleting comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to delete comment'], 500);
        }
    }
}
