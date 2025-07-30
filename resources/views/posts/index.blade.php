@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1>Gaming Posts</h1>
        <p>Discover amazing gaming content from the community</p>
        
        @if(isset($tag))
            <div class="tag-filter">
                <span class="filter-label">Filtered by:</span>
                <span class="current-tag">
                    <i class="fas fa-gamepad"></i>
                    {{ $tag->tag_name }}
                </span>
                <a href="{{ route('posts.index') }}" class="clear-filter">
                    <i class="fas fa-times"></i>
                    Clear Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="post-list">
    @forelse($posts as $post)
        <article class="post-card">
            <div class="post-header">
                <div class="post-author">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="author-info">
                        <div class="author-name">{{ $post->user->username }}</div>
                        <div class="post-date">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
            
            <div class="post-content">
                <h3 class="post-title">{{ $post->title }}</h3>
                <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>
            </div>
            
            @if($post->gameTags->count() > 0)
                <div class="post-tags">
                    @foreach($post->gameTags as $tag)
                        <a href="{{ route('posts.byTag', $tag->tag_name) }}" class="game-tag-link">
                            <i class="fas fa-gamepad"></i>
                            <span>{{ $tag->tag_name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
            
            <div class="post-actions">
                <div class="action-stats">
                    <div class="stat-item">
                        <i class="fas fa-heart"></i>
                        <span>{{ $post->likes()->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-comment"></i>
                        <span>{{ $post->comments()->count() }}</span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    @auth
                        @php
                            $userLiked = $post->likes()->where('user_id', Auth::id())->exists();
                        @endphp
                        
                        <button class="action-btn like-btn {{ $userLiked ? 'liked' : '' }}" 
                                data-post-id="{{ $post->id }}" 
                                data-liked="{{ $userLiked ? 'true' : 'false' }}">
                            <i class="fas fa-heart"></i>
                            <span>{{ $userLiked ? 'Liked' : 'Like' }}</span>
                        </button>
                        
                        <a href="{{ route('posts.show', $post) }}" class="action-btn">
                            <i class="fas fa-comment"></i>
                            <span>Comment</span>
                        </a>
                        
                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="action-btn">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                        @endcan
                        
                        @can('delete', $post)
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" 
                                        onclick="return confirm('Are you sure you want to delete this post?')">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        @endcan
                    @else
                        <a href="{{ route('login') }}" class="action-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login to interact</span>
                        </a>
                    @endauth
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">
            <i class="fas fa-gamepad"></i>
            <h3>No posts yet</h3>
            <p>Be the first to share your gaming experience!</p>
            @auth
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login to Create Post</span>
                </a>
            @endauth
        </div>
    @endforelse
</div>

@if($posts->hasPages())
    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like functionality
    const likeButtons = document.querySelectorAll('.like-btn');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const isLiked = this.dataset.liked === 'true';
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            const statsContainer = this.closest('.post-actions').querySelector('.action-stats');
            const likeCount = statsContainer.querySelector('.stat-item:first-child span');
            
            // Disable button during request
            this.disabled = true;
            this.style.opacity = '0.7';
            
            // Optimistic update
            if (isLiked) {
                this.classList.remove('liked');
                this.dataset.liked = 'false';
                icon.className = 'fas fa-heart';
                text.textContent = 'Like';
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            } else {
                this.classList.add('liked');
                this.dataset.liked = 'true';
                icon.className = 'fas fa-heart';
                text.textContent = 'Liked';
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            }
            
            // Send request
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                credentials: 'same-origin',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Like response:', data);
                if (!data.success) {
                    // Revert optimistic update on error
                    if (isLiked) {
                        this.classList.add('liked');
                        this.dataset.liked = 'true';
                        icon.className = 'fas fa-heart';
                        text.textContent = 'Liked';
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        this.classList.remove('liked');
                        this.dataset.liked = 'false';
                        icon.className = 'fas fa-heart';
                        text.textContent = 'Like';
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                    console.error('Like failed:', data.error || 'Unknown error');
                } else {
                    // Update the count with the actual count from server
                    likeCount.textContent = data.likes_count;
                }
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.style.opacity = '1';
            })
            .catch(error => {
                console.error('Like error:', error);
                // Revert optimistic update on error
                if (isLiked) {
                    this.classList.add('liked');
                    this.dataset.liked = 'true';
                    icon.className = 'fas fa-heart';
                    text.textContent = 'Liked';
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                } else {
                    this.classList.remove('liked');
                    this.dataset.liked = 'false';
                    icon.className = 'fas fa-heart';
                    text.textContent = 'Like';
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                }
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
    });
});
</script>
@endsection 