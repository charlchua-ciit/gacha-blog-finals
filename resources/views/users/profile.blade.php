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

    <!-- Profile Stats -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['posts_count'] }}</div>
                <div class="stat-label">Posts</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['likes_given'] }}</div>
                <div class="stat-label">Likes Given</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-thumbs-up"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['likes_received'] }}</div>
                <div class="stat-label">Likes Received</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-comment"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['comments_count'] }}</div>
                <div class="stat-label">Comments</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['followers_count'] }}</div>
                <div class="stat-label">Followers</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['following_count'] }}</div>
                <div class="stat-label">Following</div>
            </div>
        </div>
    </div>

    <!-- Profile Navigation -->
    <div class="profile-nav">
        <a href="{{ route('user.profile', $user->username) }}" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Overview</span>
        </a>
        <a href="{{ route('user.posts', $user->username) }}" class="nav-item">
            <i class="fas fa-file-alt"></i>
            <span>Posts</span>
        </a>
        <a href="{{ route('user.likes', $user->username) }}" class="nav-item">
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

    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Recent Posts -->
        <div class="content-section">
            <div class="section-header">
                <h2>Recent Posts</h2>
                <a href="{{ route('user.posts', $user->username) }}" class="view-all">View All</a>
            </div>
            
            <div class="posts-grid">
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
                            <div class="post-excerpt-container">
                                @if(strlen($post->content) > 150)
                                    <p class="post-excerpt" id="profile-posts-excerpt-{{ $post->id }}">
                                        {{ Str::limit($post->content, 150) }}
                                    </p>
                                    <p class="post-full-content" id="profile-posts-full-{{ $post->id }}" style="display: none;">
                                        {{ $post->content }}
                                    </p>
                                    <button class="see-more-btn" onclick="toggleProfilePostsContent({{ $post->id }})" id="profile-posts-toggle-{{ $post->id }}">
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
                        <i class="fas fa-file-alt"></i>
                        <h3>No posts yet</h3>
                        <p>{{ $user->username }} hasn't created any posts yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Liked Posts -->
        @if($likedPosts->count() > 0)
            <div class="content-section">
                <div class="section-header">
                    <h2>Recently Liked</h2>
                    <a href="{{ route('user.likes', $user->username) }}" class="view-all">View All</a>
                </div>
                
                <div class="posts-grid">
                    @foreach($likedPosts as $post)
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
                                    @if(strlen($post->content) > 150)
                                        <p class="post-excerpt" id="profile-liked-excerpt-{{ $post->id }}">
                                            {{ Str::limit($post->content, 150) }}
                                        </p>
                                        <p class="post-full-content" id="profile-liked-full-{{ $post->id }}" style="display: none;">
                                            {{ $post->content }}
                                        </p>
                                        <button class="see-more-btn" onclick="toggleProfileLikedContent({{ $post->id }})" id="profile-liked-toggle-{{ $post->id }}">
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
                                    @else
                                        <a href="{{ route('login') }}" class="action-btn">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Login to interact</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Comments -->
        @if($comments->count() > 0)
            <div class="content-section">
                <div class="section-header">
                    <h2>Recent Comments</h2>
                    <a href="{{ route('user.comments', $user->username) }}" class="view-all">View All</a>
                </div>
                
                <div class="comments-list">
                    @foreach($comments as $comment)
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-author">
                                    <div class="avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="author-info">
                                        <div class="author-name">{{ $comment->user->username }}</div>
                                        <div class="comment-date">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="comment-content">
                                <p>{{ Str::limit($comment->content, 200) }}</p>
                            </div>
                            
                            <div class="comment-meta">
                                <span class="on-post">on</span>
                                <a href="{{ route('posts.show', $comment->post) }}" class="post-link">
                                    {{ Str::limit($comment->post->title ?? $comment->post->content, 50) }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Toggle content function for profile posts "See more" feature
function toggleProfilePostsContent(postId) {
    const excerpt = document.getElementById(`profile-posts-excerpt-${postId}`);
    const fullContent = document.getElementById(`profile-posts-full-${postId}`);
    const toggleBtn = document.getElementById(`profile-posts-toggle-${postId}`);
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

// Toggle content function for profile liked posts "See more" feature
function toggleProfileLikedContent(postId) {
    const excerpt = document.getElementById(`profile-liked-excerpt-${postId}`);
    const fullContent = document.getElementById(`profile-liked-full-${postId}`);
    const toggleBtn = document.getElementById(`profile-liked-toggle-${postId}`);
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

    // Follow functionality is now handled by follow.js
});
</script>
@endsection 