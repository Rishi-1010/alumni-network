console.log('register.js loaded');

// Language, Tools, and Technologies Lists
const languageSpecializations = [
    "Python",
    "JavaScript",
    "Java",
    "C++",
    "C#",
    "PHP",
    "Ruby",
    "Swift",
    "Kotlin",
    "Go",
    "TypeScript",
    "R",
    "MATLAB",
    "Scala",
    "Rust",
    "Dart",
    "HTML/CSS",
    "SQL",
    "Shell Scripting",
    "Perl"
];

const tools = [
    "Git",
    "Docker",
    "Jenkins",
    "Visual Studio Code",
    "IntelliJ IDEA",
    "Eclipse",
    "PyCharm",
    "Sublime Text",
    "Postman",
    "JIRA",
    "Kubernetes",
    "Maven",
    "npm",
    "Gradle",
    "Webpack",
    "Android Studio",
    "Xcode",
    "GitHub Desktop",
    "Azure DevOps",
    "GitLab CI/CD"
];

const technologies = [
    // Frontend
    "React.js",
    "Angular",
    "Vue.js",
    "Next.js",
    "Bootstrap",
    "Tailwind CSS",
    "jQuery",
    
    // Backend
    "Node.js",
    "Django",
    "Flask",
    "Laravel",
    "Spring Boot",
    "Express.js",
    "ASP.NET Core",
    
    // Databases
    "MySQL",
    "PostgreSQL",
    "MongoDB",
    "Redis",
    "SQLite",
    "Oracle",
    "Microsoft SQL Server",
    
    // Cloud Platforms
    "AWS",
    "Google Cloud",
    "Azure",
    "Heroku",
    "DigitalOcean",
    
    // Mobile
    "React Native",
    "Flutter",
    "iOS Development",
    "Android Development",
    
    // Other
    "GraphQL",
    "REST APIs",
    "WebSockets",
    "Machine Learning",
    "Artificial Intelligence",
    "Blockchain"
];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Initialize all form elements and validation
    initializePhoneValidation();
    initializeEmailValidation();
    initializeDOBValidation();
    initializePasswordValidation();
    initializeStepIndicators();
    initializeSelect2Dropdowns();
    initializeProjectHandling();
    initializeCertificateHandling();
    initializeFormNavigation();
    initializeEnrollmentFormat();

    // Add event listeners for project radio buttons
    const projectRadios = document.querySelectorAll('input[name="hasProjects"]');
    projectRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Project radio changed:', this.value);
            
            // Handle project fields' required attribute
            const projectFields = document.querySelectorAll('#projects-container input[required], #projects-container textarea[required]');
            if (this.value === 'no') {
                projectFields.forEach(field => {
                    field.removeAttribute('required');
                });
                // Clear project fields
                const projectsContainer = document.getElementById('projects-container');
                if (projectsContainer) {
                    projectsContainer.innerHTML = '';
                }
            }
            
            updateStepIndicators(4);
        });
    });
});

// Phone Validation
function initializePhoneValidation() {
    const phoneInput = document.getElementById('phone');
    if (!phoneInput) return;

    const phoneContainer = phoneInput.parentElement;
    const phoneIcon = document.createElement('span');
    phoneIcon.className = 'validation-icon';
    phoneContainer.appendChild(phoneIcon);

    const phoneError = document.createElement('div');
    phoneError.className = 'error-message';
    phoneContainer.appendChild(phoneError);

    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        
        if (value.length > 10) {
            value = value.slice(0, 10);
            this.value = value;
        }
        
        updatePhoneValidationState(value, phoneContainer, phoneIcon, phoneError);
    });

    phoneInput.addEventListener('keypress', function(e) {
        if (!/[\d]/.test(e.key)) {
            e.preventDefault();
        }
    });

    phoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const numericValue = pastedText.replace(/\D/g, '').slice(0, 10);
        this.value = numericValue;
        this.dispatchEvent(new Event('input'));
    });
}

function updatePhoneValidationState(value, container, icon, error) {
    if (!value) {
        container.classList.remove('valid', 'error');
        icon.className = 'validation-icon';
        error.style.display = 'none';
    } else if (value.length === 10) {
        container.classList.add('valid');
        container.classList.remove('error');
        icon.className = 'validation-icon valid';
        icon.innerHTML = '✓';
        error.style.display = 'none';
    } else {
        container.classList.add('error');
        container.classList.remove('valid');
        icon.className = 'validation-icon invalid';
        icon.innerHTML = '✕';
        error.textContent = 'Please enter exactly 10 digits';
        error.style.display = 'block';
    }
}

// Email Validation
function initializeEmailValidation() {
    const emailInput = document.getElementById('email');
    if (!emailInput) return;

    const emailContainer = emailInput.parentElement;
    const emailIcon = document.createElement('span');
    emailIcon.className = 'validation-icon';
    emailContainer.appendChild(emailIcon);

    const emailError = document.createElement('div');
    emailError.className = 'error-message';
    emailError.textContent = 'Please enter a valid email address';
    emailContainer.appendChild(emailError);

    emailInput.addEventListener('input', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        updateEmailValidationState(email, emailRegex, emailContainer, emailIcon, emailError);
    });
}

function updateEmailValidationState(email, regex, container, icon, error) {
    if (email === '') {
        container.classList.remove('valid', 'error');
        icon.className = 'validation-icon';
        error.style.display = 'none';
    } else if (regex.test(email)) {
        container.classList.add('valid');
        container.classList.remove('error');
        icon.className = 'validation-icon valid';
        icon.innerHTML = '✓';
        error.style.display = 'none';
    } else {
        container.classList.add('error');
        container.classList.remove('valid');
        icon.className = 'validation-icon invalid';
        icon.innerHTML = '✕';
        error.style.display = 'block';
    }
}

// DOB Validation
function initializeDOBValidation() {
    const dobInput = document.getElementById('dob');
    if (!dobInput) return;

    const dobContainer = dobInput.parentElement;
    const dobIcon = document.createElement('span');
    dobIcon.className = 'validation-icon';
    dobContainer.appendChild(dobIcon);

    const dobError = document.createElement('div');
    dobError.className = 'error-message';
    dobContainer.appendChild(dobError);

    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    dobInput.setAttribute('max', today);

    // Add event listeners
    ['input', 'change', 'blur', 'keyup'].forEach(event => {
        dobInput.addEventListener(event, () => validateDOB(dobInput, dobContainer, dobIcon, dobError));
    });

    dobInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            validateDOB(dobInput, dobContainer, dobIcon, dobError);
        }
    });

    // Initial validation
    validateDOB(dobInput, dobContainer, dobIcon, dobError);
}

function validateDOB(input, container, icon, error) {
    const dob = new Date(input.value);
    const today = new Date();
    
    container.classList.remove('valid', 'error');
    icon.className = 'validation-icon';
    error.style.display = 'none';

    if (!input.value) return;

    if (isNaN(dob.getTime())) {
        showDOBError(container, icon, error, 'Please enter a valid date');
        return;
    }

    if (dob > today) {
        showDOBError(container, icon, error, 'Date of birth cannot be in the future');
        return;
    }

    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    const minAge = 16;
    const maxAge = 100;

    if (age < minAge) {
        showDOBError(container, icon, error, `You must be at least ${minAge} years old`);
    } else if (age > maxAge) {
        showDOBError(container, icon, error, `Age cannot be more than ${maxAge} years`);
                 } else {
        container.classList.add('valid');
        icon.className = 'validation-icon valid';
        icon.innerHTML = '✓';
    }
}

function showDOBError(container, icon, error, message) {
    container.classList.add('error');
    icon.className = 'validation-icon invalid';
    icon.innerHTML = '✕';
    error.textContent = message;
    error.style.display = 'block';
}

// Password Validation
function initializePasswordValidation() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;

    const passwordContainer = passwordInput.parentElement;
    const passwordIcon = document.createElement('span');
    passwordIcon.className = 'validation-icon';
    passwordContainer.appendChild(passwordIcon);

    // Create password toggle button
    const passwordToggle = document.createElement('button');
    passwordToggle.type = 'button';
    passwordToggle.className = 'password-toggle';
    passwordToggle.innerHTML = '👁️';
    passwordContainer.appendChild(passwordToggle);

    // Create password strength meter
    const strengthMeter = document.createElement('div');
    strengthMeter.className = 'password-strength-meter';
    const meter = document.createElement('div');
    meter.className = 'meter';
    strengthMeter.appendChild(meter);
    passwordContainer.appendChild(strengthMeter);

    // Create password requirements list
    const requirements = document.createElement('div');
    requirements.className = 'password-requirements';
    requirements.innerHTML = `
        <ul>
            <li data-requirement="length">At least 8 characters long</li>
            <li data-requirement="uppercase">Contains uppercase letter</li>
            <li data-requirement="lowercase">Contains lowercase letter</li>
            <li data-requirement="number">Contains number</li>
            <li data-requirement="special">Contains special character</li>
        </ul>
    `;
    passwordContainer.appendChild(requirements);

    // Add event listeners
    passwordInput.addEventListener('input', () => validatePassword(passwordInput, passwordContainer, passwordIcon, meter));
    passwordToggle.addEventListener('click', () => togglePasswordVisibility(passwordInput, passwordToggle));
}

function validatePassword(input, container, icon, meter) {
    const password = input.value;
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    Object.keys(requirements).forEach(req => {
        const element = document.querySelector(`[data-requirement="${req}"]`);
        if (requirements[req]) {
            element.classList.add('valid');
        } else {
            element.classList.remove('valid');
        }
    });

    const strength = Object.values(requirements).filter(Boolean).length;
    meter.className = 'meter';
    
    if (password.length === 0) {
        meter.style.width = '0';
        container.classList.remove('valid', 'error');
        icon.className = 'validation-icon';
    } else if (strength <= 2) {
        meter.classList.add('weak');
        container.classList.add('error');
        container.classList.remove('valid');
        icon.className = 'validation-icon invalid';
        icon.innerHTML = '✕';
    } else if (strength <= 4) {
        meter.classList.add('medium');
        container.classList.add('error');
        container.classList.remove('valid');
        icon.className = 'validation-icon invalid';
        icon.innerHTML = '✕';
        } else {
        meter.classList.add('strong');
        container.classList.add('valid');
        container.classList.remove('error');
        icon.className = 'validation-icon valid';
        icon.innerHTML = '✓';
    }
}

function togglePasswordVisibility(input, toggle) {
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    toggle.innerHTML = type === 'password' ? '👁️' : '👁️‍🗨️';
}

// Step Indicators
function initializeStepIndicators() {
    updateStepIndicators(1);
}

function updateStepIndicators(currentStep) {
    console.log('Updating step indicators for step:', currentStep);
    
    const steps = document.querySelectorAll('.step');
    const progressBar = document.querySelector('.step-progress');
    const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
    const skipProjects = hasProjectsRadio && hasProjectsRadio.value === 'no';
    
    console.log('Skip projects:', skipProjects);
    
    // Hide projects step if "No" was selected
    const projectStep = document.querySelector('.step.project-dependent');
    if (projectStep) {
        projectStep.style.display = skipProjects ? 'none' : '';
        console.log('Project step visibility:', skipProjects ? 'hidden' : 'visible');
    }

    // Update step numbers
    if (skipProjects) {
        document.querySelector('.skills-step-number').textContent = '5';
        document.querySelector('.goals-step-number').textContent = '6';
        console.log('Updated step numbers for skipping projects');
        } else {
        document.querySelector('.skills-step-number').textContent = '6';
        document.querySelector('.goals-step-number').textContent = '7';
        console.log('Updated step numbers for including projects');
    }

    // Update active and completed states
    steps.forEach((step, index) => {
        const stepNumber = index + 1;
        step.classList.remove('active', 'completed');

        // Skip project step in calculations if "No" was selected
        if (skipProjects && stepNumber === 5) {
            step.style.display = 'none';
            return;
        }

        // Adjust step number for display
        let adjustedStepNumber = skipProjects && stepNumber > 5 ? stepNumber - 1 : stepNumber;
        let adjustedCurrentStep = skipProjects && currentStep > 5 ? currentStep - 1 : currentStep;

        if (stepNumber < currentStep) {
            step.classList.add('completed');
            console.log(`Step ${stepNumber} marked as completed`);
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
            console.log(`Step ${stepNumber} marked as active`);
        }
    });
    
    // Update progress bar
    if (progressBar) {
        const totalSteps = skipProjects ? 6 : 7; // Adjust total steps based on projects selection
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = `${progress}%`;
        console.log(`Progress bar updated to ${progress}%`);
    }
}

// Form Navigation and Step Transitions
function initializeFormNavigation() {
    // Initialize step indicators
    updateStepIndicators(1);
    
    // Add AJAX form submission
    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
            const skipProjects = hasProjectsRadio && hasProjectsRadio.value === 'no';

            // Remove project fields from form data if projects are skipped
            if (skipProjects) {
                const projectFields = document.querySelectorAll('#projects-container input, #projects-container textarea');
                projectFields.forEach(field => {
                    field.disabled = true;
                });
            }

            // Show loading state
            showLoadingState();
            
            // Create FormData object
            const formData = new FormData(this);
            
            // Re-enable fields to not affect future submissions
            if (skipProjects) {
                const projectFields = document.querySelectorAll('#projects-container input, #projects-container textarea');
                projectFields.forEach(field => {
                    field.disabled = false;
                });
            }

            // Send AJAX request
            fetch('process_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingState();
                if (data.status === 'error') {
                    showFormError(data.message);
                } else if (data.status === 'success') {
                    // Show success modal
                    const modal = document.getElementById('successModal');
                    const countdownElement = document.getElementById('countdown');
                    let countdown = 5;

                    // Display the modal
                    modal.style.display = 'flex';

                    // Start countdown
                    const countdownInterval = setInterval(() => {
                        countdown--;
                        countdownElement.textContent = countdown;

                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = data.redirect;
                        }
                    }, 1000);
                }
            })
            .catch(error => {
                hideLoadingState();
                showFormError('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
    }
    
    // Add event listeners for all step navigation buttons
    for (let i = 1; i <= 7; i++) {
        // Next buttons
        const nextBtn = document.getElementById(`step${i}Next`);
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (validateStep(i)) {
                    const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
                    const skipProjects = hasProjectsRadio && hasProjectsRadio.value === 'no';
                    
                    if (i === 4 && skipProjects) {
                        moveToStep(4, 6); // Skip to Skills step
                    } else {
                        moveToStep(i, i + 1);
                    }
                }
            });
        }
        
        // Previous buttons
        const prevBtn = document.getElementById(`step${i}Prev`);
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
                const skipProjects = hasProjectsRadio && hasProjectsRadio.value === 'no';
                
                if (i === 6 && skipProjects) {
                    moveToStep(6, 4); // Go back to Projects question
                } else if (skipProjects && i > 5) {
                    moveToStep(i, i - 2); // Skip Projects step when going back
                } else {
                    moveToStep(i, i - 1);
                }
            });
        }
    }

    // Special handling for step 4 (Project question)
    const step4Next = document.getElementById('step4Next');
    if (step4Next) {
        step4Next.addEventListener('click', () => {
            const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
            if (!hasProjectsRadio) {
                showError('Please select whether you have projects or not');
                return;
            }
            
            if (validateStep(4)) {
                if (hasProjectsRadio.value === 'no') {
                    moveToStep(4, 6); // Skip directly to Skills step
                } else {
                    moveToStep(4, 5); // Go to Projects step
                }
            }
        });
    }

    // Special handling for step 5 (Projects)
    const step5Next = document.getElementById('step5Next');
    if (step5Next) {
        step5Next.addEventListener('click', () => {
            if (validateStep(5)) {
                moveToStep(5, 6);
            }
        });
    }

    // Special handling for step 6 (Skills)
    const step6Next = document.getElementById('step6Next');
    if (step6Next) {
        step6Next.addEventListener('click', () => {
            if (validateStep(6)) {
                moveToStep(6, 7);
            }
        });
    }
}

function moveToStep(currentStep, nextStep) {
    console.log(`Moving from step ${currentStep} to step ${nextStep}`);
    
    const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
    const skipProjects = hasProjectsRadio && hasProjectsRadio.value === 'no';
    
    console.log('Skip projects:', skipProjects);

    // Handle project fields' required attribute
    if (skipProjects) {
        const projectFields = document.querySelectorAll('#projects-container input[required], #projects-container textarea[required]');
        projectFields.forEach(field => {
            field.removeAttribute('required');
        });
    }

    // Hide all steps first
    const allSteps = document.querySelectorAll('.form-step');
    allSteps.forEach(step => {
        step.style.display = 'none';
        step.classList.remove('active');
    });

    // Show next step
    const nextStepElement = document.getElementById(`step${nextStep}`);
    if (nextStepElement) {
        nextStepElement.style.display = 'block';
        nextStepElement.classList.add('active');
        console.log(`Showing step ${nextStep}`);
        
        // Update step indicators with the actual step number
        updateStepIndicators(nextStep);
        
        // Reinitialize Select2 when moving to skills step
        if (nextStep === 6) {
            console.log('Reinitializing Select2 dropdowns');
            try {
                $('select[name="skills[language][]"], select[name="skills[tools][]"], select[name="skills[technologies][]"]').select2('destroy');
                initializeSelect2Dropdowns();
            } catch (error) {
                console.error('Error reinitializing Select2:', error);
            }
        }
        
        // Scroll to top of form
        window.scrollTo({
            top: nextStepElement.offsetTop - 100,
            behavior: 'smooth'
        });
    }
}

function validateStep(step) {
    console.log('Validating step:', step);
    
    const stepElement = document.getElementById(`step${step}`);
    if (!stepElement) {
        console.error(`Step element ${step} not found`);
        return false;
    }

    // Skip validation for project step if user selected "no" for projects
    if (step === 5) {
        const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
        if (hasProjectsRadio && hasProjectsRadio.value === 'no') {
            console.log('Skipping validation for project step');
            return true;
        }
    }

    let isValid = true;
    const requiredFields = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredFields.forEach(field => {
        if (field.offsetParent !== null) { // Only validate visible fields
            let value = field.value.trim();
            
            if (!value) {
                    isValid = false;
                    field.classList.add('error');
                showFieldError(field, 'This field is required');
                console.error(`Validation failed for field: ${field.name || field.id}`);
                } else {
                    field.classList.remove('error');
                removeFieldError(field);
                console.log(`Field validated successfully: ${field.name || field.id}`);
            }
        }
    });

    // Validate Select2 fields if we're on the status step and freelancer is selected
    if (step === 3 && document.getElementById('current_status').value === 'freelancer') {
        console.log('Validating freelancer fields');
        const platformsValid = validateSelect2Field($('#platforms'), 'Please select at least one platform');
        const expertiseValid = validateSelect2Field($('#expertise_areas'), 'Please select at least one area of expertise');
        isValid = isValid && platformsValid && expertiseValid;
    }

        if (!isValid) {
        console.error(`Step ${step} validation failed`);
        showError('Please fill in all required fields correctly');
        } else {
        console.log(`Step ${step} validation successful`);
        }

        return isValid;
    }

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.innerHTML = `
        <div class="alert-content">
            <span>${message}</span>
            <button class="close-btn">&times;</button>
        </div>
    `;
    
    const formContainer = document.querySelector('.form-container');
    formContainer.insertBefore(errorDiv, formContainer.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
    
    // Add close button functionality
    const closeBtn = errorDiv.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            errorDiv.remove();
        });
    }
}

function showFieldError(field, message) {
    let errorDiv = field.parentElement.querySelector('.field-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
}

function removeFieldError(field) {
    const errorDiv = field.parentElement.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Select2 Initialization
function initializeSelect2Dropdowns() {
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
        console.error('jQuery or Select2 is not loaded');
        return;
    }

    // Initialize language specialization dropdown
    $('select[name="skills[language][]"]').select2({
        placeholder: "Search and select programming languages...",
        tags: true,
        data: languageSpecializations.map(lang => ({ id: lang, text: lang })),
        maximumSelectionLength: 5,
        theme: "classic",
        width: '100%',
        closeOnSelect: false,
        allowClear: true
    });

    // Initialize tools dropdown
    $('select[name="skills[tools][]"]').select2({
        placeholder: "Select or type to search tools",
        tags: true,
        data: tools.map(tool => ({ id: tool, text: tool })),
        maximumSelectionLength: 8,
        theme: "classic",
        width: '100%',
        closeOnSelect: false,
        allowClear: true
    });

    // Initialize technologies dropdown
    $('select[name="skills[technologies][]"]').select2({
        placeholder: "Select or type to search technologies",
        tags: true,
        data: technologies.map(tech => ({ id: tech, text: tech })),
        maximumSelectionLength: 10,
        theme: "classic",
        width: '100%',
        closeOnSelect: false,
        allowClear: true
    });

    // Add validation for select2 multiple selects
    $('select[name="skills[language][]"], select[name="skills[tools][]"], select[name="skills[technologies][]"]').on('change', function() {
        const selected = $(this).val();
        if (!selected || selected.length === 0) {
            $(this).next('.select2-container').addClass('error');
    } else {
            $(this).next('.select2-container').removeClass('error');
        }
    });
}

function handleOtherOptions() {
    // Language Specialization
    $('select[name="skills[language][]"]').on('change', function() {
        const selectedOptions = $(this).val();
        toggleOtherField(selectedOptions, 'other_language_container', 'other_language');
    });

    // Tools
    $('select[name="skills[tools][]"]').on('change', function() {
        const selectedOptions = $(this).val();
        toggleOtherField(selectedOptions, 'other_tools_container', 'other_tools');
    });

    // Technologies
    $('select[name="skills[technologies][]"]').on('change', function() {
        const selectedOptions = $(this).val();
        toggleOtherField(selectedOptions, 'other_technologies_container', 'other_technologies');
    });

    // Add "Other" option to all specialization dropdowns
    ['language', 'tools', 'technologies'].forEach(type => {
        addOtherOption(`select[name="skills[${type}][]"]`);
    });

    // Form submission handling
    $('#registrationForm').on('submit', function(e) {
        if (!validateOtherFields()) {
            e.preventDefault();
        }
    });
}

function toggleOtherField(selectedOptions, containerId, inputId) {
    const container = $(`#${containerId}`);
    const input = $(`#${inputId}`);
    
    if (selectedOptions && selectedOptions.includes('other')) {
        container.show();
        input.prop('required', true);
            } else {
        container.hide();
        input.prop('required', false);
    }
}

function addOtherOption(selectElement) {
    const $select = $(selectElement);
    let hasOtherOption = false;
    
    $select.find('option').each(function() {
        if ($(this).val() === 'other') {
            hasOtherOption = true;
            return false;
        }
    });

    if (!hasOtherOption) {
        $select.append(new Option('Other', 'other'));
    }
}

function validateOtherFields() {
    let isValid = true;
    
    const fields = {
        language: $('select[name="skills[language][]"]'),
        tools: $('select[name="skills[tools][]"]'),
        technologies: $('select[name="skills[technologies][]"]')
    };

    Object.entries(fields).forEach(([type, select]) => {
        const selectedOptions = select.val();
        if (selectedOptions && selectedOptions.includes('other')) {
            const otherInput = $(`#other_${type}`);
            if (!otherInput.val()) {
                alert(`Please enter your other ${type}`);
                isValid = false;
            }
        }
    });

    return isValid;
}

// Project Handling
function initializeProjectHandling() {
    const addProjectButton = document.getElementById('add-project');
    const projectsContainer = document.getElementById('projects-container');

    if (addProjectButton && projectsContainer) {
        addProjectButton.addEventListener('click', () => addProject(projectsContainer));
    }
}

function addProject(container) {
            const newProject = document.createElement('div');
            newProject.className = 'project-entry';
    const currentIndex = container.querySelectorAll('.project-entry').length;
    
            newProject.innerHTML = `
                <h4>Project ${currentIndex + 1}</h4>
                <div class="form-group">
                    <label for="project_title_${currentIndex}">Project Title*</label>
                    <input type="text" id="project_title_${currentIndex}" name="projects[${currentIndex}][title]" required>
                </div>
                <div class="form-group">
                    <label for="project_description_${currentIndex}">Description*</label>
                    <textarea id="project_description_${currentIndex}" name="projects[${currentIndex}][description]" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="project_technologies_${currentIndex}">Technologies Used*</label>
                    <input type="text" id="project_technologies_${currentIndex}" name="projects[${currentIndex}][technologies]"
                           placeholder="e.g., PHP, MySQL, JavaScript" required>
                </div>
                <button type="button" class="remove-project btn-secondary" onclick="removeEntry(this)">Remove Project</button>
            `;
    
    container.appendChild(newProject);
    }

// Certificate Handling
function initializeCertificateHandling() {
    const addCertificationButton = document.getElementById('add-certification');
    const certificationsContainer = document.getElementById('certifications-container');

    if (addCertificationButton && certificationsContainer) {
        addCertificationButton.addEventListener('click', () => addCertificate(certificationsContainer));
    }
}

function addCertificate(container) {
            const newCertification = document.createElement('div');
            newCertification.className = 'certification-entry';
    const currentIndex = container.querySelectorAll('.certification-entry').length;
            
            newCertification.innerHTML = `
                <div class="form-group">
                    <label for="cert_file_${currentIndex}">Upload Certificate* (PDF, JPG, JPEG, PNG)</label>
                    <input type="file" name="certifications[${currentIndex}][certificate_file]" 
                           accept=".pdf, .jpg, .jpeg, .png" required>
                    <button type="button" class="remove-certification btn-secondary" onclick="removeEntry(this)">
                        Remove Certificate
                    </button>
                </div>
            `;
    
    container.appendChild(newCertification);
    }

// Global function for removing entries
    window.removeEntry = function(button) {
        const entryToRemove = button.closest('.project-entry, .certification-entry');
        if (entryToRemove) {
            entryToRemove.remove();
    }
};

// Add enrollment format toggle functionality
function initializeEnrollmentFormat() {
    const formatOld = document.getElementById('format_old');
    const formatNew = document.getElementById('format_new');
    const oldFormatGroup = document.getElementById('old_format_group');
    const newFormatGroup = document.getElementById('new_format_group');
    const enrollmentOld = document.getElementById('enrollment_number_old');
    const enrollmentNew = document.getElementById('enrollment_number');
    const courseSelect = document.getElementById('course');

    if (!formatOld || !formatNew || !oldFormatGroup || !newFormatGroup || !courseSelect) return;

    function validateOldEnrollmentFormat() {
        const enrollmentValue = enrollmentOld.value.toUpperCase();
        enrollmentOld.value = enrollmentValue;
        const selectedCourse = courseSelect.value;
        const errorContainer = enrollmentOld.parentElement.querySelector('.error-message') || 
                             document.createElement('div');
        
        errorContainer.className = 'error-message';
        if (!enrollmentOld.parentElement.querySelector('.error-message')) {
            enrollmentOld.parentElement.appendChild(errorContainer);
        }

        // Clear previous validation state
        enrollmentOld.classList.remove('error', 'valid');
        errorContainer.style.display = 'none';

        if (!enrollmentValue) return;

        const regex = /^(\d{2})(BCA|MCA)(\d{2,3})$/;
        const match = enrollmentValue.match(regex);

        if (!match) {
            enrollmentOld.classList.add('error');
            errorContainer.textContent = 'Invalid format. Please use YY|Course|Number format (e.g., 02BCA16 or 09BCA015)';
            errorContainer.style.display = 'block';
            return false;
        }

        const [_, year, course, number] = match;
        
        // Check if the number is 00, 000, or out of valid range
        const numValue = parseInt(number);
        if (numValue === 0) {
            enrollmentOld.classList.add('error');
            errorContainer.textContent = 'Enrollment number cannot end with 00 or 000';
            errorContainer.style.display = 'block';
            return false;
        }

        // Check if number is within valid range (01-99 for 2 digits, 001-999 for 3 digits)
        const isValidRange = number.length === 2 ? 
            (numValue >= 1 && numValue <= 99) : 
            (numValue >= 1 && numValue <= 999);

        if (!isValidRange) {
            enrollmentOld.classList.add('error');
            errorContainer.textContent = `Enrollment number must be between ${number.length === 2 ? '01-99' : '001-999'}`;
            errorContainer.style.display = 'block';
            return false;
        }

        const enrollmentCourse = match[2];
        if (selectedCourse && enrollmentCourse !== selectedCourse) {
            enrollmentOld.classList.add('error');
            errorContainer.textContent = `The enrollment number should contain ${selectedCourse} for ${selectedCourse} students`;
            errorContainer.style.display = 'block';
            return false;
        }

        enrollmentOld.classList.add('valid');
        return true;
    }

    function validateNewEnrollmentFormat() {
        const enrollmentValue = enrollmentNew.value;
        const errorContainer = enrollmentNew.parentElement.querySelector('.error-message') || 
                             document.createElement('div');
        
        errorContainer.className = 'error-message';
        if (!enrollmentNew.parentElement.querySelector('.error-message')) {
            enrollmentNew.parentElement.appendChild(errorContainer);
        }

        // Clear previous validation state
        enrollmentNew.classList.remove('error', 'valid');
        errorContainer.style.display = 'none';

        if (!enrollmentValue) return;

        // First 4 digits should be a valid year between 2011 and current year
        const yearPrefix = enrollmentValue.substring(0, 4);
        const currentYear = new Date().getFullYear();
        const year = parseInt(yearPrefix);

        if (isNaN(year) || year < 2011 || year > currentYear) {
            enrollmentNew.classList.add('error');
            errorContainer.textContent = `Enrollment number must start with a valid year between 2011 and ${currentYear}`;
            errorContainer.style.display = 'block';
            return false;
        }

        // Check if total length is 15 and all characters are numbers
        if (!/^\d{15}$/.test(enrollmentValue)) {
            enrollmentNew.classList.add('error');
            errorContainer.textContent = 'Enrollment number must be exactly 15 digits starting with your enrollment year';
            errorContainer.style.display = 'block';
            return false;
        }

        enrollmentNew.classList.add('valid');
        return true;
    }

    function toggleEnrollmentFormat(isOldFormat) {
        oldFormatGroup.style.display = isOldFormat ? 'block' : 'none';
        newFormatGroup.style.display = isOldFormat ? 'none' : 'block';
        
        // Toggle required attribute
        enrollmentOld.required = isOldFormat;
        enrollmentNew.required = !isOldFormat;
        
        // Clear values when switching
        if (isOldFormat) {
            enrollmentNew.value = '';
    } else {
            enrollmentOld.value = '';
        }

        // Validate if there's a value
        if (isOldFormat && enrollmentOld.value) {
            validateOldEnrollmentFormat();
        } else if (!isOldFormat && enrollmentNew.value) {
            validateNewEnrollmentFormat();
        }
    }

    // Force uppercase for old format enrollment
    enrollmentOld.addEventListener('input', function(e) {
        const start = this.selectionStart;
        const end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
        validateOldEnrollmentFormat();
    });

    // Add validation for new format enrollment
    enrollmentNew.addEventListener('input', validateNewEnrollmentFormat);

    formatOld.addEventListener('change', () => toggleEnrollmentFormat(true));
    formatNew.addEventListener('change', () => toggleEnrollmentFormat(false));
    
    // Add validation when course changes
    courseSelect.addEventListener('change', function() {
        if (formatOld.checked && enrollmentOld.value) {
            validateOldEnrollmentFormat();
        }
    });

    // Set initial state based on default selection
    toggleEnrollmentFormat(formatOld.checked);
}

function toggleEmploymentFields() {
    const status = document.getElementById('current_status').value;
    const employmentFields = document.getElementById('employment-fields');
    const freelancerFields = document.getElementById('freelancer-fields');
    
    // Hide all fields first
    employmentFields.style.display = 'none';
    freelancerFields.style.display = 'none';
    
    // Remove required attribute from all fields
    const employedInputs = employmentFields.querySelectorAll('input, select');
    const freelancerInputs = freelancerFields.querySelectorAll('input, select');
    
    employedInputs.forEach(input => input.required = false);
    freelancerInputs.forEach(input => {
        input.required = false;
        // Destroy any existing Select2 instances
        if ($(input).hasClass('select2-hidden-accessible')) {
            $(input).select2('destroy');
        }
    });
    
    // Show and make required based on status
    if (status === 'employed') {
        employmentFields.style.display = 'block';
        employedInputs.forEach(input => input.required = true);
    } else if (status === 'freelancer') {
        freelancerFields.style.display = 'block';
        
        // Initialize Select2 for platforms
        $('#platforms').select2({
            placeholder: "Select freelancing platforms",
            tags: true,
            theme: "classic",
            width: '100%',
            closeOnSelect: false,
            allowClear: true,
            minimumResultsForSearch: 0
        }).on('change', function() {
            validateSelect2Field($(this), 'Please select at least one platform');
        });

        // Initialize Select2 for expertise areas
        $('#expertise_areas').select2({
            placeholder: "Select areas of expertise",
            tags: true,
            theme: "classic",
            width: '100%',
            closeOnSelect: false,
            allowClear: true,
            minimumResultsForSearch: 0
        }).on('change', function() {
            validateSelect2Field($(this), 'Please select at least one area of expertise');
        });

        // Make non-Select2 fields required
        freelancerInputs.forEach(input => {
            if (!$(input).hasClass('select2-hidden-accessible')) {
                input.required = true;
            }
        });

        // Trigger initial validation for Select2 fields
        validateSelect2Field($('#platforms'), 'Please select at least one platform');
        validateSelect2Field($('#expertise_areas'), 'Please select at least one area of expertise');
    }
}

function validateSelect2Field($select, errorMessage) {
    const container = $select.next('.select2-container');
    const errorDiv = container.next('.field-error');
    const selected = $select.val();
    
    // Remove existing error div if it exists
    if (errorDiv.length) {
        errorDiv.remove();
    }
    
    if (!selected || selected.length === 0) {
        container.addClass('error');
        const newErrorDiv = $('<div class="field-error"></div>').text(errorMessage);
        container.after(newErrorDiv);
        return false;
    } else {
        container.removeClass('error');
        return true;
    }
}

function showLoadingState() {
    // Create loading overlay if it doesn't exist
    let loadingOverlay = document.querySelector('.loading-overlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your registration...</div>
        `;
        document.body.appendChild(loadingOverlay);
    }
    loadingOverlay.style.display = 'flex';
}

function hideLoadingState() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

function showFormError(message) {
    // Remove any existing error messages
    const existingErrors = document.querySelectorAll('.form-error-message');
    existingErrors.forEach(error => error.remove());

    // Create new error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'form-error-message';
    errorDiv.innerHTML = `
        <div class="error-content">
            <div class="error-icon">⚠️</div>
            <div class="error-text">${message}</div>
            <button class="error-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    // Insert error at the top of the form
    const form = document.querySelector('.registration-form');
    form.insertBefore(errorDiv, form.firstChild);

    // Scroll to error message
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentElement) {
            errorDiv.remove();
        }
    }, 5000);
}

// Add these styles to your CSS
const style = document.createElement('style');
style.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-text {
        color: white;
        margin-top: 20px;
        font-size: 18px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .form-error-message {
        margin: 20px;
        padding: 0;
        animation: slideDown 0.3s ease-out;
    }

    .error-content {
        background-color: #fff3f3;
        border: 1px solid #ff4444;
        border-radius: 8px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .error-icon {
        font-size: 20px;
        margin-right: 15px;
    }

    .error-text {
        color: #dc3545;
        flex-grow: 1;
        font-size: 16px;
    }

    .error-close {
        background: none;
        border: none;
        color: #666;
        font-size: 24px;
        cursor: pointer;
        padding: 0 5px;
    }

    .error-close:hover {
        color: #dc3545;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
