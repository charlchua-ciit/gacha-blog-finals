@extends('layouts.app')

@section('content')
<div class="post-header">
    <div class="post-header-content">
        <h1>{{ $post->title }}</h1>
        <div class="post-meta">
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
    </div>
</div>

<article class="post-card">
    <div class="post-content">
        <p>{{ $post->content }}</p>
    </div>
    
    @if($post->gameTags->count() > 0)
        <div class="post-tags-section">
            <div class="tags-label">Game Tags:</div>
            <div class="tags-container">
                @foreach($post->gameTags as $tag)
                    <a href="{{ route('posts.byTag', $tag->tag_name) }}" class="game-tag-link">
                        <i class="fas fa-gamepad"></i>
                        <span>{{ $tag->tag_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    
    <div class="post-actions-bar">
        <div class="action-stats">
            <div class="stat-item">
                <i class="fas fa-heart"></i>
                <span id="likes-count">{{ $post->likes()->count() }}</span>
            </div>
            <div class="stat-item">
                <i class="fas fa-comment"></i>
                <span id="comments-count">{{ $post->comments()->count() }}</span>
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

<!-- Comments Section -->
<div class="comments-section">
    <h2>Comments ({{ $post->comments()->count() }})</h2>
    
    @auth
        <div class="comment-form-container">
            <form class="comment-form" id="commentForm">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <div class="form-group">
                    <textarea name="content" class="comment-input" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" class="comment-submit">
                    <i class="fas fa-paper-plane"></i>
                    <span>Post Comment</span>
                </button>
            </form>
        </div>
    @else
        <div class="login-prompt">
            <p>Please <a href="{{ route('login') }}">login</a> to leave a comment.</p>
        </div>
    @endauth
    
    <div class="comments-list" id="commentsList">
        @forelse($post->comments()->with('user')->latest()->get() as $comment)
            <div class="comment-item" data-comment-id="{{ $comment->id }}">
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
                    
                    @can('update', $comment)
                        <div class="comment-actions">
                            <button class="action-btn edit-comment-btn" onclick="editComment({{ $comment->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" 
                                        onclick="return confirm('Are you sure you want to delete this comment?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
                
                <div class="comment-content" id="comment-content-{{ $comment->id }}">
                    {{ $comment->content }}
                </div>
                
                @can('update', $comment)
                    <div class="comment-edit-form" id="comment-edit-{{ $comment->id }}" style="display: none;">
                        <form method="POST" action="{{ route('comments.update', $comment) }}" class="edit-form">
                            @csrf
                            @method('PUT')
                            <textarea name="content" class="comment-input" required>{{ $comment->content }}</textarea>
                            <div class="edit-actions">
                                <button type="submit" class="action-btn">
                                    <i class="fas fa-save"></i>
                                    <span>Save</span>
                                </button>
                                <button type="button" class="action-btn" onclick="cancelEdit({{ $comment->id }})">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="empty-comments">
                <i class="fas fa-comments"></i>
                <p>No comments yet. Be the first to comment!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Message Notifications -->
<div id="messageContainer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like functionality
    const likeBtn = document.querySelector('.like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const isLiked = this.dataset.liked === 'true';
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            const likesCount = document.getElementById('likes-count');
            
            // Disable button during request
            this.disabled = true;
            this.style.opacity = '0.7';
            
            // Optimistic update
            if (isLiked) {
                this.classList.remove('liked');
                this.dataset.liked = 'false';
                icon.className = 'fas fa-heart';
                text.textContent = 'Like';
                likesCount.textContent = parseInt(likesCount.textContent) - 1;
            } else {
                this.classList.add('liked');
                this.dataset.liked = 'true';
                icon.className = 'fas fa-heart';
                text.textContent = 'Liked';
                likesCount.textContent = parseInt(likesCount.textContent) + 1;
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
                        likesCount.textContent = parseInt(likesCount.textContent) + 1;
                    } else {
                        this.classList.remove('liked');
                        this.dataset.liked = 'false';
                        icon.className = 'fas fa-heart';
                        text.textContent = 'Like';
                        likesCount.textContent = parseInt(likesCount.textContent) - 1;
                    }
                    console.error('Like failed:', data.error || 'Unknown error');
                } else {
                    // Update the count with the actual count from server
                    likesCount.textContent = data.likes_count;
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
                    likesCount.textContent = parseInt(likesCount.textContent) + 1;
                } else {
                    this.classList.remove('liked');
                    this.dataset.liked = 'false';
                    icon.className = 'fas fa-heart';
                    text.textContent = 'Like';
                    likesCount.textContent = parseInt(likesCount.textContent) - 1;
                }
            });
        });
    }
    
    // Comment form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.comment-submit');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Posting...</span>';
            submitBtn.disabled = true;
            
            fetch('/comments', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 422) {
                        return response.json().then(data => {
                            const errors = data.errors || {};
                            const errorMessage = Object.values(errors).flat().join(', ') || 'Validation failed';
                            throw new Error(errorMessage);
                        });
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Clear form
                    this.reset();
                    
                    // Add new comment to the list
                    const commentsList = document.getElementById('commentsList');
                    const newComment = createCommentElement(data.comment);
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    
                    // Update comment count
                    const commentsCount = document.getElementById('comments-count');
                    commentsCount.textContent = data.comments_count;
                    
                    // Show success message
                    showMessage('Comment posted successfully!', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage(error.message || 'Failed to post comment. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

function createCommentElement(comment) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    commentDiv.dataset.commentId = comment.id;
    
    commentDiv.innerHTML = `
        <div class="comment-header">
            <div class="comment-author">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="author-info">
                    <div class="author-name">${comment.user.username}</div>
                    <div class="comment-date">Just now</div>
                </div>
            </div>
        </div>
        <div class="comment-content">
            ${comment.content}
        </div>
    `;
    
    return commentDiv;
}

function showMessage(message, type) {
    const container = document.getElementById('messageContainer');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}-message fade-in`;
    messageDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(messageDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

function editComment(commentId) {
    const contentDiv = document.getElementById(`comment-content-${commentId}`);
    const editForm = document.getElementById(`comment-edit-${commentId}`);
    
    contentDiv.style.display = 'none';
    editForm.style.display = 'block';
}

function cancelEdit(commentId) {
    const contentDiv = document.getElementById(`comment-content-${commentId}`);
    const editForm = document.getElementById(`comment-edit-${commentId}`);
    
    contentDiv.style.display = 'block';
    editForm.style.display = 'none';
}
</script>
@endsection 