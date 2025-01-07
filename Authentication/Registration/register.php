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
                            <option value="">Select University</option>
                            <option value="">Select University</option>
                                <?php
                                // Database connection
                                $conn = new mysqli("localhost", "root", "", "alumni_network");
                                
                                // Check connection
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                
                                // Fetch universities
                                $sql = "SELECT university_id, university_name FROM universities";
                                $result = $conn->query($sql);
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['university_id'] . "'>" . htmlspecialchars($row['university_name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No universities available</option>";
                                }
                                
                                // Close connection
                                $conn->close();
                                ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="enrollment">Enrollment Number*</label>
                        <input type="text" id="enrollment" name="enrollment" required>
                    </div>
                    <div class="form-group">
                        <label for="graduation_year">Graduation Year*</label>
                        <select id="graduation_year" name="graduation_year" required>
                            <?php
                            $current_year = date('Y');
                            for($year = $current_year; $year >= $current_year - 50; $year--) {
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
                        <select id="current_status" name="current_status" required>
                            <option value="employed">Employed</option>
                            <option value="seeking">Seeking Opportunities</option>
                            <option value="student">Further Studies</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="current_company">Current Company</label>
                        <input type="text" id="current_company" name="current_company" 
                               disabled required>
                    </div>
                    <div class="form-group">
                        <label for="current_position">Current Position</label>
                        <input type="text" id="current_position" name="current_position" 
                               disabled required>
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
    <div class="form-group">
        <label>Short-term Goal (1 year)*</label>
        <textarea name="goals[short_term]" required></textarea>
    </div>
    <div class="form-group">
        <label>Mid-term Goal (3 years)*</label>
        <textarea name="goals[mid_term]" required></textarea>
    </div>
    <div class="form-group">
        <label>Long-term Goal (5+ years)*</label>
        <textarea name="goals[long_term]" required></textarea>
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(6)">Previous</button>
    <button type="submit" class="submit-btn">Complete Registration</button>
</div>

                <!-- Add login link at the bottom of the form -->
                <div class="form-links">
                    <span>Already have an account? <a href="../Login/login.php">Login here</a></span>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/register.js"></script>
</body>
</html>
