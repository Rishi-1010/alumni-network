<?php
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
$status = $_POST['current_status'];
$company = $_POST['current_company'] ?? null;
$position = $_POST['current_position'] ?? null;

try {
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
                $_SESSION['success'] = "Registration completed successfully!";
                header("Location: ../../dashboard.php");
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
