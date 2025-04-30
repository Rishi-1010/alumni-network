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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="register-page">
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
            <!-- New Steps Flow -->
            <div class="steps-flow">
                <div class="step-line"></div>
                <div class="step-progress"></div>
                
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-title">Basic Info</div>
                </div>
                
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-title">Education</div>
                </div>
                
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-title">Status</div>
                </div>
                
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-title">Projects?</div>
                </div>
                
                <div class="step project-dependent" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-title">Projects</div>
                </div>
                
                <div class="step" data-step="6">
                    <div class="step-circle skills-step-number">6</div>
                    <div class="step-title">Skills</div>
                </div>
                
                <div class="step" data-step="7">
                    <div class="step-circle goals-step-number">7</div>
                    <div class="step-title">Goals</div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="form-container">
            <form id="registrationForm" method="POST" action="process_registration.php" enctype="multipart/form-data">
                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" id="step1">
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
                    <div class="password-strength-meter">
                        <div class="meter"></div>
                    </div>
                        </div>
                        
                        <div class="step-navigation">
                            <button type="button" class="nav-button next" id="step1Next">
                                Next
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
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
                                    <input type="radio" id="format_old" name="enrollment_format" value="old">
                                <label for="format_old">Before 2011</label>
                            </div>
                            <div class="radio-item">
                                    <input type="radio" id="format_new" name="enrollment_format" value="new" checked>
                                <label for="format_new">After 2011</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="old_format_group" style="display: none;">
                        <label for="enrollment_number_old">Old Enrollment Number (YY|Course|Number)*</label>
                            <input type="text" 
                                   id="enrollment_number_old" 
                                   name="enrollment_number_old" 
                                   placeholder="e.g., 10BCA16 or 07BCA015" 
                                   pattern="[0-9]{2}(BCA|MCA)([1-9][0-9]|[1-9][0-9]{2})" 
                                   title="Please enter your enrollment number in the format YY|Course|Number." 
                                   maxlength="8" 
                                   required>
                    </div>
                        <div class="form-group" id="new_format_group">
                        <label for="enrollment_number">New Enrollment Number*</label>
                            <input type="text" 
                                   id="enrollment_number" 
                                   name="enrollment_number" 
                                   pattern="\d{15}" 
                                   title="Please enter your 15-digit enrollment number" 
                                   placeholder="Enter your 15-digit enrollment number" 
                                   maxlength="15" 
                                   required>
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
                            <option value="freelancer">Freelancer</option>
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

                    <div id="freelancer-fields" style="display: none;">
                        <div class="form-group">
                            <label for="freelance_title">Professional Title*</label>
                            <input type="text" id="freelance_title" name="freelance_title" placeholder="e.g., Full Stack Developer, UI/UX Designer">
                        </div>
                        <div class="form-group">
                            <label for="platforms">Freelancing Platforms*</label>
                            <select id="platforms" name="platforms[]" multiple class="select2-multiple">
                                <option value="upwork">Upwork</option>
                                <option value="fiverr">Fiverr</option>
                                <option value="freelancer">Freelancer.com</option>
                                <option value="toptal">Toptal</option>
                                <option value="guru">Guru</option>
                                <option value="independent">Independent/Direct Clients</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expertise_areas">Areas of Expertise*</label>
                            <select id="expertise_areas" name="expertise_areas[]" multiple class="select2-multiple">
                                <option value="web_development">Web Development</option>
                                <option value="mobile_development">Mobile Development</option>
                                <option value="ui_ux">UI/UX Design</option>
                                <option value="data_science">Data Science</option>
                                <option value="digital_marketing">Digital Marketing</option>
                                <option value="content_writing">Content Writing</option>
                                <option value="graphic_design">Graphic Design</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="experience_years">Years of Freelancing Experience*</label>
                            <select id="experience_years" name="experience_years" required>
                                <option value="">Select Experience</option>
                                <option value="less_than_1">Less than 1 year</option>
                                <option value="1_2">1-2 years</option>
                                <option value="2_3">2-3 years</option>
                                <option value="3_5">3-5 years</option>
                                <option value="more_than_5">More than 5 years</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button type="button" class="prev-btn" id="step3Prev">Previous</button>
                        <button type="button" class="next-btn" id="step3Next">Next</button>
                    </div>
                </div>

                <!-- Step 4: Project Question -->
                <div class="form-step" id="step4" style="display: none;">
                    <h2>Do you have any completed projects you'd like to share with the system?</h2>
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
                            </div>
                            <div class="form-group">
                                <label for="tools">Tools*</label>
                                <select class="select2-multiple" name="skills[tools][]" multiple="multiple" required>
                                    <!-- Options will be populated via JavaScript -->
                                </select>
                                <small class="help-text">Type to search or add custom tools</small>
                            </div>
                            <div class="form-group">
                                <label for="technologies">Technologies*</label>
                                <select class="select2-multiple" name="skills[technologies][]" multiple="multiple" required>
                                    <!-- Options will be populated via JavaScript -->
                                </select>
                                <small class="help-text">Type to search or add custom technologies</small>
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
    </div>

    <script src="../../assets/js/register.js"></script>
    <div class="certificate-preview"></div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">✓</div>
            <h2>Registration Successful!</h2>
            <p>You will be redirected to the login page in <span id="countdown">5</span> seconds...</p>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-icon {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 1rem;
        }

        .modal h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .modal p {
            color: #666;
            font-size: 1.1rem;
        }

        #countdown {
            font-weight: bold;
            color: #4CAF50;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</body>
</html>
