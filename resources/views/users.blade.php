@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1>Community Members</h1>
        <p>Discover amazing gamers and content creators</p>
    </div>
</div>

<div class="users-grid">
    @forelse($users as $user)
        <div class="user-card">
            <div class="user-header">
                <div class="user-avatar">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ $user->username }}</div>
                    <div class="user-meta">
                        <span class="member-since">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="user-stats">
                <div class="stat-item">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ $user->posts()->count() }} posts</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $user->followers()->count() }} followers</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-user-friends"></i>
                    <span>{{ $user->following()->count() }} following</span>
                </div>
            </div>
            
            <div class="user-actions">
                <a href="{{ route('user.profile', $user->username) }}" class="action-btn">
                    <i class="fas fa-eye"></i>
                    <span>View Profile</span>
                </a>
                
                @auth
                    @if(Auth::id() !== $user->id)
                        @php
                            $isFollowing = $user->followers()->where('follower_id', Auth::id())->exists();
                        @endphp
                        <button class="action-btn follow-btn {{ $isFollowing ? 'following' : '' }}" 
                                data-user-id="{{ $user->id }}" 
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
            <h3>No users found</h3>
            <p>There are no users in the community yet.</p>
        </div>
    @endforelse
</div>
<!-- Follow functionality is now handled by follow.js -->
@endsection