<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\GameTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of posts
     */
    public function index(): View
    {
        $posts = Post::withRelations()->latest()->paginate(10);
        $tags = GameTag::all();
        $notifications = Auth::check() ? Auth::user()->notifications()->latest()->take(5)->get() : collect();
        
        return view('posts.index', compact('posts', 'tags', 'notifications'));
    }

    /**
     * Display posts filtered by game tag
     */
    public function byTag(GameTag $tag): View
    {
        $posts = Post::withRelations()
            ->whereHas('gameTags', function($query) use ($tag) {
                $query->where('game_tags.id', $tag->id);
            })
            ->latest()
            ->paginate(10);
        
        $tags = GameTag::all();
        $notifications = Auth::check() ? Auth::user()->notifications()->latest()->take(5)->get() : collect();
        
        return view('posts.index', compact('posts', 'tags', 'notifications', 'tag'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create(): View
    {
        $tags = GameTag::all();
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        
        return view('posts.create', compact('tags', 'notifications'));
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'game_tags' => 'nullable|array',
            'game_tags.*' => 'nullable|exists:game_tags,id'
        ]);

        try {
            $post = Post::create([
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            if ($request->has('game_tags') && is_array($request->game_tags) && !empty($request->game_tags)) {
                $post->gameTags()->attach($request->game_tags);
            }

            return redirect()->route('posts.show', $post)->with('success', 'Post created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create post. Please try again.']);
        }
    }

    /**
     * Display the specified post
     */
    public function show(Post $post): View
    {
        $post->load(['user', 'gameTags', 'comments.user', 'likes']);
        $tags = GameTag::all();
        $notifications = Auth::check() ? Auth::user()->notifications()->latest()->take(5)->get() : collect();
        
        return view('posts.show', compact('post', 'tags', 'notifications'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post): View
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $tags = GameTag::all();
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        
        return view('posts.edit', compact('post', 'tags', 'notifications'));
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'game_tags' => 'nullable|array',
            'game_tags.*' => 'exists:game_tags,id'
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        $post->gameTags()->sync($request->game_tags ?? []);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post): RedirectResponse
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
} 