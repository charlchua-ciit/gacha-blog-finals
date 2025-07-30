@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 0 auto;">
    <div class="card">
        <h1 style="text-align: center; margin-bottom: 2rem;">Login</h1>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('email')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 500;">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--card-bg); color: var(--text-primary);">
                @error('password')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="remember" style="width: 1rem; height: 1rem;">
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">Remember me</span>
                </label>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <button type="submit" style="width: 100%; padding: 0.75rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: background-color 0.2s;">
                    Log in
                </button>
            </div>

            <div style="text-align: center;">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem;">
                        Forgot your password?
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card" style="text-align: center; margin-top: 1rem;">
        <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Don't have an account?</p>
        <a href="{{ route('register') }}" style="color: var(--accent); text-decoration: none; font-weight: 500;">Register here</a>
    </div>
</div>
@endsection
