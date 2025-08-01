<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Get user's posts with relationships
        $posts = $user->posts()
            ->with(['user', 'gameTags', 'comments.user', 'likes'])
            ->latest()
            ->paginate(10);
            
        // Get user's liked posts
        $likedPosts = Post::whereHas('likes', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['user', 'gameTags', 'comments.user', 'likes'])
        ->latest()
        ->take(5)
        ->get();
        
        // Get user's comments
        $comments = $user->comments()
            ->with(['post', 'user'])
            ->latest()
            ->take(5)
            ->get();
            
        // Get user statistics
        $stats = [
            'posts_count' => $user->posts()->count(),
            'likes_given' => $user->likes()->count(),
            'likes_received' => $user->posts()->withCount('likes')->get()->sum('likes_count'),
            'comments_count' => $user->comments()->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ];
        
        // Check if current user is following this user
        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = $user->followers()->where('follower_id', Auth::id())->exists();
        }
        
        Log::info('User profile viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.profile', compact('user', 'posts', 'likedPosts', 'comments', 'stats', 'isFollowing'));
    }
    
    public function posts($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $posts = $user->posts()
            ->with(['user', 'gameTags', 'comments.user', 'likes'])
            ->latest()
            ->paginate(15);
            
        Log::info('User posts viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.posts', compact('user', 'posts'));
    }
    
    public function likes($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $likedPosts = Post::whereHas('likes', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['user', 'gameTags', 'comments.user', 'likes'])
        ->latest()
        ->paginate(15);
        
        Log::info('User likes viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.likes', compact('user', 'likedPosts'));
    }
    
    public function comments($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $comments = $user->comments()
            ->with(['post', 'user'])
            ->latest()
            ->paginate(15);
            
        Log::info('User comments viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.comments', compact('user', 'comments'));
    }
    
    public function followers($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $followers = $user->followers()
            ->with(['posts', 'followers', 'following'])
            ->paginate(20);
            
        Log::info('User followers viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.followers', compact('user', 'followers'));
    }
    
    public function following($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $following = $user->following()
            ->with(['posts', 'followers', 'following'])
            ->paginate(20);
            
        Log::info('User following viewed', [
            'viewer_id' => Auth::id(),
            'viewer_username' => Auth::user()?->username,
            'profile_user_id' => $user->id,
            'profile_username' => $user->username
        ]);
        
        return view('users.following', compact('user', 'following'));
    }
}
