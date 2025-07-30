@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Create New Post</h1>
        <a href="{{ route('posts.index') }}" style="color: var(--text-secondary); text-decoration: none;">← Back to Posts</a>
    </div>

    <div class="card">
        @if ($errors->any())
            <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('posts.store') }}">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label for="content">Post Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="6" 
                    placeholder="Share your gacha gaming experience, pulls, strategies, or thoughts..."
                    required
                >{{ old('content') }}</textarea>
                @error('content')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="margin-bottom: 0.75rem; display: block;">Game Tags (Optional)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;">
                    @foreach($tags as $tag)
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.5rem; cursor: pointer; transition: all 0.2s ease;">
                            <input 
                                type="checkbox" 
                                name="game_tags[]" 
                                value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('game_tags', [])) ? 'checked' : '' }}
                            >
                            <span>{{ $tag->tag_name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('game_tags')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn" style="flex: 1;">
                    Create Post
                </button>
                <a href="{{ route('posts.index') }}" style="padding: 0.75rem 1.5rem; background: var(--text-muted); color: white; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; transition: all 0.2s ease; text-decoration: none; display: inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 