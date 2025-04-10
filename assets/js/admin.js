document.addEventListener('DOMContentLoaded', function() {
    

    // Verify button confirmation
    const verifyButtons = document.querySelectorAll('a.btn-primary');
    verifyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to verify this alumni?')) {
                e.preventDefault();
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Status badge styling
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        const status = badge.textContent.toLowerCase().trim();
        let className = '';
        
        switch(status) {
            case 'verified':
                className = 'bg-success';
                break;
            case 'pending':
                className = 'bg-warning';
                break;
            case 'rejected':
                className = 'bg-danger';
                break;
            default:
                className = 'bg-secondary';
        }
        
        badge.classList.add(className);
    });

    // Function to show notifications
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('fade-out');
            notification.addEventListener('animationend', () => notification.remove());
        }, 3000);
    }
});