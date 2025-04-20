class CustomModal {
    constructor() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initialize());
        } else {
            this.initialize();
        }
    }

    initialize() {
        this.modal = document.getElementById('customModal');
        if (!this.modal) {
            console.error('Modal element not found');
            return;
        }
        
        this.messageElement = document.getElementById('modalMessage');
        this.confirmBtn = document.getElementById('modalConfirm');
        this.cancelBtn = document.getElementById('modalCancel');
        this.closeBtn = this.modal.querySelector('.close-modal');
        
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.hide());
        }
        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.hide());
        }
        
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.hide();
            }
        });
    }

    show(message, onConfirm) {
        if (!this.modal || !this.messageElement) {
            console.error('Modal not properly initialized');
            return;
        }
        
        this.messageElement.textContent = message;
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        if (this.confirmBtn) {
            this.confirmBtn.replaceWith(this.confirmBtn.cloneNode(true));
            this.confirmBtn = document.getElementById('modalConfirm');
            
            this.confirmBtn.addEventListener('click', () => {
                this.hide();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        }
    }

    hide() {
        if (!this.modal) return;
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Create global modal instance
let customModal;
document.addEventListener('DOMContentLoaded', () => {
    customModal = new CustomModal();
});

// Replace default confirm with custom modal
window.customConfirm = function(message, onConfirm) {
    if (!customModal) {
        console.error('Modal not initialized');
        return false;
    }
    customModal.show(message, onConfirm);
    return false;
};

function verifyAlumni(userId) {
    if (!customModal) {
        console.error('Modal not initialized');
        return;
    }
    
    customConfirm('Are you sure you want to verify this alumni?', () => {
        $.ajax({
            url: 'verify_alumni.php',
            method: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
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
