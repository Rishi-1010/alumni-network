function nextStep(currentStep) {
    // Validate current step
    if (!validateStep(currentStep)) return;

    document.getElementById(`step${currentStep}`).style.display = 'none';
    document.getElementById(`step${currentStep + 1}`).style.display = 'block';
}

function prevStep(currentStep) {
    document.getElementById(`step${currentStep}`).style.display = 'none';
    document.getElementById(`step${currentStep - 1}`).style.display = 'block';
}

function validateStep(step) {
    const currentStep = document.getElementById(`step${step}`);
    const inputs = currentStep.querySelectorAll('input[required], select[required]');
    
    for (let input of inputs) {
        if (!input.value) {
            alert('Please fill in all required fields');
            return false;
        }
    }
    
    if (step === 1) {
        // Validate email format
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address');
            return false;
        }
        
        // Validate password strength
        const password = document.getElementById('password').value;
        if (password.length < 8) {
            alert('Password must be at least 8 characters long');
            return false;
        }
    }
    
    return true;
} 