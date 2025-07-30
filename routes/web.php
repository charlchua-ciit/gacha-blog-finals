<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\User;
use App\Models\GameTag;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    $tags = GameTag::all();
    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->latest()->take(5)->get() : collect();
    return view('home', compact('tags', 'notifications'));
})->name('home');

// Post routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/tag/{tag}', [PostController::class, 'byTag'])->name('posts.byTag');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// Comment routes
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Like routes
Route::post('/posts/{post}/like', [App\Http\Controllers\API\LikeController::class, 'toggleLike'])->name('posts.like');

// Follow routes
Route::post('/follow', [FollowController::class, 'toggleFollow'])->name('follow.toggle')->middleware('auth');
Route::post('/users/{user}/follow', [App\Http\Controllers\API\FollowController::class, 'toggleFollow'])->name('api.follow.toggle')->where('user', '[0-9]+');
Route::get('/users/{user}/follow-status', [App\Http\Controllers\API\FollowController::class, 'getFollowStatus'])->name('api.follow.status')->where('user', '[0-9]+');

// Test routes for debugging
Route::get('/test-comment', function() {
    return response()->json([
        'message' => 'Comment system is working',
        'comments_count' => App\Models\Comment::count(),
        'posts_count' => App\Models\Post::count()
    ]);
});

Route::get('/test-follow-status', function() {
    $user1 = App\Models\User::first();
    $user2 = App\Models\User::skip(1)->first();
    
    $isFollowing = App\Models\Follow::where('follower_id', $user1->id)
        ->where('followee_id', $user2->id)
        ->exists();
    
    return response()->json([
        'user1_id' => $user1->id,
        'user1_username' => $user1->username,
        'user2_id' => $user2->id,
        'user2_username' => $user2->username,
        'is_following' => $isFollowing,
        'user1_followers' => $user1->followers()->count(),
        'user1_following' => $user1->following()->count(),
        'user2_followers' => $user2->followers()->count(),
        'user2_following' => $user2->following()->count(),
        'total_follows' => App\Models\Follow::count()
    ]);
});

Route::get('/test-follow-toggle', function() {
    $user1 = App\Models\User::first();
    $user2 = App\Models\User::skip(1)->first();
    
    // Simulate the follow toggle logic
    $existingFollow = App\Models\Follow::where('follower_id', $user1->id)
        ->where('followee_id', $user2->id)
        ->first();
    
    if ($existingFollow) {
        // Unfollow
        $existingFollow->delete();
        $action = 'unfollowed';
        $isFollowing = false;
    } else {
        // Follow
        App\Models\Follow::create([
            'follower_id' => $user1->id,
            'followee_id' => $user2->id
        ]);
        $action = 'followed';
        $isFollowing = true;
    }
    
    // Get updated counts
    $user1->refresh();
    $user2->refresh();
    
    return response()->json([
        'success' => true,
        'action' => $action,
        'is_following' => $isFollowing,
        'user1_followers' => $user1->followers()->count(),
        'user1_following' => $user1->following()->count(),
        'user2_followers' => $user2->followers()->count(),
        'user2_following' => $user2->following()->count(),
        'total_follows' => App\Models\Follow::count()
    ]);
});

Route::get('/test-api-follow', function() {
    $user = App\Models\User::find(2);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'username' => $user->username,
        'message' => 'API endpoint should work with this user'
    ]);
});

Route::get('/test-follow-api-route', function() {
    // Test if the route exists and can be resolved
    $route = Route::getRoutes()->get('POST');
    $followRoute = null;
    
    foreach ($route as $routeItem) {
        if (str_contains($routeItem->uri(), 'users/{user}/follow')) {
            $followRoute = $routeItem;
            break;
        }
    }
    
    return response()->json([
        'route_exists' => $followRoute ? true : false,
        'route_uri' => $followRoute ? $followRoute->uri() : 'not found',
        'route_action' => $followRoute ? $followRoute->getActionName() : 'not found',
        'test_url' => url('/users/2/follow'),
        'message' => 'Testing follow API route'
    ]);
});



Route::get('/test-auth', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->username : null,
        'user_id' => auth()->id(),
        'session_id' => session()->getId()
    ]);
});



Route::get('/users', function () {
    $users = User::with('posts')->get();
    $tags = GameTag::all();
    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->latest()->take(5)->get() : collect();
    return view('users', compact('users', 'tags', 'notifications'));
})->name('users');

// User profile routes
Route::get('/user/{username}', [UserProfileController::class, 'show'])->name('user.profile');
Route::get('/user/{username}/posts', [UserProfileController::class, 'posts'])->name('user.posts');
Route::get('/user/{username}/likes', [UserProfileController::class, 'likes'])->name('user.likes');
Route::get('/user/{username}/comments', [UserProfileController::class, 'comments'])->name('user.comments');
Route::get('/user/{username}/followers', [UserProfileController::class, 'followers'])->name('user.followers');
Route::get('/user/{username}/following', [UserProfileController::class, 'following'])->name('user.following');

// My profile route (redirects to current user's profile)
Route::get('/my-profile', function () {
    return redirect()->route('user.profile', auth()->user()->username);
})->middleware('auth')->name('my.profile');

// Dashboard route (protected)
Route::get('/dashboard', function () {
    $tags = GameTag::all();
    $notifications = Notification::where('user_id', Auth::id())->latest()->take(5)->get();
    return view('dashboard', compact('tags', 'notifications'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
