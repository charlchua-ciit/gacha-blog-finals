<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function toggleLike(Post $post) {
        Log::info('Like toggle request received', [
            'post_id' => $post->id,
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'headers' => request()->headers->all()
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            Log::warning('Unauthorized like attempt', [
                'post_id' => $post->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId()
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            Log::info('Like toggle request', [
                'user_id' => $user->id,
                'username' => $user->username,
                'post_id' => $post->id,
                'post_title' => $post->content ? Str::limit($post->content, 50) : 'No content'
            ]);

            $existingLike = Like::where('user_id', $user->id)
                               ->where('post_id', $post->id)
                               ->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $liked = false;
                
                Log::info('Post unliked', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'post_id' => $post->id,
                    'post_author' => $post->user->username ?? 'Unknown'
                ]);
            } else {
                // Like
                Like::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
                $liked = true;
                
                Log::info('Post liked', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'post_id' => $post->id,
                    'post_author' => $post->user->username ?? 'Unknown'
                ]);
            }

            $likesCount = $post->likes()->count();

            Log::info('Like toggle completed', [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'liked' => $liked,
                'likes_count' => $likesCount
            ]);

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling like', [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to toggle like'], 500);
        }
    }
}

