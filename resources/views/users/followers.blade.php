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
        <a href="{{ route('user.comments', $user->username) }}" class="nav-item">
            <i class="fas fa-comment"></i>
            <span>Comments</span>
        </a>
        <a href="{{ route('user.followers', $user->username) }}" class="nav-item active">
            <i class="fas fa-users"></i>
            <span>Followers</span>
        </a>
        <a href="{{ route('user.following', $user->username) }}" class="nav-item">
            <i class="fas fa-user-friends"></i>
            <span>Following</span>
        </a>
    </div>

    <!-- Followers Content -->
    <div class="profile-content">
        <div class="content-section">
            <div class="section-header">
                <h2>{{ $user->username }}'s Followers</h2>
                <div class="followers-count">{{ $followers->total() }} followers</div>
            </div>
            
            <div class="users-grid">
                @forelse($followers as $follower)
                    <div class="user-card">
                        <div class="user-header">
                            <div class="user-avatar">
                                <div class="avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ $follower->username }}</div>
                                <div class="user-meta">
                                    <span class="member-since">Member since {{ $follower->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="user-stats">
                            <div class="stat-item">
                                <i class="fas fa-file-alt"></i>
                                <span>{{ $follower->posts()->count() }} posts</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $follower->followers()->count() }} followers</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-user-friends"></i>
                                <span>{{ $follower->following()->count() }} following</span>
                            </div>
                        </div>
                        
                        <div class="user-actions">
                            <a href="{{ route('user.profile', $follower->username) }}" class="action-btn">
                                <i class="fas fa-eye"></i>
                                <span>View Profile</span>
                            </a>
                            
                            @auth
                                @if(Auth::id() !== $follower->id)
                                    @php
                                        $isFollowing = $follower->followers()->where('follower_id', Auth::id())->exists();
                                    @endphp
                                    <button class="action-btn follow-btn {{ $isFollowing ? 'following' : '' }}" 
                                            data-user-id="{{ $follower->id }}" 
                                            data-following="{{ $isFollowing ? 'true' : 'false' }}">
                                        <i class="fas {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                                        <span>{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No followers yet</h3>
                        <p>{{ $user->username }} doesn't have any followers yet.</p>
                        @auth
                            @if(Auth::id() !== $user->id)
                                @php
                                    $isFollowing = $user->followers()->where('follower_id', Auth::id())->exists();
                                @endphp
                                <button class="btn btn-primary follow-btn {{ $isFollowing ? 'following' : '' }}" 
                                        data-user-id="{{ $user->id }}" 
                                        data-following="{{ $isFollowing ? 'true' : 'false' }}">
                                    <i class="fas {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                                    <span>{{ $isFollowing ? 'Following' : 'Be the First to Follow' }}</span>
                                </button>
                            @endif
                        @endauth
                    </div>
                @endforelse
            </div>
            
            @if($followers->hasPages())
                <div class="pagination">
                    {{ $followers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Follow functionality is now handled by follow.js -->
@endsection 