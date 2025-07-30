<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    UserController, PostController, CommentController,
    LikeController, GameTagController, NotificationController, FollowController
};

Route::apiResource('users', UserController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('game-tags', GameTagController::class);

Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('likes', LikeController::class);
    Route::apiResource('notifications', NotificationController::class);
});

// Post interactions
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('posts/{post}/like', [LikeController::class, 'toggleLike']);
    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
});

// Follows only needs store & delete
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('follows', [FollowController::class, 'index']);
    Route::post('follows', [FollowController::class, 'store']);
    Route::delete('follows', [FollowController::class, 'destroy']);
});


// Test routes
Route::get('/ping', fn () => response()->json(['message' => 'API is working!']));
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/test-auth', function() {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? auth()->user()->username : null,
            'user_id' => auth()->id()
        ]);
    });
});
