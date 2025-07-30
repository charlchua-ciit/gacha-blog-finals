@extends('layouts.app')

@section('content')
<h1>Users</h1>
<div class="user-list">
    @forelse($users as $user)
        <div class="card user-card">
            <div class="avatar"></div>
            <div>
                <div class="user-name">{{ $user->username }}</div>
                <div class="user-info">{{ $user->posts->count() }} posts</div>
            </div>
        </div>
    @empty
        <div>No users found.</div>
    @endforelse
</div>
@endsection