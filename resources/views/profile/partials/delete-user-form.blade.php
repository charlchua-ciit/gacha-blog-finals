<div class="delete-account-section">
    <div class="warning-box">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="warning-content">
            <h4>Permanent Account Deletion</h4>
            <p>Once your account is deleted, all of its resources and data will be permanently deleted. This includes:</p>
            <ul>
                <li>All your posts and comments</li>
                <li>Your profile information</li>
                <li>Your likes and follows</li>
                <li>All associated data</li>
            </ul>
            <p><strong>Before deleting your account, please download any data or information that you wish to retain.</strong></p>
        </div>
    </div>

    <button type="button" class="btn btn-danger delete-trigger" onclick="showDeleteModal()">
        <i class="fas fa-trash"></i>
        <span>Delete Account</span>
    </button>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay" style="display: none;">
        <div class="modal-content delete-modal">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Delete Account</h3>
                </div>
                <button type="button" class="modal-close" onclick="hideDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="post" action="{{ route('profile.destroy') }}" class="delete-form">
                @csrf
                @method('delete')

                <div class="warning-message">
                    <div class="warning-icon-large">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="warning-text">
                        <h4>This action cannot be undone</h4>
                        <p>Are you absolutely sure you want to delete your account? This will permanently remove all your data from our system.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="delete_password">
                        <i class="fas fa-lock"></i>
                        Confirm Password
                    </label>
                    <input type="password" id="delete_password" name="password" required placeholder="Enter your password to confirm deletion">
                    @error('password', 'userDeletion')
                        <span class="error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline cancel-btn" onclick="hideDeleteModal()">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="submit" class="btn btn-danger confirm-delete-btn">
                        <i class="fas fa-trash"></i>
                        <span>Delete Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
