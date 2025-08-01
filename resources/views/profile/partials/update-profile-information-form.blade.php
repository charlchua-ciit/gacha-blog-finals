<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="profile-form">
    @csrf
    @method('patch')

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required autofocus>
        @error('username')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <span class="error">{{ $message }}</span>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="email-verification">
                <p>Your email address is unverified.</p>
                <button form="send-verification" class="btn btn-outline">
                    Click here to re-send the verification email
                </button>
            </div>

            @if (session('status') === 'verification-link-sent')
                <p class="success">A new verification link has been sent to your email address.</p>
            @endif
        @endif
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Changes</button>

        @if (session('status') === 'profile-updated')
            <p class="success">Profile updated successfully!</p>
        @endif
    </div>
</form>
