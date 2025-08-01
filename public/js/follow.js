// Real-time follow functionality
class FollowManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindFollowButtons();
    }

    bindFollowButtons() {
        const followButtons = document.querySelectorAll('.follow-btn');
        
        followButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleFollowClick(button);
            });
        });
    }

    handleFollowClick(button) {
        const userId = button.dataset.userId;
        const isFollowing = button.dataset.following === 'true';
        const icon = button.querySelector('i');
        const text = button.querySelector('span');
        
        // Disable button during request
        button.disabled = true;
        button.style.opacity = '0.7';
        
        // Optimistic update - change button immediately
        if (isFollowing) {
            // Unfollow
            button.classList.remove('following');
            button.dataset.following = 'false';
            icon.className = 'fas fa-user-plus';
            text.textContent = 'Follow';
        } else {
            // Follow
            button.classList.add('following');
            button.dataset.following = 'true';
            icon.className = 'fas fa-user-check';
            text.textContent = 'Following';
        }
        
        // Send request to API
        fetch(`/users/${userId}/follow`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Follow response:', data);
            if (!data.success) {
                // Revert optimistic update on error
                this.revertButtonState(button, isFollowing);
                console.error('Follow failed:', data.error || 'Unknown error');
                
                // Show error message to user
                this.showErrorMessage(data.error || 'Failed to update follow status');
            } else {
                // Update all follow buttons for this user
                this.updateAllFollowButtonsForUser(userId, data.following);
                
                // Update follower counts if they exist on the page
                this.updateFollowerCounts(data.followers_count);
                this.updateFollowingCounts(data.following_count);
                
                // Log successful action for debugging
                console.log(`Successfully ${data.following ? 'followed' : 'unfollowed'} user ${userId}`);
            }
        })
        .catch(error => {
            console.error('Follow error:', error);
            // Revert optimistic update on error
            this.revertButtonState(button, isFollowing);
        })
        .finally(() => {
            // Re-enable button
            button.disabled = false;
            button.style.opacity = '1';
        });
    }

    revertButtonState(button, wasFollowing) {
        const icon = button.querySelector('i');
        const text = button.querySelector('span');
        
        if (wasFollowing) {
            button.classList.add('following');
            button.dataset.following = 'true';
            icon.className = 'fas fa-user-check';
            text.textContent = 'Following';
        } else {
            button.classList.remove('following');
            button.dataset.following = 'false';
            icon.className = 'fas fa-user-plus';
            text.textContent = 'Follow';
        }
    }

    updateAllFollowButtonsForUser(userId, isFollowing) {
        const followButtons = document.querySelectorAll(`.follow-btn[data-user-id="${userId}"]`);
        followButtons.forEach(button => {
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            if (isFollowing) {
                button.classList.add('following');
                button.dataset.following = 'true';
                icon.className = 'fas fa-user-check';
                text.textContent = 'Following';
            } else {
                button.classList.remove('following');
                button.dataset.following = 'false';
                icon.className = 'fas fa-user-plus';
                text.textContent = 'Follow';
            }
        });
    }

    updateFollowerCounts(count) {
        // Update follower count in profile stats
        const followerStatCards = document.querySelectorAll('.stat-card');
        followerStatCards.forEach(card => {
            const icon = card.querySelector('.stat-icon i');
            const number = card.querySelector('.stat-number');
            const label = card.querySelector('.stat-label');
            
            if (icon && icon.classList.contains('fa-users') && label && label.textContent === 'Followers') {
                number.textContent = count;
            }
        });

        // Update follower count in navigation or other places
        const followerCountElements = document.querySelectorAll('.followers-count');
        followerCountElements.forEach(element => {
            if (element.textContent.includes('followers')) {
                element.textContent = `${count} followers`;
            }
        });
    }

    updateFollowingCounts(count) {
        // Update following count in profile stats
        const followingStatCards = document.querySelectorAll('.stat-card');
        followingStatCards.forEach(card => {
            const icon = card.querySelector('.stat-icon i');
            const number = card.querySelector('.stat-number');
            const label = card.querySelector('.stat-label');
            
            if (icon && icon.classList.contains('fa-user-friends') && label && label.textContent === 'Following') {
                number.textContent = count;
            }
        });
    }

    showErrorMessage(message) {
        // Create a simple error notification
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 14px;
            font-weight: 500;
        `;
        errorDiv.textContent = message;
        
        document.body.appendChild(errorDiv);
        
        // Remove after 3 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 3000);
    }

    /**
     * Verify the current follow state matches the database
     * This can be called to ensure UI consistency
     */
    verifyFollowState(userId) {
        fetch(`/users/${userId}/follow-status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateAllFollowButtonsForUser(userId, data.following);
            }
        })
        .catch(error => {
            console.error('Error verifying follow state:', error);
        });
    }
}

// Initialize follow manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new FollowManager();
}); 