// Function to toggle employment fields
function toggleEmploymentFields() {
    const currentStatus = document.getElementById('current_status').value;
    const companyField = document.getElementById('current_company');
    const positionField = document.getElementById('current_position');

    if (currentStatus === 'employed') {
        companyField.disabled = false;
        positionField.disabled = false;
    } else {
        companyField.disabled = true;
        positionField.disabled = true;
        companyField.value = ''; // Clear the value if not employed
        positionField.value = ''; // Clear the value if not employed
    }
}


document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationForm');
    const steps = document.querySelectorAll('.form-step');
    const progressBar = document.querySelector('.progress-bar');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    let currentStep = 1;

    

    // Validate fields in the current step
    function validateStep(step) {
        const currentStepElement = document.getElementById(`step${step}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]:not([disabled])');
        let isValid = true;
    
        requiredFields.forEach(field => {
            field.classList.remove('error');
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
                console.log(`Field: ${field.id}, Value: "${field.value}"`);
            }
        });
    
        if (!isValid) {
            alert('Please fill in all required fields.');
        }
    
        return isValid;
    }

    function toggleRequiredAttributes(step) {
        steps.forEach((stepElement, index) => {
            const fields = stepElement.querySelectorAll('[required]');
            fields.forEach(field => {
                if (index + 1 === step) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            });
        });
    }

    // Navigate to the next step
    window.nextStep = function (step) {
        if (validateStep(step)) {
            document.getElementById(`step${step}`).style.display = 'none';
            document.getElementById(`step${step + 1}`).style.display = 'block';
            currentStep = step + 1;
            updateProgressBar();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // Navigate to the previous step
    window.prevStep = function (step) {
        document.getElementById(`step${step}`).style.display = 'none';
        document.getElementById(`step${step - 1}`).style.display = 'block';
        currentStep = step - 1;
        updateProgressBar();
    };

    // Update progress bar and step indicators
    function updateProgressBar() {
        const progress = ((currentStep - 1) / (steps.length - 1)) * 100;
        progressBar.style.width = `${progress}%`;

        stepIndicators.forEach((indicator, index) => {
            if (index + 1 < currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (index + 1 === currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        });
    }

    // Initialize form
    if (document.getElementById('step1')) {
        document.getElementById('step1').style.display = 'block';
        for (let i = 2; i <= steps.length; i++) {
            const step = document.getElementById(`step${i}`);
            if (step) step.style.display = 'none';
        }
        updateProgressBar();
    }
});
