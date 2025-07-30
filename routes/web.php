<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\User;
use App\Models\GameTag;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $tags = GameTag::all();
    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->latest()->take(5)->get() : collect();
    return view('home', compact('tags', 'notifications'));
})->name('home');

Route::get('/posts', function () {
    $posts = Post::with('user')->latest()->get();
    $tags = GameTag::all();
    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->latest()->take(5)->get() : collect();
    return view('posts', compact('posts', 'tags', 'notifications'));
})->name('posts');

Route::get('/users', function () {
    $users = User::with('posts')->get();
    $tags = GameTag::all();
    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->latest()->take(5)->get() : collect();
    return view('users', compact('users', 'tags', 'notifications'));
})->name('users');
