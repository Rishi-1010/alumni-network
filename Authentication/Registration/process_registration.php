<?php
session_start();
require_once '../../config/db_connection.php';

// Initialize Database
$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Database connection failed.");
}

// Capture POST data
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$university = $_POST['university'];
$enrollment = $_POST['enrollment'];
$graduation_year = $_POST['graduation_year'];
$status = $_POST['current_status'] ?? 'seeking'; // Default to 'seeking' if no status is provided
$company = $_POST['current_company'] ?? null;
$position = $_POST['current_position'] ?? null;

// Capture additional data
$projects = $_POST['projects'] ?? [];
$skills = $_POST['skills'] ?? [];
$careerGoals = $_POST['career_goals'] ?? [];
$certifications = $_POST['certifications'] ?? [];

// Validate if `current_status` is not null or empty
if (empty($status)) {
    $status = 'seeking'; // Ensure a valid status is set
}

try {
    // Check for duplicate enrollment
    $stmt = $conn->prepare("SELECT COUNT(*) FROM educational_details WHERE enrollment_number = ?");
    $stmt->execute([$_POST['enrollment']]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("This enrollment number is already registered.");
    }

    // Register User
    $result = $db->registerUser($fullname, $email, $phone, $password);

    if ($result['status'] === 'success') {
        $user_id = $result['user_id'];

        // Add Educational Details
        $educationResult = $db->addEducation($user_id, $university, $enrollment, $graduation_year);

        if ($educationResult['status'] === 'success') {
            // Update Professional Status
            $statusResult = $db->updateStatus($user_id, $status, $company, $position);

            if ($statusResult['status'] === 'success') {
                // Insert Projects
                foreach ($projects as $project) {
                    $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, start_date, end_date, technologies_used) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $project['title'], $project['description'], $project['start_date'], $project['end_date'], $project['technologies']]);
                }

                // Insert Skills
                foreach ($skills as $skill) {
                    $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, proficiency_level) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $skill['name'], $skill['level']]);
                }

                // Insert Career Goals
                foreach ($careerGoals as $goal) {
                    $stmt = $conn->prepare("INSERT INTO career_goals (user_id, goal_year, description, target_date, status) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $goal['year'], $goal['description'], $goal['target_date'], $goal['status']]);
                }

                // Insert Certifications
                foreach ($certifications as $cert) {
                    $stmt = $conn->prepare("
                        INSERT INTO certifications (
                            user_id, 
                            title, 
                            issuing_organization, 
                            issue_date, 
                            expiry_date,
                            credential_id,
                            credential_url
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $user_id,
                        $cert['title'],
                        $cert['issuing_organization'],
                        $cert['issue_date'],
                        $cert['expiry_date'],
                        $cert['credential_id'],
                        $cert['credential_url']
                    ]);
                }

                $_SESSION['success'] = "Registration completed successfully!";
                header("Location: ../login/login.php");
                exit();
            } else {
                throw new Exception($statusResult['message']);
            }
        } else {
            throw new Exception($educationResult['message']);
        }
    } else {
        throw new Exception($result['message']);
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error occurred: " . $e->getMessage();
    header("Location: register.php");
    exit();
}
