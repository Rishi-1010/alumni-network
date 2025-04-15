<?php
// Enable error reporting - Keep this for now, can be changed later if needed
error_reporting(E_ALL);
ini_set('display_errors', 1); // Keep displaying errors for now

// Start output buffering
ob_start();

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

    // Determine enrollment number based on format
    $enrollmentNumber = $_POST['enrollment_format'] === 'old' ? $_POST['enrollment_number_old'] : $_POST['enrollment_number'];

    // Validate enrollment number
    if (empty($enrollmentNumber)) {
        throw new Exception("Enrollment number is required");
    }

    // Check enrollment number separately
    $stmt = $conn->prepare("SELECT user_id FROM educational_details WHERE enrollment_number = ?");
    $stmt->execute([$enrollmentNumber]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('User with this enrollment number already exists');
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Insert User Basic Details (Added dob)
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, dob, password)
                               VALUES (?, ?, ?, ?, ?)");
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt->execute([
            $_POST['fullname'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['dob'],
            $hashedPassword
        ]);
        $userId = $conn->lastInsertId();

        // Insert Skills
        if (!empty($_POST['skills'])) {
            $stmt_skill = $conn->prepare("INSERT INTO skills (user_id, language_specialization, tools, technologies, proficiency_level) VALUES (?, ?, ?, ?, ?)");
            foreach ($_POST['skills'] as $skill) {
                if (!empty($skill['language']) && !empty($skill['tools']) && !empty($skill['technologies']) && !empty($skill['level'])) {
                    $stmt_skill->execute([$userId, $skill['language'], $skill['tools'], $skill['technologies'], $skill['level']]);
                } else {
                    error_log("Skipping incomplete skill entry for user ID: $userId");
                }
            }
        }

        // Insert Educational Details
        $stmt = $conn->prepare("INSERT INTO educational_details
                               (user_id, university_name, enrollment_number, graduation_year)
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            'Uka Tarsadia University',
            $enrollmentNumber,
            null // Graduation year is not collected in the form
        ]);

        // Insert Projects (if any submitted)
        if (isset($_POST['hasProjects']) && $_POST['hasProjects'] === 'yes' && !empty($_POST['projects'])) {
            $stmt_proj = $conn->prepare("INSERT INTO projects (user_id, title, description, technologies_used) VALUES (?, ?, ?, ?)");
            foreach ($_POST['projects'] as $project) {
                // Basic validation for project fields within the loop
                if (!empty($project['title']) && !empty($project['description']) && !empty($project['technologies'])) {
                    $stmt_proj->execute([
                        $userId,
                        $project['title'],
                        $project['description'],
                        $project['technologies']
                    ]);
                } else {
                    error_log("Skipping incomplete project entry for user ID: $userId");
                }
            }
        }

        // Insert into certifications table
        // Handle Certificate Uploads

        if (!empty($_FILES['certifications'])) {
            // Get enrollment number
            $enrollmentNumber = $_POST['enrollment_format'] === 'old' ? $_POST['enrollment_number_old'] : $_POST['enrollment_number'];

            $target_dir = "../../assets/certificates/" . $enrollmentNumber . "/";

            // Ensure the target directory exists and is writable
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    throw new Exception("Failed to create certificate directory for enrollment number: " . $enrollmentNumber);
                }
            } elseif (!is_writable($target_dir)) {
                throw new Exception("Certificate directory is not writable for enrollment number: " . $enrollmentNumber);
            }

            $stmt_cert = $conn->prepare("INSERT INTO certifications (user_id, certificate_path) VALUES (?, ?)");

            // Directly access the nested array based on var_export structure
            // Check if the 'certificate_file' key exists in the sub-arrays and if the upload was successful
            if (isset($_FILES['certifications']['error']['certificate_file']) && $_FILES['certifications']['error']['certificate_file'] === UPLOAD_ERR_OK) {

                $name = $_FILES['certifications']['name']['certificate_file'];
                $tmp_name = $_FILES['certifications']['tmp_name']['certificate_file'];
                $size = $_FILES['certifications']['size']['certificate_file'];
                $error = $_FILES['certifications']['error']['certificate_file']; // Already checked this is UPLOAD_ERR_OK

                $original_name = basename($name);
                $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                // Sanitize the original filename (remove spaces and special characters)
                // Get the enrollment number to create the directory
                $enrollmentNumber = $_POST['enrollment_format'] === 'old' ? $_POST['enrollment_number_old'] : $_POST['enrollment_number'];
                $user_cert_dir = $target_dir . $enrollmentNumber . "/";

                // Determine the next certificate number
                $certificate_count = 1;
                $existing_files = glob($user_cert_dir . 'certificate*.{pdf,jpg,jpeg,png}', GLOB_BRACE);
                if ($existing_files) {
                    $certificate_count = count($existing_files) + 1;
                }

                $new_filename = "certificate" . $certificate_count . "." . $file_extension;
                $target_file = $target_dir . $new_filename;

                // Validate file type and size
                $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
                if (!in_array($file_extension, $allowed_types)) {
                    throw new Exception("Invalid file type for certificate: $original_name. Only PDF, JPG, JPEG, PNG allowed.");
                }
                // Increased size limit to 5MB, adjust as needed
                if ($size > 5000000) {
                    throw new Exception("Certificate file is too large: $original_name. Max size 5MB.");
                }

                // Move the uploaded file
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Insert relative file path into database
                    $relative_path = "assets/certificates/" . $enrollmentNumber . "/" . $new_filename; // Use the new filename
                    if (!$stmt_cert->execute([$userId, $relative_path])) {
                        // Explicitly check execute result and throw exception if false
                        $errorInfo = $stmt_cert->errorInfo();
                        throw new Exception("Failed to insert certificate path into database for $original_name. Error: " . ($errorInfo[2] ?? 'Unknown PDO error'));
                    }
                } else {
                    // Throw exception if upload fails, triggering transaction rollback
                    throw new Exception("Failed to upload certificate file: $original_name. Check directory permissions and PHP settings.");
                }
            } elseif (isset($_FILES['certifications']['error']['certificate_file']) && $_FILES['certifications']['error']['certificate_file'] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors specifically, ignore UPLOAD_ERR_NO_FILE
                 $original_name_for_error = isset($_FILES['certifications']['name']['certificate_file']) ? basename($_FILES['certifications']['name']['certificate_file']) : 'unknown file';
                 throw new Exception("Error uploading certificate file: $original_name_for_error. Error code: " . $_FILES['certifications']['error']['certificate_file']);
            } elseif (isset($_FILES['certifications']['error']['certificate_file']) && $_FILES['certifications']['error']['certificate_file'] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors specifically, ignore UPLOAD_ERR_NO_FILE
                 $original_name_for_error = isset($_FILES['certifications']['name']['certificate_file']) ? basename($_FILES['certifications']['name']['certificate_file']) : 'unknown file';
                 throw new Exception("Error uploading certificate file: $original_name_for_error. Error code: " . $_FILES['certifications']['error']['certificate_file']);
            }
            // It's okay if no file was uploaded (UPLOAD_ERR_NO_FILE)
        } // Closing brace for if (!empty($_FILES['certifications']))


        // Insert Skills
        if (!empty($_POST['skills'])) {
            $stmt_skill = $conn->prepare("INSERT INTO skills (user_id, skill_name, proficiency_level) VALUES (?, ?, ?)");
            foreach ($_POST['skills'] as $skill) {
                if (!empty($skill['name']) && !empty($skill['level'])) {
                    $stmt_skill->execute([$userId, $skill['name'], $skill['level']]);
                } else {
                    error_log("Skipping incomplete skill entry for user ID: $userId");
                }
            }
        }


        // Insert Career Goals
        if (!empty($_POST['career_goals'])) {
            $stmt_goal = $conn->prepare("INSERT INTO career_goals (user_id, description) VALUES (?, ?)");
            foreach ($_POST['career_goals'] as $goal) {
                if (!empty($goal['description'])) {
                    $stmt_goal->execute([$userId, $goal['description']]);
                } else {
                    error_log("Skipping empty career goal entry for user ID: $userId");
                }
            }
        }

        // Commit transaction
        $conn->commit();

        // Clear any buffered output before redirect
        ob_end_clean();

        // Redirect to contactus.php on success
        header('Location: ../../contactus.php'); // Adjust path as needed
        exit;

    } catch (Exception $e) {
        // Get any buffered output
        $output = ob_get_clean();

        // Rollback transaction in case of error
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }

        // Log error with detailed information
        $errorMessage = "Registration Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
        error_log($errorMessage); // Log to PHP error log

        // Also log to debug.log - Keep this for now
        $debugErrorMessage = "Caught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
        file_put_contents('debug.log', $debugErrorMessage, FILE_APPEND);


        // Return error response as JSON (keep this for debugging/potential future use)
        // Ensure output buffer is cleaned before sending JSON error
        if (ob_get_level() > 0) {
             ob_end_clean();
        }
        header('Content-Type: application/json'); // Ensure header is set for JSON
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'output' => $output // Include buffered output in error response
        ]);
        exit;
    }
} catch (Exception $e) {
     // Get any buffered output if available
     if (ob_get_level() > 0) {
        $output = ob_get_clean();
     } else {
        $output = '';
     }

    // Rollback transaction in case of error outside the inner try-catch
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }

    // Log error with detailed information
    $errorMessage = "Outer Catch Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    error_log($errorMessage); // Log to PHP error log

    // Also log to debug.log - Keep this for now
    $debugErrorMessage = "Caught Outer Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
    file_put_contents('debug.log', $debugErrorMessage, FILE_APPEND);

    // Return error response as JSON (keep this for debugging/potential future use)
    // Ensure output buffer is cleaned before sending JSON error
    if (ob_get_level() > 0) {
         ob_end_clean();
    }
    header('Content-Type: application/json'); // Ensure header is set for JSON
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'output' => $output // Include buffered output if any
    ]);
    exit;
}
?>
