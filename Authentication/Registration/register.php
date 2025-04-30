<?php
session_start();
require_once '../../config/db_connection.php';
if (isset($_SESSION['error'])) {
    echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Registration</title>
    <link rel="stylesheet" href="../../assets/css/register.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script> -->
    <!-- <script src="../../assets/js/animations.js"></script> -->
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     hoverEffect('.next-btn');
        //     hoverEffect('.submit-btn');
        //     hoverEffect('.nav-links a');
        // });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Add this CSS for Select2 styling -->
    <style>
        .select2-container {
            width: 100% !important;
            margin-bottom: 10px;
        }
        .select2-selection--multiple {
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }
        .select2-error {
            border-color: #dc3545 !important;
        }
        .select2-container--classic .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }
        
        /* Input validation styles */
        .form-group {
            position: relative;
        }
        .form-group input {
            padding-right: 30px;
        }
        .validation-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            display: none;
            pointer-events: none;
        }
        .validation-icon.valid {
            color: #28a745;
            display: block;
        }
        .validation-icon.invalid {
            color: #dc3545;
            display: block;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .form-group.error input {
            border-color: #dc3545;
        }
        .form-group.valid input {
            border-color: #28a745;
        }
        
        /* Email validation styles */
        .email-input {
            position: relative;
        }
        .email-input input {
            padding-right: 30px;
        }
        .email-validation-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            display: none;
        }
        .email-validation-icon.valid {
            color: #28a745;
            display: block;
        }
        .email-validation-icon.invalid {
            color: #dc3545;
            display: block;
        }
        .email-error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .email-input.error input {
            border-color: #dc3545;
        }
        .email-input.valid input {
            border-color: #28a745;
        }
        
        /* Additional Select2 styling for better search visibility */
        .select2-container--classic .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 8px;
            font-size: 16px;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .select2-container--classic .select2-search--dropdown .select2-search__field:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .select2-container--classic .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
        }
        
        .select2-container--classic .select2-results__option {
            padding: 8px 12px;
        }
        
        .help-text {
            display: block;
            margin-top: 5px;
            font-size: 0.85em;
            color: #6c757d;
        }
        
        /* DOB validation styles */
        .dob-input {
            position: relative;
        }
        .dob-validation-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            display: none;
        }
        .dob-validation-icon.valid {
            color: #28a745;
            display: block;
        }
        .dob-validation-icon.invalid {
            color: #dc3545;
            display: block;
        }
        .dob-error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .dob-input.error input {
            border-color: #dc3545;
        }
        .dob-input.valid input {
            border-color: #28a745;
        }
        
        /* Password validation styles */
        .password-container {
            position: relative;
        }
        .password-strength-meter {
            height: 5px;
            background-color: #eee;
            margin-top: 10px;
            border-radius: 3px;
            overflow: hidden;
        }
        .password-strength-meter .meter {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .password-strength-meter .meter.weak {
            width: 33.33%;
            background-color: #dc3545;
        }
        .password-strength-meter .meter.medium {
            width: 66.66%;
            background-color: #ffc107;
        }
        .password-strength-meter .meter.strong {
            width: 100%;
            background-color: #28a745;
        }
        .password-requirements {
            margin-top: 10px;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .password-requirements ul {
            list-style: none;
            padding-left: 0;
            margin: 5px 0;
        }
        .password-requirements li {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }
        .password-requirements li::before {
            content: '✕';
            color: #dc3545;
            margin-right: 8px;
            font-size: 0.875rem;
        }
        .password-requirements li.valid::before {
            content: '✓';
            color: #28a745;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            font-size: 1rem;
        }
        .password-toggle:hover {
            color: #495057;
        }
    </style>
</head>
<body>
    <!-- Add navigation bar -->
    <nav class="navbar">
        <div class="logo">
            <a href="../../index.html" class="home-link">
                <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
                <span>Alumni Network</span>
            </a>
        </div>
    </nav>

    <div class="register-container">
        <div class="registration-form">
            <!-- Progress Bar and Indicators -->
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
            <div class="step-indicators">
                <div class="step-indicator active"></div>
                <div class="step-indicator"></div>
                <div class="step-indicator"></div>
                <div class="step-indicator"></div>
                <div class="step-indicator"></div>
                <div class="step-indicator"></div>
                
            </div>

            <!-- Form Steps -->
            <form id="registrationForm" method="POST" action="process_registration.php" enctype="multipart/form-data">
                <!-- Step 1 -->
                <div class="form-step" id="step1" style="display: block;">
                    <h2>Basic Information</h2>
                    <div class="form-group">
                        <label for="fullname">Full Name*</label>
                        <input type="text" id="fullname" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address*</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number*</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth*</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password*</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="password-strength-meter">
                        <div class="meter"></div>
                    </div>
                    <button type="button" id="step1Next" class="next-btn">Next</button>
                </div>

                <!-- Step 2 -->
                <div class="form-step" id="step2" style="display: none;">
                    <h2>Educational Details</h2>
                    <input type="hidden" id="university_name" name="university_name" value="Uka Tarsadia University">
                    <div class="form-group">
                        <label for="course">Course*</label>
                        <select id="course" name="course" required>
                            <option value="">Select Course</option>
                            <option value="BCA">BCA</option>
                            <option value="MCA">MCA</option>
                        </select>
                    </div>
                    <div class="form-group enrollment-format-group">
                        <label>Enrollment Number Format*</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="format_old" name="enrollment_format" value="old" checked>
                                <label for="format_old">Before 2011</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="format_new" name="enrollment_format" value="new">
                                <label for="format_new">After 2011</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="old_format_group">
                        <label for="enrollment_number_old">Old Enrollment Number (YY|Course|Number)*</label>
                        <input type="text" id="enrollment_number_old" name="enrollment_number_old" placeholder="YY|Course|Number" pattern="[0-9]{2}(BCA|MCA)[0-9]{3}" title="Please enter your enrollment number in the format YY|Course|Number" maxlength="11" required>
                        <small class="help-text">Enter your enrollment number in the format YY|Course|Number</small>
                    </div>
                    <div class="form-group" id="new_format_group" style="display: none;">
                        <label for="enrollment_number">New Enrollment Number*</label>
                        <input type="text" id="enrollment_number" name="enrollment_number" pattern="[0-9]{15}" title="Please enter your 15-digit enrollment number" placeholder="Enter your 15-digit enrollment number" maxlength="15">
                        <small class="help-text">Enter your 15-digit enrollment number</small>
                    </div>
                    
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step2Prev">Previous</button>
                        <button type="button" class="next-btn" id="step2Next">Next</button>
                    </div>
                </div>

                <!-- Step 3: Professional Status -->
                <div class="form-step" id="step3" style="display: none;">
                    <h2>Current Status</h2>
                    <div class="form-group">
                        <label for="current_status">Current Professional Status*</label>
                        <select id="current_status" name="current_status" required onchange="toggleEmploymentFields()">
                            <option value="">Select your status</option>
                            <option value="employed">Employed</option>
                            <option value="seeking">Seeking Opportunities</option>
                            <option value="student">Further Studies</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div id="employment-fields" style="display: none;">
                        <div class="form-group">
                            <label for="company_name">Company Name*</label>
                            <input type="text" id="company_name" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="position">Position*</label>
                            <input type="text" id="position" name="position">
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step3Prev">Previous</button>
                        <button type="button" class="next-btn" id="step3Next">Next</button>
                    </div>
                </div>

                <!-- Step 4: Project Question -->
                <div class="form-step" id="step4" style="display: none;">
                    <h2>Would you happen to have any completed projects?</h2>
                    <div class="form-group">
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="hasProjectsYes" name="hasProjects" value="yes">
                                <label for="hasProjectsYes">Yes</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="hasProjectsNo" name="hasProjects" value="no" checked>
                                <label for="hasProjectsNo">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- Removed project count input -->
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step4Prev">Previous</button>
                        <button type="button" class="next-btn" id="step4Next">Next</button>
                    </div>
                </div>

                <!-- Step 5: Projects -->
                <div class="form-step" id="step5" style="display: none;">
                    <h2>Projects</h2>
                    <div id="projects-container">
                        <!-- Initial Project Entry -->
                        <div class="project-entry">
                             <h4>Project 1</h4>
                             <div class="form-group">
                                 <label for="project_title_0">Project Title*</label>
                                 <input type="text" id="project_title_0" name="projects[0][title]" required>
                             </div>
                             <div class="form-group">
                                 <label for="project_description_0">Description*</label>
                                 <textarea id="project_description_0" name="projects[0][description]" rows="3" required></textarea>
                             </div>
                             <div class="form-group">
                                 <label for="project_technologies_0">Technologies Used*</label>
                                 <input type="text" id="project_technologies_0" name="projects[0][technologies]"
                                        placeholder="e.g., PHP, MySQL, JavaScript" required>
                             </div>
                             <!-- Remove button will be added dynamically for subsequent projects -->
                        </div>
                    </div>
                    <button type="button" id="add-project" class="btn-secondary">Add Another Project</button>
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step5Prev">Previous</button>
                        <button type="button" class="next-btn" id="step5Next">Next</button>
                    </div>
                </div>

                <!-- Step 6: Skills -->
                <div class="form-step" id="step6" style="display: none;">
                    <h2>Language Specialization, Tools & Technologies</h2>
                    <div id="skills-container">
                        <div class="skill-entry">
                            <div class="form-group">
                                <label for="language_specialization">Language Specialization*</label>
                                <select class="select2-multiple" name="skills[language][]" multiple="multiple" required>
                                    <!-- Options will be populated via JavaScript -->
                                </select>
                                <small class="help-text">Type to search or add custom languages</small>
                                <div id="other_language_container" style="display: none; margin-top: 10px;">
                                    <input type="text" id="other_language" name="skills[other_language]" placeholder="Enter other language specialization">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tools">Tools*</label>
                                <select class="select2-multiple" name="skills[tools][]" multiple="multiple" required>
                                    <!-- Options will be populated via JavaScript -->
                                </select>
                                <div id="other_tools_container" style="display: none; margin-top: 10px;">
                                    <input type="text" id="other_tools" name="skills[other_tools]" placeholder="Enter other tools">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="technologies">Technologies*</label>
                                <select class="select2-multiple" name="skills[technologies][]" multiple="multiple" required>
                                    <!-- Options will be populated via JavaScript -->
                                </select>
                                <div id="other_technologies_container" style="display: none; margin-top: 10px;">
                                    <input type="text" id="other_technologies" name="skills[other_technologies]" placeholder="Enter other technologies">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="skill_level">Overall Proficiency Level*</label>
                                <select name="skills[level]" required>
                                    <option value="">Select Level</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step6Prev">Previous</button>
                        <button type="button" class="next-btn" id="step6Next">Next</button>
                    </div>
                </div>

                <!-- Step 7: Career Goals & Certifications -->
                <div class="form-step" id="step7" style="display: none;">
                    <h2>Career Goals</h2>
                    <div id="goals-container">
                        <div class="goal-entry">
                            <div class="form-group">
                                <label for="goal_description">Career Goal*</label>
                                <textarea name="career_goals[description]" rows="3" required
                                          placeholder="Describe your career goal"></textarea>
                            </div>
                        </div>
                    </div>

                    <h2>Certifications</h2>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Note: You can add more certificates and update your profile information later through your dashboard.
                    </div>
                    <div id="certifications-container">
                        <div class="certification-entry">
                            <div class="form-group">
                                <label for="cert_file">Upload Certificate (Optional) (PDF, JPG, JPEG, PNG)</label>
                                <input type="file" name="certifications[0][certificate_file]" accept=".pdf, .jpg, .jpeg, .png">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-certification" class="btn-secondary">Add Another Certificate</button>

                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step7Prev">Previous</button>
                        <button type="submit" class="submit-btn" id="submitForm">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="../../assets/js/register.js"></script> <!-- Added script tag -->

    <script>
    // Phone validation
    const phoneInput = document.getElementById('phone');
    const phoneContainer = phoneInput.parentElement;
    const phoneIcon = document.createElement('span');
    phoneIcon.className = 'validation-icon';
    phoneContainer.appendChild(phoneIcon);

    const phoneError = document.createElement('div');
    phoneError.className = 'error-message';
    phoneContainer.appendChild(phoneError);

    phoneInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, '');
        if (value.length > 10) {
            this.value = value.slice(0, 10);
        }
        
        if (!this.value) {
            phoneContainer.classList.remove('valid', 'error');
            phoneIcon.className = 'validation-icon';
            phoneError.style.display = 'none';
        } else if (value.length === 10) {
            phoneContainer.classList.add('valid');
            phoneContainer.classList.remove('error');
            phoneIcon.className = 'validation-icon valid';
            phoneIcon.innerHTML = '✓';
            phoneError.style.display = 'none';
        } else {
            phoneContainer.classList.add('error');
            phoneContainer.classList.remove('valid');
            phoneIcon.className = 'validation-icon invalid';
            phoneIcon.innerHTML = '✕';
            phoneError.textContent = 'Please enter exactly 10 digits';
            phoneError.style.display = 'block';
        }
    });

    // Email validation
    const emailInput = document.getElementById('email');
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
        
        if (email === '') {
            emailContainer.classList.remove('valid', 'error');
            emailIcon.className = 'validation-icon';
            emailError.style.display = 'none';
        } else if (emailRegex.test(email)) {
            emailContainer.classList.add('valid');
            emailContainer.classList.remove('error');
            emailIcon.className = 'validation-icon valid';
            emailIcon.innerHTML = '✓';
            emailError.style.display = 'none';
        } else {
            emailContainer.classList.add('error');
            emailContainer.classList.remove('valid');
            emailIcon.className = 'validation-icon invalid';
            emailIcon.innerHTML = '✕';
            emailError.style.display = 'block';
        }
    });

    // DOB validation
    const dobInput = document.getElementById('dob');
    const dobContainer = dobInput.parentElement;
    const dobIcon = document.createElement('span');
    dobIcon.className = 'validation-icon';
    dobContainer.appendChild(dobIcon);

    const dobError = document.createElement('div');
    dobError.className = 'error-message';
    dobContainer.appendChild(dobError);

    function validateDOB() {
        const dob = new Date(dobInput.value);
        const today = new Date();
        
        // Reset validation state
        dobContainer.classList.remove('valid', 'error');
        dobIcon.className = 'validation-icon';
        dobError.style.display = 'none';

        // Check if date is empty
        if (!dobInput.value) {
            return;
        }

        // Check if date is valid
        if (isNaN(dob.getTime())) {
            dobContainer.classList.add('error');
            dobIcon.className = 'validation-icon invalid';
            dobIcon.innerHTML = '✕';
            dobError.textContent = 'Please enter a valid date';
            dobError.style.display = 'block';
            return;
        }

        // Check if date is in the future
        if (dob > today) {
            dobContainer.classList.add('error');
            dobIcon.className = 'validation-icon invalid';
            dobIcon.innerHTML = '✕';
            dobError.textContent = 'Date of birth cannot be in the future';
            dobError.style.display = 'block';
            return;
        }

        // Calculate age
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        // Adjust age if birthday hasn't occurred this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        // Set minimum and maximum age limits
        const minAge = 16;
        const maxAge = 100;

        // Validate age limits
        if (age < minAge) {
            dobContainer.classList.add('error');
            dobIcon.className = 'validation-icon invalid';
            dobIcon.innerHTML = '✕';
            dobError.textContent = `You must be at least ${minAge} years old`;
            dobError.style.display = 'block';
        } else if (age > maxAge) {
            dobContainer.classList.add('error');
            dobIcon.className = 'validation-icon invalid';
            dobIcon.innerHTML = '✕';
            dobError.textContent = `Age cannot be more than ${maxAge} years`;
            dobError.style.display = 'block';
        } else {
            dobContainer.classList.add('valid');
            dobIcon.className = 'validation-icon valid';
            dobIcon.innerHTML = '✓';
        }
    }

    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    dobInput.setAttribute('max', today);

    // Validate on all relevant events
    dobInput.addEventListener('input', validateDOB);
    dobInput.addEventListener('change', validateDOB);
    dobInput.addEventListener('blur', validateDOB);
    dobInput.addEventListener('keyup', validateDOB);

    // Prevent manual entry of future dates
    dobInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            validateDOB();
        }
    });

    // Initial validation
    validateDOB();

    // Handle "Other" option in specialization dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Language Specialization
        $('select[name="skills[language][]"]').on('change', function() {
            const selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.includes('other')) {
                $('#other_language_container').show();
                // Make the other language input required when "Other" is selected
                $('#other_language').prop('required', true);
            } else {
                $('#other_language_container').hide();
                // Remove required attribute when "Other" is not selected
                $('#other_language').prop('required', false);
            }
        });

        // Tools
        $('select[name="skills[tools][]"]').on('change', function() {
            const selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.includes('other')) {
                $('#other_tools_container').show();
                // Make the other tools input required when "Other" is selected
                $('#other_tools').prop('required', true);
            } else {
                $('#other_tools_container').hide();
                // Remove required attribute when "Other" is not selected
                $('#other_tools').prop('required', false);
            }
        });

        // Technologies
        $('select[name="skills[technologies][]"]').on('change', function() {
            const selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.includes('other')) {
                $('#other_technologies_container').show();
                // Make the other technologies input required when "Other" is selected
                $('#other_technologies').prop('required', true);
            } else {
                $('#other_technologies_container').hide();
                // Remove required attribute when "Other" is not selected
                $('#other_technologies').prop('required', false);
            }
        });

        // Add "Other" option to all specialization dropdowns
        function addOtherOption(selectElement) {
            // Check if "Other" option already exists
            let hasOtherOption = false;
            $(selectElement).find('option').each(function() {
                if ($(this).val() === 'other') {
                    hasOtherOption = true;
                    return false; // Break the loop
                }
            });

            // Add "Other" option if it doesn't exist
            if (!hasOtherOption) {
                $(selectElement).append(new Option('Other', 'other'));
            }
        }

        // Add "Other" option to all specialization dropdowns
        addOtherOption('select[name="skills[language][]"]');
        addOtherOption('select[name="skills[tools][]"]');
        addOtherOption('select[name="skills[technologies][]"]');

        // Form submission handling
        $('#registrationForm').on('submit', function(e) {
            // Check if "Other" is selected but no value is provided
            const languageSelected = $('select[name="skills[language][]"]').val();
            const toolsSelected = $('select[name="skills[tools][]"]').val();
            const technologiesSelected = $('select[name="skills[technologies][]"]').val();
            
            let isValid = true;
            
            if (languageSelected && languageSelected.includes('other') && !$('#other_language').val()) {
                alert('Please enter your other language specialization');
                isValid = false;
            }
            
            if (toolsSelected && toolsSelected.includes('other') && !$('#other_tools').val()) {
                alert('Please enter your other tools');
                isValid = false;
            }
            
            if (technologiesSelected && technologiesSelected.includes('other') && !$('#other_technologies').val()) {
                alert('Please enter your other technologies');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Password validation
    const passwordInput = document.getElementById('password');
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

    // Password validation function
    function validatePassword() {
        const password = passwordInput.value;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Update requirements list
        Object.keys(requirements).forEach(req => {
            const element = document.querySelector(`[data-requirement="${req}"]`);
            if (requirements[req]) {
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
            }
        });

        // Calculate password strength
        const strength = Object.values(requirements).filter(Boolean).length;
        meter.className = 'meter';
        
        if (password.length === 0) {
            meter.style.width = '0';
            passwordContainer.classList.remove('valid', 'error');
            passwordIcon.className = 'validation-icon';
        } else if (strength <= 2) {
            meter.classList.add('weak');
            passwordContainer.classList.add('error');
            passwordContainer.classList.remove('valid');
            passwordIcon.className = 'validation-icon invalid';
            passwordIcon.innerHTML = '✕';
        } else if (strength <= 4) {
            meter.classList.add('medium');
            passwordContainer.classList.add('error');
            passwordContainer.classList.remove('valid');
            passwordIcon.className = 'validation-icon invalid';
            passwordIcon.innerHTML = '✕';
        } else {
            meter.classList.add('strong');
            passwordContainer.classList.add('valid');
            passwordContainer.classList.remove('error');
            passwordIcon.className = 'validation-icon valid';
            passwordIcon.innerHTML = '✓';
        }
    }

    // Toggle password visibility
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '👁️' : '👁️‍🗨️';
    });

    // Validate password on input
    passwordInput.addEventListener('input', validatePassword);
    </script>

    <!-- Add preview for uploaded certificates -->
    <div class="certificate-preview"></div>
</body>
</html>
