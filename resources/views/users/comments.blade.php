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
        <a href="{{ route('user.likes', $user->username) }}" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>Likes</span>
        </a>
        <a href="{{ route('user.comments', $user->username) }}" class="nav-item active">
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

    <!-- Comments Content -->
    <div class="profile-content">
        <div class="content-section">
            <div class="section-header">
                <h2>{{ $user->username }}'s Comments</h2>
                <div class="comments-count">{{ $comments->total() }} comments</div>
            </div>
            
            <div class="comments-list">
                @forelse($comments as $comment)
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
                            <p>{{ $comment->content }}</p>
                        </div>
                        
                        <div class="comment-meta">
                            <span class="on-post">on</span>
                            <a href="{{ route('posts.show', $comment->post) }}" class="post-link">
                                {{ Str::limit($comment->post->title ?? $comment->post->content, 100) }}
                            </a>
                        </div>
                        
                        @auth
                            @if(Auth::id() === $comment->user_id)
                                <div class="comment-actions">
                                    <button class="action-btn edit-comment-btn" data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" 
                                                onclick="return confirm('Are you sure you want to delete this comment?')">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-comment"></i>
                        <h3>No comments yet</h3>
                        <p>{{ $user->username }} hasn't made any comments yet.</p>
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
            
            @if($comments->hasPages())
                <div class="pagination">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 