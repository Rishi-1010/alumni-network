document.addEventListener('DOMContentLoaded', function() {
    // Delete alumni functionality
    const deleteButtons = document.querySelectorAll('.delete-alumni');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this alumni?')) {
                const userId = this.dataset.id;
                
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
                        this.closest('tr').remove();
                        alert('Alumni deleted successfully');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the alumni');
                });
            }
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
});