document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('enrollmentSearch');
    const suggestionBox = document.getElementById('suggestionBox');
    let currentFocus = -1;
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Fetch suggestions
    const fetchSuggestions = debounce(async (searchTerm) => {
        if (searchTerm.length === 0) {
            suggestionBox.style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`search_enrollment.php?term=${searchTerm}`);
            const data = await response.json();
            
            if (data.length > 0) {
                // Filter suggestions to ensure they start with the search term
                const filteredData = data.filter(item => {
                    const enrollmentNumber = item.enrollment_number;
                    return enrollmentNumber.startsWith(searchTerm);
                });
                if (filteredData.length > 0) {
                    displaySuggestions(filteredData);
                } else {
                    suggestionBox.style.display = 'none';
                }
            } else {
                suggestionBox.style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    }, 300);
    
    // Display suggestions
    function displaySuggestions(suggestions) {
        suggestionBox.innerHTML = '';
        suggestions.forEach(item => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `
                <span class="enrollment-number">${item.enrollment_number}</span>
                <span class="student-name">${item.fullname}</span>
            `;
            suggestionBox.appendChild(div);
        });
        suggestionBox.style.display = 'block';
    }
    
    searchInput.addEventListener('input', (e) => {
        fetchSuggestions(e.target.value);
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
            suggestionBox.style.display = 'none';
        }
    });
    
    // Keyboard navigation
    searchInput.addEventListener('keydown', (e) => {
        const items = suggestionBox.getElementsByClassName('suggestion-item');
        
        if (e.key === 'ArrowDown') {
            currentFocus++;
            addActive(items);
        } else if (e.key === 'ArrowUp') {
            currentFocus--;
            addActive(items);
        } else if (e.key === 'Enter' && currentFocus > -1) {
            if (items[currentFocus]) {
                items[currentFocus].click();
            }
        }
    });
    
    function addActive(items) {
        if (!items) return;
        
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add('active');
    }
    
    function removeActive(items) {
        for (let item of items) {
            item.classList.remove('active');
        }
    }
});