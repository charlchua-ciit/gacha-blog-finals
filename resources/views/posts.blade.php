@extends('layouts.app')

@section('content')
<h1>Posts</h1>
<div class="post-list">
    @forelse($posts as $post)
        <div class="card post-card">
            <div class="avatar"></div>
            <div>
                <div class="post-author">{{ $post->user->username ?? 'Unknown' }}</div>
                <div class="post-content">{{ $post->content }}</div>
            </div>
        </div>
    @empty
        <div>No posts found.</div>
    @endforelse
</div>
@endsection