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

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format. Please enter a valid email address.');
    }

    // Validate DOB
    $dob = new DateTime($_POST['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    // Set minimum and maximum age limits
    $minAge = 16;
    $maxAge = 100;
    
    if ($age < $minAge) {
        throw new Exception("You must be at least {$minAge} years old to register.");
    }
    
    if ($age > $maxAge) {
        throw new Exception("Age cannot be more than {$maxAge} years.");
    }

    // Validate phone number (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
        throw new Exception('Invalid phone number format. Please enter exactly 10 digits.');
    }

    // Check if user exists (using correct column name)
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('This email is already registered. Please use a different email address or try logging in.');
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

    // Add this validation after the enrollment number validation
    if (empty($_POST['graduation_year'])) {
        throw new Exception("Graduation year is required");
    }

    // Validate graduation year is within acceptable range
    $graduationYear = intval($_POST['graduation_year']);
    $currentYear = date('Y');
    if ($graduationYear < 2000 || $graduationYear > ($currentYear + 4)) {
        throw new Exception("Invalid graduation year");
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

        // Insert Skills with enhanced debugging
        if (!empty($_POST['skills'])) {
            error_log("Skills data received: " . print_r($_POST['skills'], true));
            
            // Check if all required arrays exist
            if (!isset($_POST['skills']['language'])) {
                error_log("Language array is missing");
                throw new Exception("Language specialization is required");
            }
            if (!isset($_POST['skills']['tools'])) {
                error_log("Tools array is missing");
                throw new Exception("Tools selection is required");
            }
            if (!isset($_POST['skills']['technologies'])) {
                error_log("Technologies array is missing");
                throw new Exception("Technologies selection is required");
            }

            // Check if arrays are actually arrays and not empty
            if (!is_array($_POST['skills']['language']) || empty($_POST['skills']['language'])) {
                error_log("Language is not an array or is empty");
                throw new Exception("Please select at least one programming language");
            }
            if (!is_array($_POST['skills']['tools']) || empty($_POST['skills']['tools'])) {
                error_log("Tools is not an array or is empty");
                throw new Exception("Please select at least one tool");
            }
            if (!is_array($_POST['skills']['technologies']) || empty($_POST['skills']['technologies'])) {
                error_log("Technologies is not an array or is empty");
                throw new Exception("Please select at least one technology");
            }

            // Handle "Other" options with debugging
            $language = $_POST['skills']['language'];
            $tools = $_POST['skills']['tools'];
            $technologies = $_POST['skills']['technologies'];
            
            error_log("Original arrays - Languages: " . print_r($language, true));
            error_log("Original arrays - Tools: " . print_r($tools, true));
            error_log("Original arrays - Technologies: " . print_r($technologies, true));
            
            // Replace "other" with user-provided values if they exist
            if (in_array('other', $language) && !empty($_POST['skills']['other_language'])) {
                $language = array_diff($language, ['other']);
                $language[] = $_POST['skills']['other_language'];
                error_log("Added custom language: " . $_POST['skills']['other_language']);
            }
            
            if (in_array('other', $tools) && !empty($_POST['skills']['other_tools'])) {
                $tools = array_diff($tools, ['other']);
                $tools[] = $_POST['skills']['other_tools'];
                error_log("Added custom tool: " . $_POST['skills']['other_tools']);
            }
            
            if (in_array('other', $technologies) && !empty($_POST['skills']['other_technologies'])) {
                $technologies = array_diff($technologies, ['other']);
                $technologies[] = $_POST['skills']['other_technologies'];
                error_log("Added custom technology: " . $_POST['skills']['other_technologies']);
            }
            
            // Convert arrays to JSON strings for storage
            $languageJSON = json_encode(array_values($language));
            $toolsJSON = json_encode(array_values($tools));
            $technologiesJSON = json_encode(array_values($technologies));
            
            error_log("Final JSON strings:");
            error_log("Languages JSON: " . $languageJSON);
            error_log("Tools JSON: " . $toolsJSON);
            error_log("Technologies JSON: " . $technologiesJSON);
            
            $stmt_skill = $conn->prepare("INSERT INTO skills (user_id, language_specialization, tools, technologies) VALUES (?, ?, ?, ?)");
            
            try {
                $stmt_skill->execute([
                    $userId,
                    $languageJSON,
                    $toolsJSON,
                    $technologiesJSON
                ]);
                error_log("Successfully inserted skills for user ID: $userId");
            } catch (PDOException $e) {
                error_log("Database error while inserting skills: " . $e->getMessage());
                throw new Exception("Failed to save skills information: " . $e->getMessage());
            }
        } else {
            error_log("No skills data received in POST request");
            throw new Exception("Skills information is required");
        }

        // Add this before the educational details insertion
        error_log("Graduation Year from form: " . ($_POST['graduation_year'] ?? 'not set'));

        // Update the educational details insertion with debug logging
        $department = '';
        if ($_POST['course'] === 'BCA') {
            $department = "Bhulabhai VanmaliBhai Patel Institute Of Computer Science";
        } else if ($_POST['course'] === 'MCA') {
            $department = "Shrimad Rajchandra Institute Of Management And Computer Application";
        }

        $stmt = $conn->prepare("INSERT INTO educational_details
                               (user_id, university_name, course, department, enrollment_number, graduation_year)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            'Uka Tarsadia University',
            $_POST['course'],
            $department,
            $enrollmentNumber,
            $_POST['graduation_year']
        ]);
        error_log("Successfully inserted educational details with graduation year: " . $_POST['graduation_year']);

        // Insert Professional Status
        if (!empty($_POST['current_status'])) {
            if ($_POST['current_status'] === 'freelancer') {
                // Handle freelancer data
                $platforms = !empty($_POST['platforms']) ? json_encode($_POST['platforms']) : null;
                $expertise_areas = !empty($_POST['expertise_areas']) ? json_encode($_POST['expertise_areas']) : null;
                
                $stmt = $conn->prepare("INSERT INTO professional_status
                                       (user_id, current_status, freelance_title, platforms, expertise_areas, experience_years)
                                       VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $userId,
                    $_POST['current_status'],
                    $_POST['freelance_title'] ?? null,
                    $platforms,
                    $expertise_areas,
                    $_POST['experience_years'] ?? null
                ]);
            } else {
                // Handle other status types (employed, student, etc.)
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

        // Clear any buffered output before sending JSON
        ob_end_clean();

        // Return success response as JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful',
            'redirect' => '../Login/login.php'
        ]);
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
