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
    <div class="register-container">
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
                    <label for="current_company">Current Company (if employed)</label>
                    <input type="text" id="current_company" name="current_company">
                </div>
                <div class="form-group">
                    <label for="current_position">Current Position (if employed)</label>
                    <input type="text" id="current_position" name="current_position">
                </div>
                <button type="button" class="prev-btn" onclick="prevStep(3)">Previous</button>
                <button type="submit" class="submit-btn">Complete Registration</button>
            </div>
        </form>
    </div>

    <script src="../../assets/js/register.js"></script>
</body>
</html>
