@extends('layouts.app')

@section('content')
<div class="profile-page">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-cover">
            <div class="profile-avatar-large">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        
        <div class="profile-info-section">
            <div class="profile-main-info">
                <h1 class="profile-name">{{ $user->username }}</h1>
                <div class="profile-meta">
                    <span class="member-since">Member since {{ $user->created_at->format('M Y') }}</span>
                    @if($user->email)
                        <span class="email">{{ $user->email }}</span>
                    @endif
                </div>
            </div>
            
            @auth
                @if(Auth::id() !== $user->id)
                    <div class="profile-actions">
                        @php
                            $isFollowing = $user->followers()->where('follower_id', Auth::id())->exists();
                        @endphp
                        <button class="btn btn-primary follow-btn {{ $isFollowing ? 'following' : '' }}" 
                                data-user-id="{{ $user->id }}" 
                                data-following="{{ $isFollowing ? 'true' : 'false' }}">
                            <i class="fas {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                            <span>{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                        </button>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Profile Navigation -->
    <div class="profile-nav">
        <a href="{{ route('user.profile', $user->username) }}" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Overview</span>
        </a>
        <a href="{{ route('user.posts', $user->username) }}" class="nav-item">
            <i class="fas fa-file-alt"></i>
            <span>Posts</span>
        </a>
        <a href="{{ route('user.likes', $user->username) }}" class="nav-item active">
            <i class="fas fa-heart"></i>
            <span>Likes</span>
        </a>
        <a href="{{ route('user.comments', $user->username) }}" class="nav-item">
            <i class="fas fa-comment"></i>
            <span>Comments</span>
        </a>
        <a href="{{ route('user.followers', $user->username) }}" class="nav-item">
            <i class="fas fa-users"></i>
            <span>Followers</span>
        </a>
        <a href="{{ route('user.following', $user->username) }}" class="nav-item">
            <i class="fas fa-user-friends"></i>
            <span>Following</span>
        </a>
    </div>

    <!-- Likes Content -->
    <div class="profile-content">
        <div class="content-section">
            <div class="section-header">
                <h2>{{ $user->username }}'s Liked Posts</h2>
                <div class="likes-count">{{ $likedPosts->total() }} liked posts</div>
            </div>
            
            <div class="posts-grid">
                @forelse($likedPosts as $post)
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
                            <div class="post-excerpt-container">
                                @if(strlen($post->content) > 200)
                                    <p class="post-excerpt" id="user-likes-excerpt-{{ $post->id }}">
                                        {{ Str::limit($post->content, 200) }}
                                    </p>
                                    <p class="post-full-content" id="user-likes-full-{{ $post->id }}" style="display: none;">
                                        {{ $post->content }}
                                    </p>
                                    <button class="see-more-btn" onclick="toggleUserLikesContent({{ $post->id }})" id="user-likes-toggle-{{ $post->id }}">
                                        <i class="fas fa-chevron-down"></i>
                                        <span>See more</span>
                                    </button>
                                @else
                                    <p class="post-excerpt">{{ $post->content }}</p>
                                @endif
                            </div>
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
                        <i class="fas fa-heart"></i>
                        <h3>No liked posts yet</h3>
                        <p>{{ $user->username }} hasn't liked any posts yet.</p>
                        @auth
                            @if(Auth::id() === $user->id)
                                <a href="{{ route('posts.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    <span>Explore Posts</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endforelse
            </div>
            
            @if($likedPosts->hasPages())
                <div class="pagination">
                    {{ $likedPosts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Toggle content function for user likes "See more" feature
function toggleUserLikesContent(postId) {
    const excerpt = document.getElementById(`user-likes-excerpt-${postId}`);
    const fullContent = document.getElementById(`user-likes-full-${postId}`);
    const toggleBtn = document.getElementById(`user-likes-toggle-${postId}`);
    const icon = toggleBtn.querySelector('i');
    const text = toggleBtn.querySelector('span');
    
    if (fullContent.style.display === 'none') {
        // Show full content
        excerpt.style.display = 'none';
        fullContent.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
        text.textContent = 'See less';
    } else {
        // Show excerpt
        excerpt.style.display = 'block';
        fullContent.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
        text.textContent = 'See more';
    }
}

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

    // Follow functionality
    const followButtons = document.querySelectorAll('.follow-btn');
    
    followButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const isFollowing = this.dataset.following === 'true';
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            
            // Disable button during request
            this.disabled = true;
            this.style.opacity = '0.7';
            
            // Optimistic update
            if (isFollowing) {
                this.classList.remove('following');
                this.dataset.following = 'false';
                icon.className = 'fas fa-user-plus';
                text.textContent = 'Follow';
            } else {
                this.classList.add('following');
                this.dataset.following = 'true';
                icon.className = 'fas fa-user-check';
                text.textContent = 'Following';
            }
            
            // Send request
            fetch('/follow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                credentials: 'same-origin',
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Follow response:', data);
                if (!data.success) {
                    // Revert optimistic update on error
                    if (isFollowing) {
                        this.classList.add('following');
                        this.dataset.following = 'true';
                        icon.className = 'fas fa-user-check';
                        text.textContent = 'Following';
                    } else {
                        this.classList.remove('following');
                        this.dataset.following = 'false';
                        icon.className = 'fas fa-user-plus';
                        text.textContent = 'Follow';
                    }
                    console.error('Follow failed:', data.error || 'Unknown error');
                } else {
                    // Update any follower count displays if they exist
                    const followerCountElements = document.querySelectorAll('.followers-count');
                    followerCountElements.forEach(element => {
                        if (element.textContent.includes('followers')) {
                            element.textContent = `${data.followers_count} followers`;
                        }
                    });
                }
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.style.opacity = '1';
            })
            .catch(error => {
                console.error('Follow error:', error);
                // Revert optimistic update on error
                if (isFollowing) {
                    this.classList.add('following');
                    this.dataset.following = 'true';
                    icon.className = 'fas fa-user-check';
                    text.textContent = 'Following';
                } else {
                    this.classList.remove('following');
                    this.dataset.following = 'false';
                    icon.className = 'fas fa-user-plus';
                    text.textContent = 'Follow';
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