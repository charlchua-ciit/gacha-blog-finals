<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    public function toggleFollow(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $follower = Auth::user();
        $userToFollow = User::findOrFail($request->user_id);

        // Prevent self-following
        if ($follower->id === $userToFollow->id) {
            return response()->json([
                'success' => false,
                'error' => 'You cannot follow yourself'
            ], 400);
        }

        try {
            $existingFollow = Follow::where('follower_id', $follower->id)
                ->where('followee_id', $userToFollow->id)
                ->first();

            if ($existingFollow) {
                // Unfollow
                $existingFollow->delete();
                $isFollowing = false;
                
                Log::info('User unfollowed', [
                    'follower_id' => $follower->id,
                    'follower_username' => $follower->username,
                    'followee_id' => $userToFollow->id,
                    'followee_username' => $userToFollow->username
                ]);
            } else {
                // Follow
                Follow::create([
                    'follower_id' => $follower->id,
                    'followee_id' => $userToFollow->id
                ]);
                $isFollowing = true;
                
                Log::info('User followed', [
                    'follower_id' => $follower->id,
                    'follower_username' => $follower->username,
                    'followee_id' => $userToFollow->id,
                    'followee_username' => $userToFollow->username
                ]);
            }

            // Get updated counts
            $followersCount = $userToFollow->followers()->count();
            $followingCount = $userToFollow->following()->count();
            
            // Get current user's updated counts
            $currentUserFollowersCount = $follower->followers()->count();
            $currentUserFollowingCount = $follower->following()->count();

            return response()->json([
                'success' => true,
                'is_following' => $isFollowing,
                'target_user' => [
                    'id' => $userToFollow->id,
                    'username' => $userToFollow->username,
                    'followers_count' => $followersCount,
                    'following_count' => $followingCount
                ],
                'current_user' => [
                    'id' => $follower->id,
                    'username' => $follower->username,
                    'followers_count' => $currentUserFollowersCount,
                    'following_count' => $currentUserFollowingCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling follow', [
                'follower_id' => $follower->id,
                'followee_id' => $userToFollow->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update follow status'
            ], 500);
        }
    }
}
