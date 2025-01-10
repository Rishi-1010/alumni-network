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

    // Navigate to the next step
    window.nextStep = function (step) {
        console.log(`Attempting to move from Step ${step} to Step ${step + 1}`);
        // Only proceed to the next step if the current step is valid
        if (validateStep(step)) {
            document.getElementById(`step${step}`).style.display = 'none';
            document.getElementById(`step${step + 1}`).style.display = 'block';
            currentStep = step + 1;
            updateProgressBar();
        } else {
            console.log(`Validation failed for Step ${step}`);
        }
    };
    

    // Navigate to the previous step
    window.prevStep = function (step) {
        console.log(`Going back from Step ${step} to Step ${step - 1}`);
        document.getElementById(`step${step}`).style.display = 'none';
        document.getElementById(`step${step - 1}`).style.display = 'block';
        currentStep = step - 1;
        updateProgressBar();
    };

    // Validate fields in the current step
    function validateStep(step) {
        const currentStepElement = document.getElementById(`step${step}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
    
        requiredFields.forEach(field => {
            console.log(`Field: ${field.id}, Value: "${field.value}"`);
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });
    
        if (!isValid) {
            alert('Please fill in all required fields.');
        }
    
        return isValid;
    }

    // Update progress bar and step indicators
    function updateProgressBar() {
        if (progressBar && steps.length > 0) {
            const progress = ((currentStep - 1) / (steps.length - 1)) * 100;
            progressBar.style.width = `${progress}%`;

            // Update step indicators
            stepIndicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index + 1 <= currentStep);
            });
        }
    }

    // Initialize form
    if (document.getElementById('step1')) {
        document.getElementById('step1').style.display = 'block';

        // Hide other steps
        for (let i = 2; i <= steps.length; i++) {
            const step = document.getElementById(`step${i}`);
            if (step) step.style.display = 'none';
        }
        updateProgressBar();
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
