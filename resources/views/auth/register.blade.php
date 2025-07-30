@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 0 auto;">
    <div class="card">
        <h1 style="text-align: center; margin-bottom: 2rem;">Register</h1>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="username" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('username')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('email')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('password')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password_confirmation" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('password_confirmation')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <button type="submit" style="width: 100%; padding: 0.75rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background-color 0.2s;">
                    Register
                </button>
            </div>
        </form>
    </div>

    <div class="card" style="text-align: center; margin-top: 1rem;">
        <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Already have an account?</p>
        <a href="{{ route('login') }}" style="color: var(--accent); text-decoration: none; font-weight: 500;">Login here</a>
    </div>
</div>
@endsection
