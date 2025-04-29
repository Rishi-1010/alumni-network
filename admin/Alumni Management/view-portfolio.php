<?php
session_start();
require_once '../../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->connect();
$userId = $_GET['id'];

try {
    // Fetch user data along with projects, skills, career goals, and certifications
    $stmt = $conn->prepare("
        SELECT 
            u.*,
            ed.course, ed.graduation_year, ed.enrollment_number, 
            ed.verification_status, ed.verification_date,
            ps.current_status, ps.company_name, ps.position
        FROM users u
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
        LEFT JOIN professional_status ps ON u.user_id = ps.user_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug professional status data
    error_log("Professional Status Data: " . print_r([
        'current_status' => $userData['current_status'] ?? 'null',
        'company_name' => $userData['company_name'] ?? 'null',
        'position' => $userData['position'] ?? 'null'
    ], true));

    // If professional status is not found in the main query, try to fetch it separately
    if (empty($userData['current_status']) && empty($userData['company_name']) && empty($userData['position'])) {
        $stmt = $conn->prepare("SELECT current_status, company_name, position FROM professional_status WHERE user_id = ?");
        $stmt->execute([$userId]);
        $professionalData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($professionalData) {
            $userData['current_status'] = $professionalData['current_status'];
            $userData['company_name'] = $professionalData['company_name'];
            $userData['position'] = $professionalData['position'];
            
            error_log("Fetched Professional Status Separately: " . print_r($professionalData, true));
        }
    }

    // Calculate graduation year for BCA students based on enrollment number
    if (!empty($userData['enrollment_number'])) {
        $enrollmentNumber = $userData['enrollment_number'];
        $enrollmentYear = null;
        
        // Check if enrollment number is alphanumeric (like 10BCA06)
        if (preg_match('/^(\d{2})[A-Za-z]+/', $enrollmentNumber, $matches)) {
            // Extract first two digits and add 2000 to get the full year
            $twoDigitYear = $matches[1];
            $enrollmentYear = 2000 + intval($twoDigitYear);
        } 
        // Check if enrollment number is numeric with at least 4 digits
        else if (strlen($enrollmentNumber) >= 4 && is_numeric($enrollmentNumber)) {
            // Extract the first 4 digits as enrollment year
            $enrollmentYear = intval(substr($enrollmentNumber, 0, 4));
        }
        
        // Check if we have a valid enrollment year
        $currentYear = date('Y');
        if ($enrollmentYear !== null && $enrollmentYear >= 2000 && $enrollmentYear <= $currentYear) {
            // Add 3 years to get graduation year for BCA course
            $calculatedGraduationYear = $enrollmentYear + 3;
            
            // Update the graduation year in the userData array
            $userData['graduation_year'] = $calculatedGraduationYear;
            
            // Update the graduation year in the database
            $updateStmt = $conn->prepare("UPDATE educational_details SET graduation_year = ? WHERE user_id = ?");
            $updateStmt->execute([$calculatedGraduationYear, $userId]);
        }
    }

    // Fetch projects
    $stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ?");
    $stmt->execute([$userId]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch skills
    $stmt = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
    $stmt->execute([$userId]);
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch career goals
    $stmt = $conn->prepare("SELECT * FROM career_goals WHERE user_id = ?");
    $stmt->execute([$userId]);
    $careerGoals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch certifications from the new certifications table
    $stmt = $conn->prepare("SELECT certificate_id, certificate_path, upload_date FROM certifications WHERE user_id = ? ORDER BY upload_date DESC");
    $stmt->execute([$userId]);
    $certifications = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all certifications

    if (!$userData) {
        die("Alumni not found");
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Portfolio - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/portfolio.css">
</head>
<body>
    <!-- Navigation copied from dashboard.php -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-navbar" id="navLinks">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../profile.php">Profile</a>
            <a href="totalalumnis.php" class="active">Total Alumni</a>
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>
    <div class="admin-container">
        <div class="main-content">
            <div class="portfolio-header">
                <h1 align="center">Alumni Portfolio</h1>
                <a href="totalalumnis.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Alumni List
                </a>
            </div>

            <div class="portfolio-content">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="basic-info-item">
                                        <i class="fas fa-user-circle"></i>
                                        <div>
                                            <strong>Name:</strong>
                                            <div><?php echo htmlspecialchars($userData['fullname']); ?></div>
                                        </div>
                                    </div>
                                    <div class="basic-info-item">
                                        <i class="fas fa-envelope"></i>
                                        <div>
                                            <strong>Email:</strong>
                                            <div><?php echo htmlspecialchars($userData['email']); ?></div>
                                        </div>
                                    </div>
                                    <div class="basic-info-item">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <strong>Phone:</strong>
                                            <div><?php echo htmlspecialchars($userData['phone'] ?? 'Not provided'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="basic-info-item">
                                        <i class="fas fa-birthday-cake"></i>
                                        <div>
                                            <strong>Date of Birth:</strong>
                                            <div><?php echo !empty($userData['dob']) ? date('F j, Y', strtotime($userData['dob'])) : 'Not provided'; ?></div>
                                        </div>
                                    </div>
                                    <div class="basic-info-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <div>
                                            <strong>Registration Date:</strong>
                                            <div><?php echo date('F j, Y', strtotime($userData['registration_date'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Educational Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-graduation-cap"></i> Educational Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="education-card">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <p><i class="fas fa-book"></i> <strong>Course:</strong> 
                                            <?php echo htmlspecialchars($userData['course'] ?? 'Not provided'); ?>
                                        </p>
                                        <p><i class="fas fa-calendar-check"></i> <strong>Graduation Year:</strong> 
                                            <?php echo htmlspecialchars($userData['graduation_year'] ?? 'Not provided'); ?>
                                        </p>
                                        <p><i class="fas fa-id-card"></i> <strong>Enrollment Number:</strong> 
                                            <?php echo htmlspecialchars($userData['enrollment_number'] ?? 'Not provided'); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <p><strong>Verification Status:</strong></p>
                                        <span class="badge bg-<?php echo ($userData['verification_status'] ?? '') === 'verified' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($userData['verification_status'] ?? 'Pending')); ?>
                                        </span>
                                        <?php if($userData['verification_date']): ?>
                                            <p class="mt-3">
                                                <i class="fas fa-calendar-check"></i> <strong>Verified On:</strong>
                                                <?php echo date('F j, Y', strtotime($userData['verification_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-briefcase"></i> Professional Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="professional-card">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <p><i class="fas fa-user-tie"></i> <strong>Current Status:</strong> 
                                            <?php echo ucfirst(htmlspecialchars($userData['current_status'] ?? 'Not provided')); ?>
                                        </p>
                                        <?php if($userData['current_status'] === 'employed'): ?>
                                            <p><i class="fas fa-building"></i> <strong>Company:</strong> 
                                                <?php echo htmlspecialchars($userData['company_name'] ?? 'Not provided'); ?>
                                            </p>
                                            <p><i class="fas fa-id-badge"></i> <strong>Position:</strong> 
                                                <?php echo htmlspecialchars($userData['position'] ?? 'Not provided'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php if($userData['current_status'] === 'employed'): ?>
                                        <div class="info-card">
                                            <p><strong>Employment Status:</strong></p>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-project-diagram"></i> Project Details</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($projects)): ?>
                            <div class="row">
                                <?php foreach ($projects as $index => $project): ?>
                                    <div class="col-md-6">
                                        <div class="project-card">
                                            <div class="project-header">
                                                <h4 class="project-title">
                                                    <i class="fas fa-code-branch"></i> 
                                                    Project <?php echo $index + 1; ?>: <?php echo htmlspecialchars($project['title']); ?>
                                                </h4>
                                            </div>
                                            <div class="project-body">
                                                <div class="project-description">
                                                    <p><strong><i class="fas fa-info-circle"></i> Description:</strong></p>
                                                    <p class="description-text"><?php echo htmlspecialchars($project['description']); ?></p>
                                                </div>
                                                <div class="project-technologies">
                                                    <p><strong><i class="fas fa-tools"></i> Technologies Used:</strong></p>
                                                    <p class="tech-stack"><?php echo htmlspecialchars($project['technologies_used']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No projects available for this alumni.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Career Goals -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-bullseye"></i> Career Goals</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($careerGoals)): ?>
                            <?php foreach ($careerGoals as $goal): ?>
                                <div class="goal-card">
                                    <p><i class="fas fa-star"></i> <strong>Goal:</strong></p>
                                    <p class="ms-4"><?php echo htmlspecialchars($goal['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No career goals available.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Skills and Certifications -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-certificate"></i> Skills and Certifications</h3>
                    </div>
                    <div class="card-body">
                        <h4>Skills</h4>
                        <?php if (!empty($skills)): ?>
                            <div class="row">
                                <?php foreach ($skills as $skill): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="skill-card">
                                            <div class="skill-header">
                                                <h5 class="mb-2">
                                                    <i class="fas fa-code"></i> Language Specialization
                                                </h5>
                                                <span class="badge bg-<?php 
                                                    switch($skill['proficiency_level']) {
                                                        case 'Beginner': echo 'info'; break;
                                                        case 'Intermediate': echo 'primary'; break;
                                                        case 'Advanced': echo 'success'; break;
                                                        case 'Expert': echo 'warning'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>">
                                                    <?php echo htmlspecialchars($skill['proficiency_level']); ?>
                                                </span>
                                            </div>
                                            <div class="skill-details">
                                                <p>
                                                    <strong><i class="fas fa-code"></i> Language Specialization:</strong><br>
                                                    <span class="tech-badge">
                                                    <?php 
                                                        $languages = json_decode($skill['language_specialization'], true);
                                                        if (is_array($languages)) {
                                                            foreach ($languages as $lang) {
                                                                echo '<span class="badge bg-info me-1">' . htmlspecialchars($lang) . '</span>';
                                                            }
                                                        } else {
                                                            echo htmlspecialchars($skill['language_specialization']);
                                                        }
                                                    ?>
                                                    </span>
                                                    <?php if (!empty($skill['other_language'])): ?>
                                                        <br><strong>Other Languages:</strong> <?php echo htmlspecialchars($skill['other_language']); ?>
                                                    <?php endif; ?>
                                                </p>
                                                <p>
                                                    <strong><i class="fas fa-tools"></i> Tools:</strong><br>
                                                    <span class="tech-badge">
                                                    <?php 
                                                        $tools = json_decode($skill['tools'], true);
                                                        if (is_array($tools)) {
                                                            foreach ($tools as $tool) {
                                                                echo '<span class="badge bg-info me-1">' . htmlspecialchars($tool) . '</span>';
                                                            }
                                                        } else {
                                                            echo htmlspecialchars($skill['tools']);
                                                        }
                                                    ?>
                                                    </span>
                                                    <?php if (!empty($skill['other_tools'])): ?>
                                                        <br><strong>Other Tools:</strong> <?php echo htmlspecialchars($skill['other_tools']); ?>
                                                    <?php endif; ?>
                                                </p>
                                                <p>
                                                    <strong><i class="fas fa-laptop-code"></i> Technologies:</strong><br>
                                                    <span class="tech-badge">
                                                    <?php 
                                                        $technologies = json_decode($skill['technologies'], true);
                                                        if (is_array($technologies)) {
                                                            foreach ($technologies as $tech) {
                                                                echo '<span class="badge bg-info me-1">' . htmlspecialchars($tech) . '</span>';
                                                            }
                                                        } else {
                                                            echo htmlspecialchars($skill['technologies']);
                                                        }
                                                    ?>
                                                    </span>
                                                    <?php if (!empty($skill['other_technologies'])): ?>
                                                        <br><strong>Other Technologies:</strong> <?php echo htmlspecialchars($skill['other_technologies']); ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No skills available for this alumni.
                            </div>
                        <?php endif; ?>

                        <h4>Certifications</h4>
                        <?php if (!empty($certifications)): ?>
                            <div class="row">
                                <?php foreach ($certifications as $index => $certification): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="certification-item">
                                            <div class="cert-header">
                                                <h5>
                                                    <i class="fas fa-certificate"></i> 
                                                    Certificate <?php echo $index + 1; ?>
                                                </h5>
                                                <div class="cert-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?php echo date('F j, Y', strtotime($certification['upload_date'])); ?>
                                                </div>
                                            </div>
                                            <div class="cert-actions">
                                                <?php if ($certification['certificate_path']): ?>
                                                    <a href="../../<?php echo htmlspecialchars($certification['certificate_path']); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View Certificate
                                                    </a>
                                                    <a href="../../<?php echo htmlspecialchars($certification['certificate_path']); ?>" 
                                                       download
                                                       class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Certificate not available</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No certifications available.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
