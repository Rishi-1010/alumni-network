console.log('register.js loaded');
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Get all step elements
    const steps = document.querySelectorAll('.form-step');

    // Get all navigation buttons
    const step1Next = document.getElementById('step1Next');
    const step2Prev = document.getElementById('step2Prev');
    const step2Next = document.getElementById('step2Next');
    const step3Prev = document.getElementById('step3Prev');
    const step3Next = document.getElementById('step3Next');
    const step4Prev = document.getElementById('step4Prev');
    const step4Next = document.getElementById('step4Next');
    const step5Prev = document.getElementById('step5Prev'); // Projects Prev
    const step5Next = document.getElementById('step5Next'); // Projects Next
    const step6Prev = document.getElementById('step6Prev'); // Skills Prev
    const step6Next = document.getElementById('step6Next'); // Skills Next
    const step7Prev = document.getElementById('step7Prev'); // Career Goals Prev
    const submitBtn = document.getElementById('submitForm'); // Submit button on step 7

    // Project Question Elements
    const hasProjectsYes = document.getElementById('hasProjectsYes');
    const hasProjectsNo = document.getElementById('hasProjectsNo');
    // const projectCountGroup = document.getElementById('projectCountGroup'); // Removed
    // const projectCountInput = document.getElementById('projectCount'); // Removed
    const projectsContainer = document.getElementById('projects-container'); // Cache projects container
    const addProjectButton = document.getElementById('add-project'); // Cache add project button

    // --- Navigation Function ---
    function moveToStep(currentStep, nextStep) {
        console.log(`>>> Entering moveToStep: Moving from step ${currentStep} to ${nextStep}`);

        const currentStepElement = document.getElementById(`step${currentStep}`);
        const nextStepElement = document.getElementById(`step${nextStep}`);

        console.log('Current step element:', currentStepElement);
        console.log('Next step element:', nextStepElement);

        if (!currentStepElement) {
             console.error(`Current step element step${currentStep} not found.`);
             return false;
        }
         // Check if nextStepElement exists before proceeding (except for final submit)
         if (!nextStepElement && nextStep <= 7) {
             console.error(`Next step element step${nextStep} not found.`);
             return false;
         }

        // Validate only when moving forward
        if (nextStep > currentStep) {
            console.log(`>>> Checking validation for step ${currentStep}`);
            const isValid = validateStep(currentStep); // Perform validation
            console.log(`>>> validateStep(${currentStep}) returned: ${isValid}`);
            if (!isValid) {
                // If validation fails, validateStep function should handle alerts/styling
                return false; // Stop navigation
            }
        }

         // Handle normal step transitions
         if (nextStepElement) {
            // Hide current step
            currentStepElement.style.opacity = '0';
            currentStepElement.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                currentStepElement.style.display = 'none';

                // Show next step
                nextStepElement.style.display = 'block';
                nextStepElement.style.opacity = '0';
                nextStepElement.style.transform = 'translateY(20px)';
                nextStepElement.offsetHeight; // Force reflow
                nextStepElement.style.opacity = '1';
                nextStepElement.style.transform = 'translateY(0)';

                updateProgress(nextStep); // Update progress bar/indicators
                window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll to top
            }, 300); // Match transition duration
            return true;
         }
         return false; // Should not be reached if nextStepElement exists
    }

    // --- Progress Bar Update ---
    function updateProgress(step) {
        const adjustedTotalSteps = 7; // Total number of steps in the form
        const currentProgressStep = Math.min(step, adjustedTotalSteps);
        // Calculate percentage ensuring division by zero is avoided if adjustedTotalSteps is 1
        const progress = adjustedTotalSteps > 1 ? ((currentProgressStep - 1) / (adjustedTotalSteps - 1)) * 100 : (currentProgressStep === 1 ? 100 : 0);
        const progressBar = document.querySelector('.progress-bar');
        const indicators = document.querySelectorAll('.step-indicator');

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        if (indicators && indicators.length >= adjustedTotalSteps) {
             indicators.forEach((indicator, index) => {
                 if (index < currentProgressStep) {
                     indicator.classList.add('active');
                 } else {
                     indicator.classList.remove('active');
                 }
             });
        } else {
             console.warn("Progress indicators not found or not enough indicators for total steps.");
        }
    }

    // --- Field Toggling ---
    window.toggleEmploymentFields = function() {
        const status = document.getElementById('current_status').value;
        const fields = document.getElementById('employment-fields');
        if (!fields) return;
        const requiredInputs = fields.querySelectorAll('input, select, textarea');

        if (status === 'employed') {
            fields.style.display = 'block';
            requiredInputs.forEach(input => input.required = true);
        } else {
            fields.style.display = 'none';
            requiredInputs.forEach(input => {
                input.required = false;
                input.value = ''; // Clear value when hiding
                input.classList.remove('error'); // Clear error state
            });
        }
    }

    // --- Enrollment Format Logic ---
    const formatOld = document.getElementById('format_old');
    const formatNew = document.getElementById('format_new');
    const oldFormatGroup = document.getElementById('old_format_group');
    const newFormatGroup = document.getElementById('new_format_group');
    const enrollmentNumberOld = document.getElementById('enrollment_number_old');
    const enrollmentNumberNew = document.getElementById('enrollment_number');
    const courseSelect = document.getElementById('course');

    function clearEnrollmentFields() {
        if(enrollmentNumberOld) enrollmentNumberOld.value = '';
        if(enrollmentNumberNew) enrollmentNumberNew.value = '';
    }

    function updateEnrollmentVisibility() {
        clearEnrollmentFields();
        const isOldFormat = formatOld && formatOld.checked;

        if(enrollmentNumberOld) enrollmentNumberOld.required = isOldFormat;
        if(enrollmentNumberNew) enrollmentNumberNew.required = !isOldFormat;

        if(oldFormatGroup) oldFormatGroup.style.display = isOldFormat ? 'block' : 'none';
        if(newFormatGroup) newFormatGroup.style.display = isOldFormat ? 'none' : 'block';

        // Clear error states when format changes
        if(enrollmentNumberOld) enrollmentNumberOld.classList.remove('error');
        if(enrollmentNumberNew) enrollmentNumberNew.classList.remove('error');
    }

    if(formatOld) formatOld.addEventListener('change', updateEnrollmentVisibility);
    if(formatNew) formatNew.addEventListener('change', updateEnrollmentVisibility);
    if(courseSelect) courseSelect.addEventListener('change', updateEnrollmentVisibility);
    updateEnrollmentVisibility(); // Initial call

    // --- Validation Function ---
    function validateStep(step) {
        console.log(`>>> Entering validateStep for step ${step}`);
        const stepElement = document.getElementById(`step${step}`);
        if (!stepElement) {
            console.error(`validateStep: Could not find step element for step ${step}`);
            return false;
        }

        let isValid = true;
        // Find all potentially required fields within the current step
        const fieldsToCheck = stepElement.querySelectorAll('input[required], select[required], textarea[required]');

        console.log(`Validating step ${step}. Found ${fieldsToCheck.length} fields marked as required.`);

        fieldsToCheck.forEach(field => {
            // IMPORTANT: Only validate if the field is actually visible and required
            // offsetParent check is a good way to see if it's rendered
            if (field.required && field.offsetParent !== null) {
                let value = field.value;
                if (typeof value === 'string') {
                    value = value.trim();
                }

                let fieldValid = true; // Assume valid initially for this field

                // Check for emptiness (excluding file inputs here)
                if (field.type !== 'file' && !value) {
                    console.log(`Validation failed: Field ${field.id || field.name} is empty.`);
                    fieldValid = false;
                }
                // Check file inputs specifically
                else if (field.type === 'file' && field.files.length === 0) {
                    console.log(`Validation failed: File field ${field.id || field.name} has no file selected.`);
                    fieldValid = false;
                }
                 // Check select inputs specifically
                 else if (field.tagName === 'SELECT' && value === '') {
                     console.log(`Validation failed: Field ${field.id || field.name} has no value selected.`);
                     fieldValid = false;
                }
                // Specific pattern validations (only if not empty and applicable)
                else if (value && field.id === 'enrollment_number_old') {
                    const course = courseSelect ? courseSelect.value : '';
                    const enrollmentNumber = field.value.toUpperCase();
                    let pattern;
                    if (course === 'BCA') pattern = /^[0-9]{2}BCA[0-9]{3}$/;
                    else if (course === 'MCA') pattern = /^[0-9]{2}MCA[0-9]{3}$/;

                    if (!enrollmentNumber.includes(course) || (pattern && !pattern.test(enrollmentNumber))) {
                        console.log(`Validation failed: Pattern/Course mismatch for ${field.id}`);
                        fieldValid = false;
                    }
                } else if (value && field.id === 'enrollment_number') {
                    if (!/^[0-9]{15}$/.test(value)) {
                        console.log(`Validation failed: Pattern mismatch for ${field.id}`);
                        fieldValid = false;
                    }
                }
                // Removed specific validation for project count input

                // Update overall validity and styling
                if (!fieldValid) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            } else {
                 // If field is not required or not visible, ensure no error style
                 field.classList.remove('error');
                 console.log(`Skipping validation or removing error for non-required/hidden field: ${field.id || field.name}`);
            }
        });

        if (!isValid) {
            console.log(`>>> validateStep for step ${step} returning false.`);
            // Generic error message
            alert('Please fill in all required fields correctly.');
        } else {
            console.log(`>>> validateStep for step ${step} returning true.`);
        }
        return isValid;
    }


    // --- Event Listeners Setup ---
    if (step1Next) step1Next.addEventListener('click', () => moveToStep(1, 2));
    if (step2Prev) step2Prev.addEventListener('click', () => moveToStep(2, 1));
    if (step2Next) step2Next.addEventListener('click', () => moveToStep(2, 3));
    if (step3Prev) step3Prev.addEventListener('click', () => moveToStep(3, 2));
    if (step3Next) step3Next.addEventListener('click', () => moveToStep(3, 4));
    if (step4Prev) step4Prev.addEventListener('click', () => moveToStep(4, 3));
    // Step 4 Next handled below
    if (step5Prev) step5Prev.addEventListener('click', () => moveToStep(5, 4));
    // Step 5 Next is handled below within its own logic if needed, or directly moves to step 6
    // if (step5Next) step5Next.addEventListener('click', () => moveToStep(5, 6)); // Projects -> Skills

    if (step6Prev) {
        step6Prev.addEventListener('click', () => {
            const hasProjectsNoRadio = document.getElementById('hasProjectsNo');
            let previousStepTarget = null;

            // Determine the correct previous step based on the 'hasProjects' choice
            if (hasProjectsNoRadio && hasProjectsNoRadio.checked) {
                 console.log("Step 6 Prev clicked. User selected 'No' for projects. Targeting step 4.");
                 previousStepTarget = 4; // Go back to the question step
            } else {
                 console.log("Step 6 Prev clicked. User selected 'Yes' for projects. Targeting step 5.");
                 previousStepTarget = 5; // Go back to the project details step
            }

            // Move to the determined previous step
            if (previousStepTarget !== null) {
                 moveToStep(6, previousStepTarget);
            } else {
                 console.error("Could not determine previous step target from step 6.");
                 // Fallback or default behavior if needed, e.g., go to step 4
                 // moveToStep(6, 4);
            }
        });
    }
    // if (step5Next) step5Next.addEventListener('click', () => moveToStep(5, 6)); // Projects -> Skills - Redundant, removed

    // Step 6 Prev logic (already adjusted, seems correct)
    if (step6Prev) {
        step6Prev.addEventListener('click', () => {
            const hasProjectsNoRadio = document.getElementById('hasProjectsNo');
            let previousStepTarget = null;

            // Determine the correct previous step based on the 'hasProjects' choice
            if (hasProjectsNoRadio && hasProjectsNoRadio.checked) {
                 console.log("Step 6 Prev clicked. User selected 'No' for projects. Targeting step 4.");
                 previousStepTarget = 4; // Go back to the question step
            } else {
                 console.log("Step 6 Prev clicked. User selected 'Yes' for projects. Targeting step 5.");
                 previousStepTarget = 5; // Go back to the project details step
            }

            // Move to the determined previous step
            if (previousStepTarget !== null) {
                 moveToStep(6, previousStepTarget);
            } else {
                 console.error("Could not determine previous step target from step 6.");
                 // Fallback or default behavior if needed, e.g., go to step 4
                 // moveToStep(6, 4);
            }
        });
    }
    if (step6Next) step6Next.addEventListener('click', () => moveToStep(6, 7)); // Skills -> Career Goals
    if (step7Prev) step7Prev.addEventListener('click', () => moveToStep(7, 6)); // Career Goals -> Skills

    // --- Project Question Logic --- (Simplified)
    if (hasProjectsYes && hasProjectsNo && projectsContainer) {
        hasProjectsYes.addEventListener('change', function() {
            // No action needed here anymore, logic is in step4Next
        });

        hasProjectsNo.addEventListener('change', function() {
            // No action needed here anymore, logic is in step4Next
            // Clear projects container if user switches back to 'No' *after* visiting step 5
             if (projectsContainer) projectsContainer.innerHTML = '';
        });

    } else {
        console.error("One or more elements for project question logic not found (Yes/No radios or projectsContainer).");
    }

    // --- Step 4 Next Button Logic ---
    if (step4Next) {
        step4Next.addEventListener('click', () => {
            const hasProjectsRadio = document.querySelector('input[name="hasProjects"]:checked');
            if (!hasProjectsRadio) {
                console.error("No 'hasProjects' radio button selected.");
                return; // Should ideally not happen if one is checked by default
            }
            const hasProjects = hasProjectsRadio.value;
            console.log(`Step 4 Next clicked. hasProjects value: ${hasProjects}`);

            let nextStepTarget = null;

            if (hasProjects === 'yes') {
                // Target step 5, validation will happen in moveToStep
                nextStepTarget = 5;
                console.log("Targeting step 5.");
                // No need to generate fields based on count anymore
            } else { // hasProjects === 'no'
                console.log("Processing 'no' branch for projects.");
                if (projectsContainer) projectsContainer.innerHTML = ''; // Clear project fields if they exist
                nextStepTarget = 6; // Target Step 6 (Skills)
                console.log("Targeting step 6.");
            }

            // Call moveToStep, which performs validation *before* moving
            if (nextStepTarget !== null) {
                 moveToStep(4, nextStepTarget);
            } else {
                 console.error("Could not determine next step target from step 4.");
            }
        });
    }

    // --- Add/Remove Project Functionality ---
    let projectIndex = 1; // Start index for dynamically added projects (initial one is 0)

    if (addProjectButton && projectsContainer) {
        addProjectButton.addEventListener('click', function() {
            const newProject = document.createElement('div');
            newProject.className = 'project-entry';
            const currentIndex = projectsContainer.querySelectorAll('.project-entry').length; // Get current count for index
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
            projectsContainer.appendChild(newProject);
            projectIndex++; // Increment for next potential add
        });
    }

    // Generic remove function for dynamic entries (projects, skills, goals)
    window.removeEntry = function(button) {
        const entryToRemove = button.closest('.project-entry, .skill-entry, .goal-entry, .certification-entry');
        if (entryToRemove) {
            entryToRemove.remove();
            // Optional: Re-index remaining entries if necessary, though often backend can handle gaps.
            // Re-indexing is complex and prone to errors, avoid if possible.
            // If re-indexing: update labels (<h4>), input IDs, and names.
        }
    };


    // --- Skills Add/Remove ---
    let skillCount = 0; // Initialized based on existing entries later
    const addSkillButton = document.getElementById('add-skill');
    const skillsContainer = document.getElementById('skills-container');

    if (addSkillButton && skillsContainer) {
        // Initialize skillCount based on existing entries if any (e.g., if server pre-populates)
        skillCount = skillsContainer.querySelectorAll('.skill-entry').length;

        addSkillButton.addEventListener('click', function() {
            const newSkill = document.createElement('div');
            newSkill.className = 'skill-entry';
            // Use index based on current count for name attribute
            const currentIndex = skillsContainer.querySelectorAll('.skill-entry').length;
            newSkill.innerHTML = `
                <div class="form-group">
                    <label for="skill_name_${currentIndex}">Skill Name*</label>
                    <input type="text" id="skill_name_${currentIndex}" name="skills[${currentIndex}][name]" required
                           placeholder="e.g., Java, Python, Web Development">
                </div>
                <div class="form-group">
                    <label for="skill_level_${currentIndex}">Proficiency Level*</label>
                    <select id="skill_level_${currentIndex}" name="skills[${currentIndex}][level]" required>
                        <option value="">Select Level</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <button type="button" class="remove-skill btn-secondary" onclick="removeEntry(this)">Remove Skill</button>
            `;
            skillsContainer.appendChild(newSkill);
            // No need to increment skillCount here, index is based on current count
        });
    }
    // Removed redundant removeSkill function, using generic removeEntry

    // Add event listeners for Step 5 (Projects)
    if (step5Prev) {
        step5Prev.addEventListener('click', () => moveToStep(5, 4)); // Go back to Project Question
    }
    if (step5Next) {
         step5Next.addEventListener('click', () => moveToStep(5, 6)); // Go to Step 6 (Skills) - Validation happens in moveToStep
    }


    // Skills functionality (Step 6) - Simplified add logic
    const addSkillBtn = document.getElementById('add-skill'); // Ensure correct ID if changed
    const skillsCont = document.getElementById('skills-container'); // Ensure correct ID

    if (addSkillBtn && skillsCont) {
        addSkillBtn.addEventListener('click', function() {
            const container = skillsCont; // Use cached variable
            const newSkill = document.createElement('div');
            newSkill.className = 'skill-entry';
            const currentIndex = container.querySelectorAll('.skill-entry').length;
            newSkill.innerHTML = `
                <div class="form-group">
                    <label for="skill_name_${currentIndex}">Skill Name*</label>
                    <input type="text" id="skill_name_${currentIndex}" name="skills[${currentIndex}][name]" required
                           placeholder="e.g., Java, Python, Web Development">
                </div>
                <div class="form-group">
                    <label for="skill_level_${currentIndex}">Proficiency Level*</label>
                    <select id="skill_level_${currentIndex}" name="skills[${currentIndex}][level]" required>
                        <option value="">Select Level</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <button type="button" class="remove-skill btn-secondary" onclick="removeEntry(this)">Remove Skill</button>
            `;
            container.appendChild(newSkill);
        });
    }
    // Removed redundant removeSkill function

    // Add event listeners for Step 6 (Skills)
    // Step 6 Prev logic handled earlier (correctly goes to 4 or 5)
    if (step6Next) {
        step6Next.addEventListener('click', () => moveToStep(6, 7)); // Go to Step 7 (Career Goals)
    }


    // Career Goals functionality (Step 7) - Simplified add logic
    const addGoalButton = document.getElementById('add-goal');
    const goalsContainer = document.getElementById('goals-container');

    if (addGoalButton && goalsContainer) {
        // Initialize goalCount based on existing entries if any
        goalCount = goalsContainer.querySelectorAll('.goal-entry').length;

        addGoalButton.addEventListener('click', function() {
            const container = goalsContainer;
            const newGoal = document.createElement('div');
            newGoal.className = 'goal-entry';
            const currentIndex = container.querySelectorAll('.goal-entry').length;
            newGoal.innerHTML = `
                <div class="form-group">
                    <label for="goal_description_${currentIndex}">Career Goal*</label>
                    <textarea id="goal_description_${currentIndex}" name="career_goals[${currentIndex}][description]" rows="3" required
                              placeholder="Describe your career goal"></textarea>
                </div>
                <button type="button" class="remove-goal btn-secondary" onclick="removeEntry(this)">Remove Goal</button>
            `;
            container.appendChild(newGoal);
        });
    }
    // Removed redundant removeGoal function

    // Add event listeners for Step 7 (Career Goals)
    if (step7Prev) {
        step7Prev.addEventListener('click', () => moveToStep(7, 6)); // Go back to Step 6 (Skills)
    }

    // The submit button is on Step 7
    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            console.log('Step 7 Submit button clicked');
            // Validate step 7 before allowing default form submission
            if (!validateStep(7)) {
                 e.preventDefault(); // Prevent form submission if validation fails
                 console.log('Step 7 validation failed. Preventing submission.');
            } else {
                console.log('Form is valid, submitting...');
                // Allow default form submission
            }
        });
    }

    // Initialize first step
    updateProgress(1);
});
