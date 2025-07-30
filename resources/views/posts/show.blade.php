@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <a href="{{ route('posts.index') }}" style="color: var(--text-secondary); text-decoration: none;">← Back to Posts</a>
        @auth
            @if(Auth::id() === $post->user_id)
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('posts.edit', $post) }}" class="btn" style="text-decoration: none; display: inline-block;">
                        Edit Post
                    </a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 0.75rem 1.5rem; background: #ef4444; color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease;" onclick="return confirm('Are you sure you want to delete this post?')">
                            Delete Post
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    <div class="card">
        <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="avatar"></div>
            <div style="flex: 1;">
                <div class="post-author">{{ $post->user->username ?? 'Unknown' }}</div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">
                    {{ $post->created_at->format('F j, Y \a\t g:i A') }}
                    @if($post->updated_at != $post->created_at)
                        <span style="margin-left: 0.5rem;">(edited {{ $post->updated_at->diffForHumans() }})</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <div class="post-content" style="font-size: 1rem; line-height: 1.6; white-space: pre-wrap;">
                {{ $post->content }}
            </div>
        </div>

        @if($post->gameTags && $post->gameTags->count() > 0)
            <div style="margin-bottom: 1.5rem;">
                <div style="font-weight: 500; margin-bottom: 0.5rem; color: var(--text-primary);">Game Tags:</div>
                <div>
                    @foreach($post->gameTags as $tag)
                        <a href="{{ route('posts.byTag', $tag) }}" class="game-tag-link" style="padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; margin-right: 0.5rem; margin-bottom: 0.5rem; display: inline-block;">
                            {{ $tag->tag_name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; color: var(--text-muted); font-size: 0.875rem;">
            <div style="display: flex; gap: 2rem;">
                <span>{{ $post->likes ? $post->likes->count() : 0 }} likes</span>
                <span>{{ $post->comments ? $post->comments->count() : 0 }} comments</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="background: rgba(16, 185, 129, 0.1); border-color: #10b981; margin-bottom: 1.5rem;">
            <div style="color: #10b981;">{{ session('success') }}</div>
        </div>
    @endif
</div>
@endsection 