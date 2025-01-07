document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    
    loginForm.addEventListener('submit', function(e) {
        // Get form inputs
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        let isValid = true;
        
        // Clear previous error messages
        clearErrors();
        
        // Username validation
        if (username === '') {
            showError('username', 'Username is required');
            isValid = false;
        }
        
        // Password validation
        if (password === '') {
            showError('password', 'Password is required');
            isValid = false;
        }
        
        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Show error message under input field
    function showError(inputId, message) {
        const input = document.getElementById(inputId);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#E74C3C';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        input.classList.add('error');
        input.parentNode.appendChild(errorDiv);
    }
    
    // Clear all error messages
    function clearErrors() {
        const errorMessages = document.querySelectorAll('.error-message');
        const inputs = document.querySelectorAll('.error');
        
        errorMessages.forEach(error => error.remove());
        inputs.forEach(input => input.classList.remove('error'));
    }
    
    // Add password visibility toggle
    const togglePassword = document.createElement('i');
    togglePassword.className = 'fas fa-eye password-toggle';
    togglePassword.style.position = 'absolute';
    togglePassword.style.right = '10px';
    togglePassword.style.top = '50%';
    togglePassword.style.transform = 'translateY(-50%)';
    togglePassword.style.cursor = 'pointer';
    togglePassword.style.color = '#666';
    
    const passwordInput = document.getElementById('password');
    passwordInput.parentElement.style.position = 'relative';
    passwordInput.parentElement.appendChild(togglePassword);
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.className = `fas fa-eye${type === 'password' ? '' : '-slash'} password-toggle`;
    });
    
    // Add input focus effects
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Add initial focused class if input has value
        if (input.value !== '') {
            input.parentElement.classList.add('focused');
        }
    });
}); 