<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    UserController, PostController, CommentController,
    LikeController, GameTagController, NotificationController, FollowController
};

Route::apiResource('users', UserController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('likes', LikeController::class);
Route::apiResource('game-tags', GameTagController::class);
Route::apiResource('notifications', NotificationController::class);

// Follows only needs store & delete
Route::get('follows', [FollowController::class, 'index']);
Route::post('follows', [FollowController::class, 'store']);
Route::delete('follows', [FollowController::class, 'destroy']);


//Route::get('/ping', fn () => response()->json(['message' => 'API is working!']));  api test
