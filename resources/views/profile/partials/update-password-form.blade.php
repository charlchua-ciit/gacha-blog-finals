<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">Current Password</label>
            <input type="password" id="update_password_current_password" name="current_password" required autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password">New Password</label>
            <input type="password" id="update_password_password" name="password" required autocomplete="new-password">
            @error('password', 'updatePassword')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">Confirm Password</label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Password</button>

            @if (session('status') === 'password-updated')
                <p class="success">Password updated successfully!</p>
            @endif
        </div>
    </form>
</section>
