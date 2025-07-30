<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:500',
        ]);

        try {
            $post = Post::findOrFail($request->post_id);

            // Log the request data for debugging
            Log::info('Comment creation attempt', [
                'user_id' => Auth::id(),
                'post_id' => $request->post_id,
                'content' => $request->content,
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'headers' => $request->headers->all()
            ]);

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'post_id' => $request->post_id,
                'content' => $request->content,
            ]);

            // Load the user relationship for the response
            $comment->load('user');

            Log::info('Comment created via web', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'post_id' => $request->post_id,
                'post_author' => $post->user->username ?? 'Unknown',
                'comment_id' => $comment->id,
                'content_length' => strlen($request->content)
            ]);

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                $commentsCount = Comment::where('post_id', $request->post_id)->count();
                
                return response()->json([
                    'success' => true,
                    'comment' => $comment,
                    'comments_count' => $commentsCount
                ]);
            }

            // Regular form submission - redirect
            return redirect()->route('posts.show', $post)->with('success', 'Comment posted successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating comment via web', [
                'user_id' => Auth::id(),
                'post_id' => $request->post_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to post comment. Please try again.'
                ], 500);
            }
            
            return back()->withInput()->withErrors(['error' => 'Failed to post comment. Please try again.']);
        }
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            Log::warning('Unauthorized comment update attempt via web', [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id,
                'comment_owner' => $comment->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        try {
            $comment->update($request->only('content'));
            $comment->load('user');

            Log::info('Comment updated via web', [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id,
                'post_id' => $comment->post_id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'comment' => $comment
                ]);
            }

            return redirect()->route('posts.show', $comment->post)->with('success', 'Comment updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating comment via web', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to update comment. Please try again.'
                ], 500);
            }
            
            return back()->withInput()->withErrors(['error' => 'Failed to update comment. Please try again.']);
        }
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            Log::warning('Unauthorized comment deletion attempt via web', [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id,
                'comment_owner' => $comment->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        try {
            $commentId = $comment->id;
            $postId = $comment->post_id;
            $post = $comment->post;
            
            $comment->delete();

            Log::info('Comment deleted via web', [
                'user_id' => Auth::id(),
                'comment_id' => $commentId,
                'post_id' => $postId
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                $commentsCount = Comment::where('post_id', $postId)->count();
                return response()->json([
                    'success' => true,
                    'comments_count' => $commentsCount
                ]);
            }

            return redirect()->route('posts.show', $post)->with('success', 'Comment deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting comment via web', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete comment. Please try again.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to delete comment. Please try again.']);
        }
    }
}
