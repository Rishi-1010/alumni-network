class CustomModal {
    constructor() {
        console.log('Modal constructor called');
        this.modal = document.getElementById('customModal');
        this.messageElement = document.getElementById('modalMessage');
        this.confirmBtn = document.getElementById('modalConfirm');
        this.cancelBtn = document.getElementById('modalCancel');
        this.closeBtn = this.modal.querySelector('.close-modal');
        
        console.log('Modal elements:', {
            modal: this.modal,
            messageElement: this.messageElement,
            confirmBtn: this.confirmBtn,
            cancelBtn: this.cancelBtn,
            closeBtn: this.closeBtn
        });
        
        this.setupEventListeners();
    }

    setupEventListeners() {
        console.log('Setting up event listeners');
        
        // Close button
        this.closeBtn.addEventListener('click', () => {
            console.log('Close button clicked');
            this.hide();
        });
        
        // Cancel button
        this.cancelBtn.addEventListener('click', () => {
            console.log('Cancel button clicked');
            this.hide();
        });
        
        // Click outside modal
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                console.log('Clicked outside modal');
                this.hide();
            }
        });
    }

    show(message, onConfirm) {
        console.log('Showing modal with message:', message);
        this.messageElement.textContent = message;
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Remove any existing click handlers from confirm button
        console.log('Replacing confirm button');
        const newConfirmBtn = this.confirmBtn.cloneNode(true);
        this.confirmBtn.parentNode.replaceChild(newConfirmBtn, this.confirmBtn);
        this.confirmBtn = newConfirmBtn;
        
        // Add new click handler
        console.log('Adding new click handler to confirm button');
        this.confirmBtn.onclick = () => {
            console.log('Confirm button clicked');
            this.hide();
            if (typeof onConfirm === 'function') {
                console.log('Executing confirm callback');
                onConfirm();
            } else {
                console.log('No confirm callback provided');
            }
        };
    }

    hide() {
        console.log('Hiding modal');
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Create global modal instance
let customModal;

// Initialize modal when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Initializing modal');
    customModal = new CustomModal();
});

// Global confirm function
window.customConfirm = function(message, onConfirm) {
    console.log('customConfirm called with message:', message);
    if (customModal) {
        customModal.show(message, onConfirm);
    } else {
        console.error('Modal not initialized');
    }
};

// Alumni verification function
function verifyAlumni(userId) {
    console.log('verifyAlumni called for user:', userId);
    customConfirm('Are you sure you want to verify this alumni?', () => {
        console.log('Verification confirmed, making AJAX call');
        $.ajax({
            url: 'verify_alumni.php',
            method: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
                console.log('Verification response:', response);
                if (response.error) {
                    showAlert('error', response.error);
                } else {
                    showAlert('success', 'Alumni verified successfully');
                    updateSearchResults(currentPage);
                }
            },
            error: function(xhr, status, error) {
                console.error('Verification error:', error);
                showAlert('error', 'An error occurred while verifying the alumni');
            }
        });
    });
}
