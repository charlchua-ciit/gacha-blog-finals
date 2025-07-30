<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
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

// Test routes for debugging
Route::get('/test-comment', function() {
    return response()->json([
        'message' => 'Comment system is working',
        'comments_count' => App\Models\Comment::count(),
        'posts_count' => App\Models\Post::count()
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
