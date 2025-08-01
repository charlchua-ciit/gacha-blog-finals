@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1>Edit Profile</h1>
        <p>Update your account information and settings</p>
    </div>
</div>

<div class="profile-edit-container">
    @if(session('status') === 'profile-updated')
        <div class="success fade-in">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('message', 'Profile updated successfully!') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="error fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="error fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Profile Information</h2>
            <p>Update your username and email address</p>
        </div>
        <div class="card-content">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Change Password</h2>
            <p>Update your password for security</p>
        </div>
        <div class="card-content">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Delete Account</h2>
            <p>Permanently delete your account and all data</p>
        </div>
        <div class="card-content">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>

<script>
// Enhanced form validation and user experience
document.addEventListener('DOMContentLoaded', function() {
    // Real-time username validation
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            const usernameError = this.parentNode.querySelector('.error');
            
            // Clear previous error
            if (usernameError) {
                usernameError.remove();
            }
            
            // Validate username format
            if (username.length > 0) {
                if (username.length < 3) {
                    showFieldError(this, 'Username must be at least 3 characters long');
                } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                    showFieldError(this, 'Username can only contain letters, numbers, and underscores');
                } else {
                    // Check if username is available (basic check)
                    checkUsernameAvailability(username, this);
                }
            }
        });
    }

    // Real-time email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const email = this.value;
            const emailError = this.parentNode.querySelector('.error');
            
            // Clear previous error
            if (emailError) {
                emailError.remove();
            }
            
            // Validate email format
            if (email.length > 0) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showFieldError(this, 'Please enter a valid email address');
                }
            }
        });
    }

    // Password strength indicator
    const passwordInput = document.getElementById('update_password_password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = this.parentNode.querySelector('.password-strength');
            
            if (!strengthIndicator) {
                const indicator = document.createElement('div');
                indicator.className = 'password-strength';
                this.parentNode.appendChild(indicator);
            }
            
            updatePasswordStrength(password, strengthIndicator || this.parentNode.querySelector('.password-strength'));
        });
    }

    // Form submission enhancement
    const profileForm = document.querySelector('form[action*="profile.update"]');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            // Re-enable after a delay (in case of validation errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});

function showFieldError(input, message) {
    const errorDiv = document.createElement('span');
    errorDiv.className = 'error';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = 'var(--accent-red)';
}

function checkUsernameAvailability(username, input) {
    // This would typically make an AJAX call to check availability
    // For now, we'll just do basic validation
    if (username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username)) {
        input.style.borderColor = 'var(--accent-green)';
    }
}

function updatePasswordStrength(password, indicator) {
    let strength = 0;
    let message = '';
    let color = '';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            message = 'Very Weak';
            color = 'var(--accent-red)';
            break;
        case 2:
            message = 'Weak';
            color = 'var(--accent-orange)';
            break;
        case 3:
            message = 'Fair';
            color = 'var(--accent-yellow)';
            break;
        case 4:
            message = 'Good';
            color = 'var(--accent-green)';
            break;
        case 5:
            message = 'Strong';
            color = 'var(--accent-green)';
            break;
    }
    
    indicator.innerHTML = `
        <div style="margin-top: 8px; font-size: 0.875rem;">
            <span style="color: ${color}; font-weight: 600;">${message}</span>
            <div style="display: flex; gap: 4px; margin-top: 4px;">
                ${Array.from({length: 5}, (_, i) => 
                    `<div style="width: 20px; height: 4px; background: ${i < strength ? color : 'var(--text-tertiary)'}; border-radius: 2px;"></div>`
                ).join('')}
            </div>
        </div>
    `;
}

// Auto-hide success messages
document.addEventListener('DOMContentLoaded', function() {
    const successMessages = document.querySelectorAll('.success');
    successMessages.forEach(function(message) {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
});
</script>
@endsection
