document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationForm');
    const steps = document.querySelectorAll('.form-step');
    const progressBar = document.querySelector('.progress-bar');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    let currentStep = 1;
    let projectCounter = 1; // Keep track of project count

    // Ensure there's only one 'Next' button and attach the event listener correctly
    const nextButtons = document.querySelectorAll('.next-btn');
    nextButtons.forEach((button) => {
        button.addEventListener('click', function () {
            nextStep(currentStep);  // Call nextStep with the current step number
        });
    });

    // Validate fields in the current step
    function validateStep(step) {
        const currentStepElement = document.getElementById(`step${step}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]:not([disabled])');
        let isValid = true;
    
        requiredFields.forEach(field => {
            // Clear previous error state
            field.classList.remove('error');
            
            // Check if field is empty or invalid
            if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value.trim())) {
                    isValid = false;
                    field.classList.add('error');
                }
            } else if (field.type === 'tel') {
                const phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(field.value.trim())) {
                    isValid = false;
                    field.classList.add('error');
                }
            } else if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            }
        });
    
        if (!isValid) {
            const invalidFields = currentStepElement.querySelectorAll('.error');
            if (invalidFields.length > 0) {
                const firstInvalid = invalidFields[0];
                const fieldName = firstInvalid.previousElementSibling?.textContent || 'Some fields';
                alert(`${fieldName} require valid input.`);
                firstInvalid.focus();
            }
        }
    
        return isValid;
    }

    // Navigate to the next step
    window.nextStep = function (step) {
        if (validateStep(step)) {
            // Store form data in session storage
            const currentStepElement = document.getElementById(`step${step}`);
            const formFields = currentStepElement.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                if (field.type !== 'password') {
                    sessionStorage.setItem(field.name, field.value);
                }
            });

            // Proceed to next step
            document.getElementById(`step${step}`).style.display = 'none';
            document.getElementById(`step${step + 1}`).style.display = 'block';
            currentStep = step + 1;
            updateProgressBar();

            // Scroll to top of new step
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // Navigate to the previous step
    window.prevStep = function (step) {
        document.getElementById(`step${step}`).style.display = 'none';
        document.getElementById(`step${step - 1}`).style.display = 'block';
        currentStep = step - 1;
        updateProgressBar();

        // Restore form data from session storage
        const prevStepElement = document.getElementById(`step${step - 1}`);
        const formFields = prevStepElement.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            const savedValue = sessionStorage.getItem(field.name);
            if (savedValue && field.type !== 'password') {
                field.value = savedValue;
            }
        });
    };

    // Update progress bar and step indicators
    function updateProgressBar() {
        if (progressBar && steps.length > 0) {
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
    }

    // Initialize form
    if (document.getElementById('step1')) {
        document.getElementById('step1').style.display = 'block';
        for (let i = 2; i <= steps.length; i++) {
            const step = document.getElementById(`step${i}`);
            if (step) step.style.display = 'none';
        }
        updateProgressBar();

        // Restore form data if available
        const formFields = document.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            const savedValue = sessionStorage.getItem(field.name);
            if (savedValue && field.type !== 'password') {
                field.value = savedValue;
            }
        });
    }

    // Form submission handler
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (validateStep(currentStep)) {
                this.submit();
            }
        });
    }

    // Handle "current status" field changes
    document.getElementById('current_status')?.addEventListener('change', function () {
        const companyField = document.getElementById('current_company');
        const positionField = document.getElementById('current_position');

        if (this.value === 'employed') {
            companyField.required = true;
            positionField.required = true;
            companyField.disabled = false;
            positionField.disabled = false;
        } else {
            companyField.required = false;
            positionField.required = false;
            companyField.disabled = true;
            positionField.disabled = true;
            companyField.value = '';
            positionField.value = '';
        }
    });

    // Final submission button
    document.querySelector('.submit-btn')?.addEventListener('click', function (e) {
        e.preventDefault();
        if (validateStep(currentStep)) {
            document.getElementById('registrationForm').submit();
        }
    });

    // Add project functionality
    document.getElementById('add-project')?.addEventListener('click', function () {
        const projectsContainer = document.getElementById('projects-container');

        // Create new project entry
        const newProject = document.createElement('div');
        newProject.className = 'project-entry';
        newProject.innerHTML = `
            <div class="project-header">
                <h4>Project ${projectCounter + 1}</h4>
                <button type="button" class="remove-project" onclick="removeProject(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-group">
                <label for="project_title_${projectCounter}">Project Title*</label>
                <input type="text" 
                       id="project_title_${projectCounter}" 
                       name="projects[${projectCounter}][title]" 
                       required>
            </div>
            <div class="form-group">
                <label for="project_description_${projectCounter}">Description*</label>
                <textarea id="project_description_${projectCounter}" 
                          name="projects[${projectCounter}][description]" 
                          required></textarea>
            </div>
            <div class="form-group">
                <label for="technologies_${projectCounter}">Technologies Used*</label>
                <input type="text" 
                       id="technologies_${projectCounter}" 
                       name="projects[${projectCounter}][technologies]" 
                       placeholder="e.g., PHP, MySQL, JavaScript" 
                       required>
            </div>
            <div class="form-group date-range">
                <div>
                    <label for="start_date_${projectCounter}">Start Date*</label>
                    <input type="date" 
                           id="start_date_${projectCounter}" 
                           name="projects[${projectCounter}][start_date]" 
                           required>
                </div>
                <div>
                    <label for="end_date_${projectCounter}">End Date</label>
                    <input type="date" 
                           id="end_date_${projectCounter}" 
                           name="projects[${projectCounter}][end_date]">
                </div>
            </div>
        `;

        // Add animation class
        newProject.classList.add('project-entry-new');

        // Append new project
        projectsContainer.appendChild(newProject);

        // Increment counter
        projectCounter++;

        // Scroll to new project
        newProject.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Remove project functionality
    window.removeProject = function (button) {
        const projectEntry = button.closest('.project-entry');

        // Add removal animation
        projectEntry.classList.add('project-entry-remove');

        // Remove after animation
        setTimeout(() => {
            projectEntry.remove();
        }, 300);
    };
});
