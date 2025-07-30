@extends('layouts.app')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>
                @if(isset($tag))
                    <i class="fas fa-tag"></i>
                    Posts tagged with "{{ $tag->tag_name }}"
                @else
                    <i class="fas fa-stream"></i>
                    All Posts
                @endif
            </h1>
            <p class="header-subtitle">
                @if(isset($tag))
                    Discover amazing content about {{ $tag->tag_name }}
                @else
                    Explore the latest gacha gaming experiences and discussions
                @endif
            </p>
        </div>
        <div class="header-actions">
            @if(isset($tag))
                <a href="{{ route('posts.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to all posts</span>
                </a>
            @endif
            @auth
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login to Post</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- Posts List -->
    <div class="posts-container">
        @forelse($posts as $post)
            <article class="post-card fade-in">
                <div class="post-avatar">
                    <div class="avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <div class="post-content">
                    <div class="post-header">
                        <div class="post-meta">
                            <div class="post-author">{{ $post->user->username ?? 'Unknown' }}</div>
                            <div class="post-time">
                                <i class="fas fa-clock"></i>
                                {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                        
                        @auth
                            @if(Auth::id() === $post->user_id)
                                <div class="post-actions">
                                    <a href="{{ route('posts.edit', $post) }}" class="action-btn edit-btn" title="Edit post">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="Delete post" onclick="return confirm('Are you sure you want to delete this post?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                    
                    <div class="post-body">
                        <a href="{{ route('posts.show', $post) }}" class="post-link">
                            {{ Str::limit($post->content, 200) }}
                        </a>
                    </div>
                    
                    @if($post->gameTags && $post->gameTags->count() > 0)
                        <div class="post-tags">
                            @foreach($post->gameTags as $tag)
                                <a href="{{ route('posts.byTag', $tag) }}" class="game-tag-link" style="padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; margin-right: 0.5rem; display: inline-block;">
                                    <i class="fas fa-gamepad"></i>
                                    {{ $tag->tag_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="post-footer">
                        <div class="post-stats">
                            <span class="stat">
                                <i class="fas fa-heart"></i>
                                {{ $post->likes ? $post->likes->count() : 0 }}
                            </span>
                            <span class="stat">
                                <i class="fas fa-comment"></i>
                                {{ $post->comments ? $post->comments->count() : 0 }}
                            </span>
                        </div>
                        <a href="{{ route('posts.show', $post) }}" class="read-more">
                            Read more
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>No posts found</h3>
                <p>
                    @if(isset($tag))
                        No posts found with the tag "{{ $tag->tag_name }}".
                    @else
                        Be the first to share your gacha gaming experience!
                    @endif
                </p>
                @auth
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Create the first post!</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login to create posts</span>
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="pagination-container">
            <div class="pagination">
                @if($posts->onFirstPage())
                    <span class="pagination-item disabled">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $posts->previousPageUrl() }}" class="pagination-item">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </a>
                @endif
                
                <div class="pagination-info">
                    Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}
                </div>
                
                @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="pagination-item">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="pagination-item disabled">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding: 2rem;
        background: var(--gradient-secondary);
        border-radius: 1.25rem;
        border: 1px solid var(--border-light);
    }

    .header-content h1 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }

    .header-content h1 i {
        color: var(--primary);
    }

    .header-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    /* Posts Container */
    .posts-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Enhanced Post Card */
    .post-card {
        display: flex;
        gap: 1.5rem;
        padding: 2rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .post-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .post-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
        border-color: var(--primary-light);
    }

    .post-card:hover::before {
        opacity: 1;
    }

    .post-avatar {
        flex-shrink: 0;
    }

    .post-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .post-meta {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .post-author {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .post-time {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .post-actions {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .action-btn:hover {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
    }

    .delete-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .post-body {
        line-height: 1.6;
    }

    .post-link {
        color: var(--text-primary);
        text-decoration: none;
        font-size: 1rem;
        line-height: 1.6;
        transition: color 0.2s ease;
    }

    .post-link:hover {
        color: var(--primary);
    }

    .post-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border-light);
    }

    .post-stats {
        display: flex;
        gap: 1rem;
    }

    .stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .stat i {
        color: var(--primary);
    }

    .read-more {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .read-more:hover {
        gap: 0.75rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--gradient-secondary);
        border-radius: 1.25rem;
        border: 1px solid var(--border-light);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .empty-state p {
        color: var(--text-secondary);
        margin-bottom: 2rem;
        font-size: 1rem;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 2rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .pagination-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .pagination-item:hover:not(.disabled) {
        color: var(--primary);
        background: rgba(99, 102, 241, 0.1);
    }

    .pagination-item.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 0.875rem;
        padding: 0 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
        }

        .header-actions {
            width: 100%;
            justify-content: center;
        }

        .post-card {
            padding: 1.5rem;
            gap: 1rem;
        }

        .post-header {
            flex-direction: column;
            gap: 1rem;
        }

        .post-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .pagination {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endsection 