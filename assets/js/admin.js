document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const menuToggle = document.createElement('button');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.querySelector('.main-content header').prepend(menuToggle);

    menuToggle.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar') && !e.target.closest('.menu-toggle')) {
            document.querySelector('.sidebar').classList.remove('active');
        }
    });

    // Status badge colors
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        const status = badge.textContent.trim().toLowerCase();
        badge.classList.add(status);
    });

    // Table row hover effect
    const tableRows = document.querySelectorAll('table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.classList.add('hover');
        });
        row.addEventListener('mouseleave', function() {
            this.classList.remove('hover');
        });
    });

    // Verify button confirmation
    const verifyButtons = document.querySelectorAll('a.btn-primary');
    verifyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to verify this alumni?')) {
                e.preventDefault();
            }
        });
    });

    // Stats counter animation
    const stats = document.querySelectorAll('.stat-info p');
    stats.forEach(stat => {
        const target = parseInt(stat.textContent);
        let current = 0;
        const increment = target / 30; // Adjust speed here
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            stat.textContent = Math.round(current);
        }, 30);
    });

    // Search functionality
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search alumni...';
    searchInput.className = 'search-input';
    document.querySelector('.section-header').appendChild(searchInput);

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Example usage of notification
    window.showAdminNotification = showNotification;

    // Delete functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-alumni')) {
            const button = e.target.closest('.delete-alumni');
            const userId = button.dataset.id;
            
            if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                // Send delete request
                fetch('delete_alumni.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Remove the row from the table
                        button.closest('tr').remove();
                        
                        // Update the total count
                        const totalElement = document.querySelector('.stat-info p');
                        if (totalElement) {
                            let currentTotal = parseInt(totalElement.textContent);
                            totalElement.textContent = currentTotal - 1;
                        }
                        
                        // Show success notification
                        showAdminNotification('Record deleted successfully', 'success');
                    } else {
                        // Show error notification
                        showAdminNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showAdminNotification('Error deleting record', 'error');
                    console.error('Error:', error);
                });
            }
        }
    });
});



// Add styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = styles;
document.head.appendChild(styleSheet); 