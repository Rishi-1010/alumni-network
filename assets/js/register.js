document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');

    // Get all step elements
    const steps = document.querySelectorAll('.form-step');
    const totalSteps = steps.length;

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
    const step6Next = document.getElementById('step6Next');
    const step7Prev = document.getElementById('step7Prev');
    const submitBtn = document.getElementById('submitForm');

    // Navigation functions
    function moveToStep(currentStep, nextStep) {
        console.log(`Moving from step ${currentStep} to ${nextStep}`);
        
        const currentStepElement = document.getElementById(`step${currentStep}`);
        const nextStepElement = document.getElementById(`step${nextStep}`);
        
        console.log('Current step element:', currentStepElement);
        console.log('Next step element:', nextStepElement);

        if (currentStepElement && nextStepElement) {
            if (nextStep > currentStep) {
                // Moving forward - validate
                if (!validateStep(currentStep)) {
                    return false;
                }
            }
            
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
            }, 300); // Wait for the opacity transition to complete (adjust as needed)
            
            // Update progress
            updateProgress(nextStep);
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            return true;
        } else {
            console.error('Step elements not found:', 
                         `step${currentStep}:`, currentStepElement, 
                         `step${nextStep}:`, nextStepElement);
        }
        return false;
    }

   function validateStep(step) {
        const stepElement = document.getElementById(`step${step}`);
        const requiredFields = stepElement ? stepElement.querySelectorAll('[required]') : [];
        let isValid = true;

        if (!stepElement) {
            console.error(`Step element with id step${step} not found`);
            return false;
        }

        for (let i = 0; i < requiredFields.length; i++) {
            const field = requiredFields[i];

            // Skip validation for fields that are not currently displayed
            if (!field.offsetParent) {
                continue;
            }

            const value = field.value ? field.value.trim() : '';
            if (!value) {
                isValid = false;
                field.classList.add('error');
                continue;
            }

            field.classList.remove('error');

            if (field.id === 'enrollment_number_old') {
                const course = document.getElementById('course').value;
                let pattern;
                if (course === 'BCA') {
                    pattern = /^[0-9]{2}(BCA)[0-9]{3}$/;
                } else if (course === 'MCA') {
                    pattern = /^[0-9]{2}(MCA)[0-9]{3}$/;
                }
                if (pattern && !pattern.test(value)) {
                    isValid = false;
                    field.classList.add('error');
                    alert('Please enter a valid old enrollment number (YYCourseNumber)');
                }
            } else if (field.id === 'enrollment_number') {
                if (!/^[0-9]{15}$/.test(value)) {
                    isValid = false;
                    field.classList.add('error');
                    alert('Please enter a valid 15-digit enrollment number');
                }
            } else if (field.tagName === 'SELECT' && field.value === '') {
                isValid = false;
                field.classList.add('error');
            }
        }

        if (!isValid) {
            alert('Please fill in all required fields correctly.');
        }

        return isValid;
    }

    function updateProgress(step) {
        const progress = ((step - 1) / (totalSteps - 1)) * 100;
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
        const stepElement = document.getElementById(`step${step}`);
        const requiredFields = stepElement ? stepElement.querySelectorAll('[required]') : [];
        let isValid = true;

        for (let i = 0; i < requiredFields.length; i++) {
            const field = requiredFields[i];

            // Skip validation for fields that are not currently displayed
            if (!field.offsetParent) {
                continue;
            }

            const value = field.value ? field.value.trim() : '';
            if (!value) {
                isValid = false;
                field.classList.add('error');
                continue;
            }

            field.classList.remove('error');

            if (field.id === 'enrollment_number_old') {
                const course = document.getElementById('course').value;
                const enrollmentNumber = field.value.toUpperCase();

                if (!enrollmentNumber.includes(course)) {
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
                        isValid = false;
                        field.classList.add('error');
                        alert('Please enter a valid old enrollment number (YYCourseNumber)');
                    }
                }
            } else if (field.id === 'enrollment_number') {
                if (!/^[0-9]{15}$/.test(value)) {
                    isValid = false;
                    field.classList.add('error');
                    alert('Please enter a valid 15-digit enrollment number');
                }
            } else if (field.tagName === 'SELECT' && field.value === '') {
                isValid = false;
                field.classList.add('error');
            }
        }

        if (!isValid) {
            alert('Please fill in all required fields correctly.');
        }

        return isValid;
    }

    // Event Listeners
    if (step1Next) {
        step1Next.addEventListener('click', () => moveToStep(1, 2));
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
                <input type="text" name="projects[${projectCount}][technologies]" 
                       placeholder="e.g., PHP, MySQL, JavaScript" required>
            </div>
            <div class="form-group date-range">
                <div>
                    <label>Start Date*</label>
                    <input type="date" name="projects[${projectCount}][start_date]" required>
                </div>
                <div>
                    <label>End Date</label>
                    <input type="date" name="projects[${projectCount}][end_date]">
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

    if (step6Next) {
        step6Next.addEventListener('click', () => moveToStep(6, 7));
    }

    // Add event listener for Step 7 Previous button
    if (step7Prev) {
        step7Prev.addEventListener('click', () => moveToStep(7, 6));
    }

    // Add event listener for form submission
    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('Submit button clicked');
            
            const form = document.getElementById('registrationForm');
            
            if (validateStep(7)) {
                console.log('Final validation passed');
                
                // Add loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Submitting...';
                
                // Submit form using fetch
                fetch('process_registration.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(response => response.json())  // Parse response as JSON
                .then(data => {
                    console.log('Server response:', data);
                    
                    if (data.status === 'success') {
                        // Show success message
                        alert(data.message);
                        // Redirect to login page
                        window.location.href = '../Login/login.php';
                    } else {
                        // Show error message
                        throw new Error(data.message || 'Registration failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Registration failed: ' + error.message);
                    
                    // Reset submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit';
                });
            }
        });
    }

    // Initialize first step
    updateProgress(1);
});
