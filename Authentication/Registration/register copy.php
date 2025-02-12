<?php
session_start();
require_once '../../config/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Registration</title>
    <link rel="stylesheet" href="../../assets/css/register.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
        <div class="nav-links">
            <a href="../../index.html" class="home-btn">Home</a>
            <a href="../Login/login.php" class="login-btn">Login</a>
        </div>
    </nav>

    <div class="register-container">
        <div class="registration-form">
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

            <form id="registrationForm" class="registration-form" action="process_registration.php" method="POST">
                <!-- Step 1: Basic Information -->
<div class="form-step" id="step1">
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
        <label for="password">Password*</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(1)">Next</button>
</div>

<!-- Step 2: Educational Details -->
<div class="form-step" id="step2" style="display: none;">
    <h2>Educational Details</h2>
    <div class="form-group">
        <label for="university">University*</label>
        <select id="university" name="university" required>
            <option value="1" selected>Uka Tarsadia University</option>
        </select>
    </div>
    <div class="form-group">
        <label for="enrollment">Enrollment Number*</label>
        <input type="text" id="enrollment" name="enrollment" required>
    </div>
    <div class="form-group">
        <label for="graduation_year_1">Graduation Year*</label>
        <select id="graduation_year_1" name="graduation_year" required>
            <?php
            $current_year = date('Y');
            for ($year = $current_year; $year >= $current_year - 50; $year--) {
                echo "<option value='$year'>$year</option>";
            }
            ?>
        </select>
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(2)">Previous</button>
    <button type="button" class="next-btn" onclick="nextStep(2)">Next</button>
</div>


<!-- Step 3: Current Status -->
<div class="form-step" id="step3" style="display: none;">
    <h2>Current Status</h2>
    <div class="form-group">
        <label for="current_status">Current Professional Status*</label>
        <select id="current_status" name="current_status" required onchange="toggleEmploymentFields()">
            <option value="seeking">Seeking Opportunities</option>
            <option value="employed">Employed</option>
            <option value="student">Further Studies</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="current_company">Current Company</label>
        <input type="text" id="current_company" name="current_company" disabled required>
    </div>

    <div class="form-group">
        <label for="current_position">Current Position</label>
        <input type="text" id="current_position" name="current_position" disabled required>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(3)">Previous</button>
    <button type="button" class="next-btn" onclick="nextStep(3)">Next</button>
</div>
                <!-- Step 4: Projects -->
                <div class="form-step" id="step4" style="display: none;">
                    <h3>Projects</h3>
                    <div id="projects-container">
                        <div class="project-entry">
                            <div class="form-group">
                                <label for="project_title">Project Title*</label>
                                <input type="text" name="projects[0][title]" required>
                            </div>
                            <div class="form-group">
                                <label for="project_description">Description*</label>
                                <textarea name="projects[0][description]" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="technologies">Technologies Used*</label>
                                <input type="text" name="projects[0][technologies]" placeholder="e.g., PHP, MySQL, JavaScript" required>
                            </div>
                            <div class="form-group date-range">
                                <div>
                                    <label>Start Date*</label>
                                    <input type="date" name="projects[0][start_date]" required>
                                </div>
                                <div>
                                    <label>End Date</label>
                                    <input type="date" name="projects[0][end_date]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-project" class="btn-secondary">Add Another Project</button>
                    <button type="button" class="prev-btn" onclick="prevStep(4)">Previous</button>
                    <button type="button" class="next-btn" onclick="nextStep(4)">Next</button>
                </div>

                <!-- Step 5: Skills & Certifications -->
                <div class="form-step" id="step5" style="display: none;">
                    <h3>Skills & Certifications</h3>
                    <div id="skills-container">
                        <div class="skill-entry">
                            <div class="form-group">
                                <label>Skill Name*</label>
                                <input type="text" name="skills[0][name]" required>
                            </div>
                            <div class="form-group">
                                <label>Proficiency Level*</label>
                                <select name="skills[0][level]" required>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-skill" class="btn-secondary">Add Another Skill</button>
                    <button type="button" class="prev-btn" onclick="prevStep(5)">Previous</button>
                    <button type="button" class="next-btn" onclick="nextStep(5)">Next</button>
                </div>

                

                <!-- Step 6: Career Goals -->
                <div class="form-step" id="step6" style="display: none;">
                    <h3>Career Goals</h3>
                    <div class="career-goals-container">
                        <div class="career-goal-entry">
                            <div class="form-group">
                                <label for="goal_year">Goal Year*</label>
                                <input type="number" name="career_goals[0][year]" min="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="goal_description">Description*</label>
                                <textarea name="career_goals[0][description]" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="goal_target_date">Target Date</label>
                                <input type="date" name="career_goals[0][target_date]">
                            </div>
                            <div class="form-group">
                                <label for="goal_status">Status*</label>
                                <select name="career_goals[0][status]" required>
                                    <option value="planned">Planned</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="achieved">Achieved</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="prev-btn" onclick="prevStep(6)">Previous</button>
                    <button type="button" class="next-btn" onclick="nextStep(6)">Next</button>
                </div>

                <!-- Step 7: Certifications -->
                <div class="form-step" id="step7" style="display: none;">
                    <h3>Certifications</h3>
                    <div class="certification-container">
                        <div class="certification-entry">
                            <div class="form-group">
                                <label for="cert_title">Certification Title*</label>
                                <input type="text" name="certifications[0][title]" required>
                            </div>
                            <div class="form-group">
                                <label for="issuing_org">Issuing Organization*</label>
                                <input type="text" name="certifications[0][issuing_organization]" required>
                            </div>
                            <div class="form-group">
                                <label for="issue_date">Issue Date</label>
                                <input type="date" name="certifications[0][issue_date]">
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" name="certifications[0][expiry_date]">
                            </div>
                            <div class="form-group">
                                <label for="credential_id">Credential ID</label>
                                <input type="text" name="certifications[0][credential_id]">
                            </div>
                            <div class="form-group">
                                <label for="credential_url">Credential URL</label>
                                <input type="url" name="certifications[0][credential_url]" placeholder="https://example.com/credential">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="add-cert-btn" onclick="addCertification()">+ Add Another Certification</button>
                    <button type="button" class="prev-btn" onclick="prevStep(7)">Previous</button>
                    <button type="submit" class="submit-btn">Submit Registration</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/register.js"></script>
</body>
</html>
