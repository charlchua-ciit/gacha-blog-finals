<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function toggleFollow($userId) {
        $user = User::findOrFail($userId);
        
        // Check authentication first
        if (!Auth::check()) {
            Log::warning('Unauthorized follow attempt - not authenticated', [
                'target_user_id' => $userId,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId()
            ]);
            return response()->json(['error' => 'Unauthorized - Please log in'], 401);
        }
        
        $currentUser = Auth::user();
        
        Log::info('Follow toggle request received', [
            'user_id' => $user->id,
            'username' => $user->username,
            'auth_check' => Auth::check(),
            'current_user_id' => $currentUser->id,
            'session_id' => session()->getId()
        ]);

        // Prevent self-following
        if ($currentUser->id === $user->id) {
            Log::warning('Self-follow attempt', [
                'user_id' => $currentUser->id,
                'username' => $currentUser->username
            ]);
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        try {
            Log::info('Follow toggle request', [
                'current_user_id' => $currentUser->id,
                'current_username' => $currentUser->username,
                'target_user_id' => $user->id,
                'target_username' => $user->username
            ]);

            // Check if already following
            $isCurrentlyFollowing = Follow::isFollowing($currentUser->id, $user->id);

            if ($isCurrentlyFollowing) {
                // Unfollow
                Follow::removeFollow($currentUser->id, $user->id);
                $following = false;
                
                Log::info('User unfollowed', [
                    'follower_id' => $currentUser->id,
                    'follower_username' => $currentUser->username,
                    'followee_id' => $user->id,
                    'followee_username' => $user->username
                ]);
            } else {
                // Follow
                Follow::createFollow($currentUser->id, $user->id);
                $following = true;
                
                Log::info('User followed', [
                    'follower_id' => $currentUser->id,
                    'follower_username' => $currentUser->username,
                    'followee_id' => $user->id,
                    'followee_username' => $user->username
                ]);
            }

            // Get updated counts
            $followersCount = $user->followers()->count();
            $followingCount = $user->following()->count();

            Log::info('Follow toggle completed', [
                'current_user_id' => $currentUser->id,
                'target_user_id' => $user->id,
                'following' => $following,
                'followers_count' => $followersCount,
                'following_count' => $followingCount
            ]);

            return response()->json([
                'success' => true,
                'following' => $following,
                'followers_count' => $followersCount,
                'following_count' => $followingCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling follow', [
                'current_user_id' => $currentUser->id,
                'target_user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to toggle follow'], 500);
        }
    }

    /**
     * Get the current follow status for a user
     */
    public function getFollowStatus($userId) {
        $user = User::findOrFail($userId);
        
        // Check authentication
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $currentUser = Auth::user();
        
        // Prevent checking self-follow status
        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Cannot check self-follow status'], 400);
        }

        try {
            $isFollowing = Follow::isFollowing($currentUser->id, $user->id);
            
            return response()->json([
                'success' => true,
                'following' => $isFollowing,
                'follower_id' => $currentUser->id,
                'followee_id' => $user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting follow status', [
                'current_user_id' => $currentUser->id,
                'target_user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to get follow status'], 500);
        }
    }
}

