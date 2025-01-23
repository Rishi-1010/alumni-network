<?php
// Prevent any HTML output from error messages
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Ensure we're outputting JSON
header('Content-Type: application/json');

session_start();
require_once '../../config/db_connection.php';

try {
    // Initialize Database
    $db = new Database();
    $conn = $db->connect();

    // Basic validation
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Validate mandatory fields
    $requiredFields = ['fullname', 'email', 'phone', 'password', 'enrollment_number', 'graduation_year'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate phone number (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
        throw new Exception('Invalid phone number');
    }

    // Check if user exists (using correct column name)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('User with this email already exists');
    }

    // Check enrollment number separately
    $stmt = $conn->prepare("SELECT user_id FROM educational_details WHERE enrollment_number = ?");
    $stmt->execute([$_POST['enrollment_number']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('User with this enrollment number already exists');
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Insert User Basic Details (removing created_at as it's not in the schema)
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password) 
                               VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            $_POST['fullname'],
            $_POST['email'],
            $_POST['phone'],
            $hashedPassword
        ]);
        $userId = $conn->lastInsertId();

        // Insert Educational Details
        $stmt = $conn->prepare("INSERT INTO educational_details 
                               (user_id, university_name, enrollment_number, graduation_year) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            'Uka Tarsadia University',
            $_POST['enrollment_number'],
            $_POST['graduation_year']
        ]);

        // Insert Professional Status (using correct table name and columns)
        if (!empty($_POST['current_status'])) {
            $stmt = $conn->prepare("INSERT INTO professional_status 
                                   (user_id, current_status, company_name, position, start_date, is_current) 
                                   VALUES (?, ?, ?, ?, ?, TRUE)");
            $stmt->execute([
                $userId,
                $_POST['current_status'],
                $_POST['company_name'] ?? null,
                $_POST['position'] ?? null,
                $_POST['start_date'] ?? null
            ]);
        }

        // Insert Projects (using correct column names)
        if (!empty($_POST['projects'])) {
            $stmt = $conn->prepare("INSERT INTO projects 
                                   (user_id, title, description, technologies_used, start_date, end_date) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($_POST['projects'] as $project) {
                $stmt->execute([
                    $userId,
                    $project['title'],
                    $project['description'],
                    $project['technologies'],
                    $project['start_date'],
                    $project['end_date'] ?? null
                ]);
            }
        }

        // Insert Skills (using correct column names)
        if (!empty($_POST['skills'])) {
            $stmt = $conn->prepare("INSERT INTO skills 
                                   (user_id, skill_name, proficiency_level) 
                                   VALUES (?, ?, ?)");
            foreach ($_POST['skills'] as $skill) {
                // Convert proficiency level to match ENUM values
                $proficiencyLevel = strtolower($skill['level']);
                $stmt->execute([
                    $userId,
                    $skill['name'],
                    $proficiencyLevel
                ]);
            }
        }

        // Insert Career Goals (using correct column names)
        if (!empty($_POST['career_goals'])) {
            $stmt = $conn->prepare("INSERT INTO career_goals 
                                   (user_id, goal_type, description, target_date, status) 
                                   VALUES (?, ?, ?, ?, ?)");
            foreach ($_POST['career_goals'] as $goal) {
                $stmt->execute([
                    $userId,
                    'career', // default type
                    $goal['description'],
                    date('Y-m-d', strtotime('+' . $goal['timeline'] . ' years')), // Convert timeline to target date
                    $goal['status']
                ]);
            }
        }

        // Insert Certifications (using correct column names)
        if (!empty($_POST['certifications'])) {
            $stmt = $conn->prepare("INSERT INTO certifications 
                                   (user_id, title, issuing_organization, issue_date, expiry_date, credential_id, credential_url) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($_POST['certifications'] as $cert) {
                $stmt->execute([
                    $userId,
                    $cert['title'],
                    $cert['issuing_organization'],
                    $cert['issue_date'],
                    $cert['expiry_date'] ?? null,
                    $cert['credential_id'] ?? null,
                    $cert['credential_url'] ?? null
                ]);
            }
        }

        // Commit transaction
        $conn->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration completed successfully!'
        ]);
        exit;

    } catch (Exception $e) {
        // Rollback transaction in case of error
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }

        // Log error
        error_log("Registration Error: " . $e->getMessage());

        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }

} catch (Exception $e) {
    // Rollback transaction in case of error
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }

    // Log error
    error_log("Registration Error: " . $e->getMessage());

    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
