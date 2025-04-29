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
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
        if (this.files[0].size > MAX_FILE_SIZE) {
            alert('File size must be less than 5MB');
            this.value = '';
        }
    });

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
    </script>

    <!-- Add preview for uploaded certificates -->
    <div class="certificate-preview"></div>
</body>
</html>
