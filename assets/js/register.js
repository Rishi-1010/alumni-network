console.log('register.js loaded');
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Get all step elements
    const steps = document.querySelectorAll('.form-step');
    const totalSteps = steps.length; // This will now be 6 after removing step 7 from HTML

    // Get all navigation buttons
    const step1Next = document.getElementById('step1Next');
    const step2Prev = document.getElementById('step2Prev');
    const step2Next = document.getElementById('step2Next');
    const step3Prev = document.getElementById('step3Prev');
    const step3Next = document.getElementById('step3Next');
    const step4Prev = document.getElementById('step4Prev');
    const step4Next = document.getElementById('step4Next');
    const step5Prev = document.getElementById('step5Prev');
    const step5Next = document.getElementById('step5Next');
    const step6Prev = document.getElementById('step6Prev');
    const submitBtn = document.getElementById('submitForm'); // This is now the "Next" button on step 6

    // Navigation functions
    function moveToStep(currentStep, nextStep) {
        console.log(`>>> Entering moveToStep: Moving from step ${currentStep} to ${nextStep}`); // Log entry

        const currentStepElement = document.getElementById(`step${currentStep}`);
        const nextStepElement = document.getElementById(`step${nextStep}`);

        console.log('Current step element:', currentStepElement);
        console.log('Next step element:', nextStepElement);


        if (currentStepElement) {
            if (nextStep > currentStep) {
                console.log(`>>> Checking validation for step ${currentStep}`); // Log before validation
                // Moving forward - validate
                const isValid = validateStep(currentStep); // Store result
                console.log(`>>> validateStep(${currentStep}) returned: ${isValid}`); // Log result
                if (!isValid) {
                    return false; // Stop if invalid
                }
            }

            // If moving from step 6, submit the form data first, then redirect
            if (currentStep === 6 && nextStep === 7) { // Check if moving from step 6
                console.log('Attempting to submit form data before redirecting...');
                const form = document.getElementById('registrationForm');
                const formData = new FormData(form);

                // Add loading state to the button that triggered this
                 const step6NextButton = document.getElementById('submitForm'); // Get the button
                 if(step6NextButton){
                    step6NextButton.disabled = true;
                    step6NextButton.innerHTML = 'Processing...';
                 }


            }

             // Handle normal step transitions (excluding step 7)
             // The form will now submit normally, without AJAX
             if (nextStepElement) {
                // Hide current step with transition
                currentStepElement.style.opacity = '0';
                currentStepElement.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    currentStepElement.style.display = 'none';

                    // Show next step with transition
                    nextStepElement.style.display = 'block';
                    nextStepElement.style.opacity = '0';
                    nextStepElement.style.transform = 'translateY(20px)';

                    // Force reflow to ensure transition starts from initial state
                    nextStepElement.offsetHeight;

                    nextStepElement.style.opacity = '1';
                    nextStepElement.style.transform = 'translateY(0)';
                }, 300); // Wait for the opacity transition to complete

                // Update progress
                updateProgress(nextStep);

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return true;
             } else {
                 console.error(`Next step element step${nextStep} not found.`);
                 return false;
             }

        } else {
            console.error(`Current step element step${currentStep} not found.`);
        }
        return false;
    }

    // Note: The first validateStep function definition was removed as it was duplicated.
    // The second definition below is the one being used.

    function updateProgress(step) {
        // Adjust totalSteps since step 7 is removed from the visual flow
        const adjustedTotalSteps = 6;
        const progress = ((step - 1) / (adjustedTotalSteps - 1)) * 100;
        const progressBar = document.querySelector('.progress-bar');
        const indicators = document.querySelectorAll('.step-indicator');

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        indicators.forEach((indicator, index) => {
            if (index < step) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
    }

    // Toggle employment fields based on status
    window.toggleEmploymentFields = function() {
        const status = document.getElementById('current_status').value;
        const fields = document.getElementById('employment-fields');
        const requiredInputs = fields.querySelectorAll('input');

        if (status === 'employed') {
            fields.style.display = 'block';
            requiredInputs.forEach(input => input.required = true);
        } else {
            fields.style.display = 'none';
            requiredInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    }

    // Enrollment format logic
    const formatOld = document.getElementById('format_old');
    const formatNew = document.getElementById('format_new');
    const oldFormatGroup = document.getElementById('old_format_group');
    const newFormatGroup = document.getElementById('new_format_group');
    const enrollmentNumberOld = document.getElementById('enrollment_number_old');
    const enrollmentNumberNew = document.getElementById('enrollment_number');

    function clearEnrollmentFields() {
        enrollmentNumberOld.value = '';
        enrollmentNumberNew.value = '';
    }

    const courseSelect = document.getElementById('course');

    function updateEnrollmentVisibility() {
        clearEnrollmentFields();
        const isOldFormat = formatOld.checked;

        enrollmentNumberOld.required = isOldFormat;
        enrollmentNumberNew.required = !isOldFormat;

        oldFormatGroup.style.display = isOldFormat ? 'block' : 'none';
        newFormatGroup.style.display = isOldFormat ? 'none' : 'block';
    }

    formatOld.addEventListener('change', updateEnrollmentVisibility);
    formatNew.addEventListener('change', updateEnrollmentVisibility);
    courseSelect.addEventListener('change', updateEnrollmentVisibility);

    // Update validation function to handle new enrollment fields
    function validateStep(step) {
        console.log(`>>> Entering validateStep for step ${step}`); // Log entry
        const stepElement = document.getElementById(`step${step}`);
        const requiredFields = stepElement ? stepElement.querySelectorAll('[required]') : [];
        let isValid = true;

        for (let i = 0; i < requiredFields.length; i++) {
            const field = requiredFields[i];

            // Skip validation for fields that are not currently displayed
            if (!field.offsetParent) {
                console.log(`Skipping validation for hidden field: ${field.id || field.name}`);
                continue;
            }

            const value = field.value ? field.value.trim() : '';
            if (!value) {
                console.log(`Validation failed: Field ${field.id || field.name} is empty.`);
                isValid = false;
                field.classList.add('error');
                // Don't continue here, check other fields too
            } else {
                 field.classList.remove('error'); // Remove error class if valid
            }

            // Specific field validations (only if value is not empty)
            if (value && field.id === 'enrollment_number_old') {
                const course = document.getElementById('course').value;
                const enrollmentNumber = field.value.toUpperCase();

                if (!enrollmentNumber.includes(course)) {
                    console.log(`Validation failed: Course mismatch for ${field.id}`);
                    isValid = false;
                    field.classList.add('error');
                    document.getElementById('course').classList.add('error');
                    alert("Course and Old Enrollment Number course don't match properly.");
                }
                else {
                    let pattern;
                    if (course === 'BCA') {
                        pattern = /^[0-9]{2}(BCA)[0-9]{3}$/;
                    } else if (course === 'MCA') {
                        pattern = /^[0-9]{2}(MCA)[0-9]{3}$/;
                    }
                    if (pattern && !pattern.test(value)) {
                        console.log(`Validation failed: Pattern mismatch for ${field.id}`);
                        isValid = false;
                        field.classList.add('error');
                        alert('Please enter a valid old enrollment number (YYCourseNumber)');
                    }
                }
            } else if (value && field.id === 'enrollment_number') {
                if (!/^[0-9]{15}$/.test(value)) {
                    console.log(`Validation failed: Pattern mismatch for ${field.id}`);
                    isValid = false;
                    field.classList.add('error');
                    alert('Please enter a valid 15-digit enrollment number');
                }
            } else if (value && field.tagName === 'SELECT' && field.value === '') {
                 console.log(`Validation failed: Select field ${field.id || field.name} has no value.`);
                 isValid = false;
                 field.classList.add('error');
            } else if (field.type === 'file' && field.files.length === 0) {
                console.log(`Validation failed: File field ${field.id || field.name} has no file selected.`);
                isValid = false;
                field.classList.add('error');
            }
        }

        if (!isValid) {
            console.log(`>>> validateStep for step ${step} returning false.`); // Log final result
            alert('Please fill in all required fields correctly.');
        } else {
             console.log(`>>> validateStep for step ${step} returning true.`); // Log final result
        }

        return isValid;
    }

    // Event Listeners
    if (step1Next) {
        console.log('Adding event listener to step1Next'); // Log listener setup
        step1Next.addEventListener('click', () => {
            console.log('step1Next button clicked!'); // Log click
            moveToStep(1, 2);
        });
    } else {
        console.error('step1Next button not found!');
    }

    if (step2Prev) {
        step2Prev.addEventListener('click', () => moveToStep(2, 1));
    }

    if (step2Next) {
        step2Next.addEventListener('click', () => moveToStep(2, 3));
    }

    if (step3Prev) {
        step3Prev.addEventListener('click', () => moveToStep(3, 2));
    }

    if (step3Next) {
        step3Next.addEventListener('click', () => moveToStep(3, 4));
    }

    // Add project functionality
    let projectCount = 0;
    document.getElementById('add-project').addEventListener('click', function() {
        projectCount++;
        const container = document.getElementById('projects-container');
        const newProject = document.createElement('div');
        newProject.className = 'project-entry';
        newProject.innerHTML = `
            <div class="form-group">
                <label>Project Title*</label>
                <input type="text" name="projects[${projectCount}][title]" required>
            </div>
            <div class="form-group">
                <label>Description*</label>
                <textarea name="projects[${projectCount}][description]" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Technologies Used*</label>
                <input type="text" name="skills[${projectCount}][technologies]"
                       placeholder="e.g., PHP, MySQL, JavaScript" required>
            </div>
            <div class="form-group date-range">
                <div>
                    <label>Start Date*</label>
                    <input type="date" name="projects[${projectCount}][start_date]" required>
                </div>
                <div>
                    <label>End Date</label>
                    <input type="date" name="projects[${projectCount}][end-date]">
                </div>
            </div>
            <button type="button" class="remove-project" onclick="removeProject(this)">Remove Project</button>
        `;
        container.appendChild(newProject);
    });

    // Remove project functionality
    window.removeProject = function(button) {
        button.parentElement.remove();
    };

    // Add event listeners for Step 4
    if (step4Prev) {
        step4Prev.addEventListener('click', () => moveToStep(4, 3));
    }

    if (step4Next) {
        step4Next.addEventListener('click', () => moveToStep(4, 5));
    }

    // Skills functionality
    let skillCount = 0;
    document.getElementById('add-skill').addEventListener('click', function() {
        skillCount++;
        const container = document.getElementById('skills-container');
        const newSkill = document.createElement('div');
        newSkill.className = 'skill-entry';
        newSkill.innerHTML = `
            <div class="form-group">
                <label>Skill Name*</label>
                <input type="text" name="skills[${skillCount}][name]" required
                       placeholder="e.g., Java, Python, Web Development">
            </div>
            <div class="form-group">
                <label>Proficiency Level*</label>
                <select name="skills[${skillCount}][level]" required>
                    <option value="">Select Level</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                    <option value="Expert">Expert</option>
                </select>
            </div>
            <button type="button" class="remove-skill" onclick="removeSkill(this)">Remove Skill</button>
        `;
        container.appendChild(newSkill);
    });

    // Remove skill functionality
    window.removeSkill = function(button) {
        button.parentElement.remove();
    };

    // Add event listeners for Step 5
    if (step5Prev) {
        step5Prev.addEventListener('click', () => moveToStep(5, 4));
    }

    if (step5Next) {
        step5Next.addEventListener('click', () => moveToStep(5, 6));
    }

    // Career Goals functionality
    let goalCount = 0;
    document.getElementById('add-goal').addEventListener('click', function() {
        goalCount++;
        const container = document.getElementById('goals-container');
        const newGoal = document.createElement('div');
        newGoal.className = 'goal-entry';
        newGoal.innerHTML = `
            <div class="form-group">
                <label>Career Goal*</label>
                <textarea name="career_goals[${goalCount}][description]" rows="3" required
                          placeholder="Describe your career goal"></textarea>
            </div>
            <button type="button" class="remove-goal" onclick="removeGoal(this)">Remove Goal</button>
        `;
        container.appendChild(newGoal);
    });

    // Remove goal functionality
    window.removeGoal = function(button) {
        button.parentElement.remove();
    };

    // Add event listeners for Step 6
    if (step6Prev) {
        step6Prev.addEventListener('click', () => moveToStep(6, 5));
    }

    // The submit button now acts as the "Next" button for step 6
    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            // e.preventDefault(); // Remove this line
            console.log('Step 6 Next/Submit button clicked');

            // Validate step 6 before proceeding
            if (validateStep(6)) {
                // Log a message before form submission
                console.log('Form is valid, submitting...');
                // Form will submit normally
            } else {
                 // Re-enable button if validation fails
                 submitBtn.disabled = false;
                 submitBtn.innerHTML = 'Submit';
            }
        });
    }

    // Initialize first step
    updateProgress(1);
});
