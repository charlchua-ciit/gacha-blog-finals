@extends('layouts.app')

@section('content')
<div class="card home-card">
    <div class="avatar avatar-lg"></div>
    <div>
        <h1>Welcome back, {{ Auth::user()->username }}!</h1>
        <p>Manage your posts, check notifications, and stay updated with the latest gacha gaming content.</p>
    </div>
</div>

<div style="display: flex; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="flex: 1;">
        <h2 style="margin-bottom: 1rem; color: var(--text-primary);">Your Activity</h2>
        <div style="color: var(--text-secondary);">
            <p>You have {{ Auth::user()->posts->count() }} posts</p>
            <p>You have {{ Auth::user()->comments->count() }} comments</p>
            <p>You have {{ Auth::user()->likes->count() }} likes</p>
        </div>
    </div>
    
    <div class="card" style="flex: 1;">
        <h2 style="margin-bottom: 1rem; color: var(--text-primary);">Quick Actions</h2>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('posts.create') }}" class="btn" style="text-decoration: none; display: inline-block; text-align: center;">
                Create New Post
            </a>
            <a href="{{ route('posts.index') }}" style="padding: 0.75rem 1.5rem; background: var(--text-muted); color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease; text-decoration: none; display: inline-block; text-align: center;">
                View All Posts
            </a>
        </div>
    </div>
</div>

@if(Auth::user()->posts->count() > 0)
    <div class="card">
        <h2 style="margin-bottom: 1rem; color: var(--text-primary);">Your Recent Posts</h2>
        <div class="post-list">
            @foreach(Auth::user()->posts()->with('gameTags')->latest()->take(3)->get() as $post)
                <div class="card post-card" style="margin-bottom: 1rem;">
                    <div class="avatar"></div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div>
                                <div class="post-author">{{ $post->user->username }}</div>
                                <div style="color: var(--text-muted); font-size: 0.75rem;">
                                    {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('posts.edit', $post) }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem;">Edit</a>
                                <a href="{{ route('posts.show', $post) }}" style="color: var(--accent); text-decoration: none; font-size: 0.875rem;">View</a>
                            </div>
                        </div>
                        <div class="post-content">
                            {{ Str::limit($post->content, 150) }}
                        </div>
                        @if($post->gameTags && $post->gameTags->count() > 0)
                            <div style="margin-top: 0.75rem;">
                                @foreach($post->gameTags as $tag)
                                    <a href="{{ route('posts.byTag', $tag) }}" class="game-tag-link" style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-right: 0.5rem; display: inline-block;">
                                        {{ $tag->tag_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if(Auth::user()->posts->count() > 3)
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('posts.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">View all your posts →</a>
            </div>
        @endif
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem;">
        <h2 style="margin-bottom: 1rem; color: var(--text-primary);">No Posts Yet</h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Start sharing your gacha gaming experiences!</p>
        <a href="{{ route('posts.create') }}" class="btn">Create Your First Post</a>
    </div>
@endif
@endsection
