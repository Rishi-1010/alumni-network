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
                    <h2>Do you have any projects?</h2>
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
                                <input type="text" name="skills[language]" required 
                                       placeholder="e.g., Java, Python">
                            </div>
                            <div class="form-group">
                                <label for="tools">Tools*</label>
                                <input type="text" name="skills[tools]" required 
                                       placeholder="e.g., Git, Docker">
                            </div>
                            <div class="form-group">
                                <label for="technologies">Technologies*</label>
                                <input type="text" name="skills[technologies]" required 
                                       placeholder="e.g., React, Node.js">
                            </div>
                            <div class="form-group">
                                <label for="skill_level">Proficiency Level*</label>
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
                                <label for="cert_file">Upload Certificate* (PDF, JPG, JPEG, PNG)</label>
                                <input type="file" name="certifications[0][certificate_file]" accept=".pdf, .jpg, .jpeg, .png" required>
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
</body>
</html>
