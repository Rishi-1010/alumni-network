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
            // Check if we have arrays for language, tools, and technologies
            if (is_array($_POST['skills']['language']) && 
                is_array($_POST['skills']['tools']) && 
                is_array($_POST['skills']['technologies']) && 
                !empty($_POST['skills']['level'])) {
                
                // Handle "Other" options
                $language = $_POST['skills']['language'];
                $tools = $_POST['skills']['tools'];
                $technologies = $_POST['skills']['technologies'];
                
                // Replace "other" with user-provided values if they exist
                if (in_array('other', $language) && !empty($_POST['skills']['other_language'])) {
                    $language = array_diff($language, ['other']);
                    $language[] = $_POST['skills']['other_language'];
                }
                
                if (in_array('other', $tools) && !empty($_POST['skills']['other_tools'])) {
                    $tools = array_diff($tools, ['other']);
                    $tools[] = $_POST['skills']['other_tools'];
                }
                
                if (in_array('other', $technologies) && !empty($_POST['skills']['other_technologies'])) {
                    $technologies = array_diff($technologies, ['other']);
                    $technologies[] = $_POST['skills']['other_technologies'];
                }
                
                // Convert arrays to JSON strings for storage
                $languageJSON = json_encode(array_values($language));
                $toolsJSON = json_encode(array_values($tools));
                $technologiesJSON = json_encode(array_values($technologies));
                
                $stmt_skill = $conn->prepare("INSERT INTO skills (user_id, language_specialization, tools, technologies, proficiency_level) VALUES (?, ?, ?, ?, ?)");
                
                $stmt_skill->execute([
                    $userId,
                    $languageJSON,
                    $toolsJSON,
                    $technologiesJSON,
                    $_POST['skills']['level']
                ]);
            } else {
                error_log("Incomplete skill entry for user ID: $userId");
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

        // Insert Professional Status
        if (!empty($_POST['current_status'])) {
            $stmt = $conn->prepare("INSERT INTO professional_status
                                   (user_id, current_status, company_name, position)
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $_POST['current_status'],
                $_POST['company_name'] ?? null,
                $_POST['position'] ?? null
            ]);
        }

        // Insert Projects (if any submitted)
        if (isset($_POST['hasProjects']) && $_POST['hasProjects'] === 'yes' && !empty($_POST['projects'])) {
            // Maximum number of projects allowed
            $MAX_PROJECTS = 10;
            
            if (count($_POST['projects']) > $MAX_PROJECTS) {
                throw new Exception("Maximum number of projects ($MAX_PROJECTS) exceeded");
            }
            
            $stmt_proj = $conn->prepare("INSERT INTO projects (user_id, title, description, technologies_used) VALUES (?, ?, ?, ?)");
            $projectCount = 0;
            
            foreach ($_POST['projects'] as $project) {
                // Enhanced validation
                if (empty($project['title']) || empty($project['description']) || empty($project['technologies'])) {
                    error_log("Skipping incomplete project entry for user ID: $userId");
                    continue;
                }
                
                // Sanitize inputs
                $title = htmlspecialchars(trim($project['title']), ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars(trim($project['description']), ENT_QUOTES, 'UTF-8');
                $technologies = htmlspecialchars(trim($project['technologies']), ENT_QUOTES, 'UTF-8');
                
                // Validate length
                if (strlen($title) > 255 || strlen($technologies) > 255) {
                    throw new Exception("Project title or technologies exceed maximum length");
                }
                
                try {
                    $stmt_proj->execute([
                        $userId,
                        $title,
                        $description,
                        $technologies
                    ]);
                    $projectCount++;
                } catch (PDOException $e) {
                    error_log("Failed to insert project for user ID: $userId. Error: " . $e->getMessage());
                    throw new Exception("Failed to save project information");
                }
            }
            
            // Log success
            if ($projectCount > 0) {
                error_log("Successfully inserted $projectCount projects for user ID: $userId");
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

            // Loop through each uploaded certificate
            foreach ($_FILES['certifications']['name'] as $key => $name) {
                if ($_FILES['certifications']['error'][$key]['certificate_file'] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['certifications']['tmp_name'][$key]['certificate_file'];
                    $original_name = basename($name['certificate_file']);
                    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    
                    // Validate file type
                    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
                    if (!in_array($file_extension, $allowed_types)) {
                        error_log("Invalid file type for certificate: $original_name");
                        continue;
                    }

                    // Validate file size (5MB limit)
                    if ($_FILES['certifications']['size'][$key]['certificate_file'] > 5000000) {
                        error_log("File too large: $original_name");
                        continue;
                    }

                    // Create unique filename
                    $certificate_count = count(glob($target_dir . 'certificate*.{pdf,jpg,jpeg,png}', GLOB_BRACE)) + 1;
                    $new_filename = "certificate" . $certificate_count . "." . $file_extension;
                    $target_file = $target_dir . $new_filename;

                    // Move the uploaded file
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        // Store relative path in database
                        $relative_path = "assets/certificates/" . $enrollmentNumber . "/" . $new_filename;
                        try {
                            $stmt_cert->execute([$userId, $relative_path]);
                        } catch (PDOException $e) {
                            error_log("Failed to insert certificate into database: " . $e->getMessage());
                            // Continue with next certificate even if this one fails
                            continue;
                        }
                    } else {
                        error_log("Failed to move uploaded file: $original_name");
                    }
                } elseif ($_FILES['certifications']['error'][$key]['certificate_file'] !== UPLOAD_ERR_NO_FILE) {
                    // Log any upload errors except "no file"
                    error_log("Error uploading certificate: " . $_FILES['certifications']['error'][$key]['certificate_file']);
                }
            }
        }

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
            if (!empty($_POST['career_goals']['description'])) {
                $stmt_goal->execute([$userId, $_POST['career_goals']['description']]);
            } else {
                error_log("Empty career goal entry for user ID: $userId");
            }
        }

        // Commit transaction
        $conn->commit();

        // Clear any buffered output before redirect
        ob_end_clean();

        // Redirect to contactus.php on success
        header('Location: ../Login/login.php'); // Adjust path as needed
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
