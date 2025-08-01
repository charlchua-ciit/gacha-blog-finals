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
        <a href="{{ route('user.followers', $user->username) }}" class="nav-item">
            <i class="fas fa-users"></i>
            <span>Followers</span>
        </a>
        <a href="{{ route('user.following', $user->username) }}" class="nav-item active">
            <i class="fas fa-user-friends"></i>
            <span>Following</span>
        </a>
    </div>

    <!-- Following Content -->
    <div class="profile-content">
        <div class="content-section">
            <div class="section-header">
                <h2>{{ $user->username }}'s Following</h2>
                <div class="following-count">{{ $following->total() }} following</div>
            </div>
            
            <div class="users-grid">
                @forelse($following as $followedUser)
                    <div class="user-card">
                        <div class="user-header">
                            <div class="user-avatar">
                                <div class="avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ $followedUser->username }}</div>
                                <div class="user-meta">
                                    <span class="member-since">Member since {{ $followedUser->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="user-stats">
                            <div class="stat-item">
                                <i class="fas fa-file-alt"></i>
                                <span>{{ $followedUser->posts()->count() }} posts</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $followedUser->followers()->count() }} followers</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-user-friends"></i>
                                <span>{{ $followedUser->following()->count() }} following</span>
                            </div>
                        </div>
                        
                        <div class="user-actions">
                            <a href="{{ route('user.profile', $followedUser->username) }}" class="action-btn">
                                <i class="fas fa-eye"></i>
                                <span>View Profile</span>
                            </a>
                            
                            @auth
                                @if(Auth::id() !== $followedUser->id)
                                    @php
                                        $isFollowing = $followedUser->followers()->where('follower_id', Auth::id())->exists();
                                    @endphp
                                    <button class="action-btn follow-btn {{ $isFollowing ? 'following' : '' }}" 
                                            data-user-id="{{ $followedUser->id }}" 
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
                        <i class="fas fa-user-friends"></i>
                        <h3>Not following anyone yet</h3>
                        <p>{{ $user->username }} isn't following anyone yet.</p>
                        @auth
                            @if(Auth::id() === $user->id)
                                <a href="{{ route('users') }}" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    <span>Discover Users</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endforelse
            </div>
            
            @if($following->hasPages())
                <div class="pagination">
                    {{ $following->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Follow functionality is now handled by follow.js -->
@endsection 