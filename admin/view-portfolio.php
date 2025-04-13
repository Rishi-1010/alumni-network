<?php
session_start();
require_once '../config/db_connection.php';

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
            ed.university_name, ed.graduation_year, ed.enrollment_number, 
            ed.verification_status, ed.verification_date,
            ps.current_status, ps.company_name, ps.position
        FROM users u
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
        LEFT JOIN professional_status ps ON u.user_id = ps.user_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../assets/css/portfolio.css"> <!-- Link to the new portfolio CSS -->
</head>
<body>
    <!-- Navigation copied from dashboard.php -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-navbar" id="navLinks">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="totalalumnis.php">Total Alumni</a>
            <a href="../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>
    <div class="admin-container">
        <div class="main-content">
            <div class="portfolio-header">
                <h1 align="center">Alumni Portfolio</h1>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
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
                                <div class="info-item">
                                    <strong><i class="fas fa-user-circle"></i> Name:</strong> 
                                    <span><?php echo htmlspecialchars($userData['fullname']); ?></span>
                                </div>
                                <div class="info-item">
                                    <strong><i class="fas fa-envelope"></i> Email:</strong> 
                                    <span><?php echo htmlspecialchars($userData['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <strong><i class="fas fa-phone"></i> Phone:</strong> 
                                    <span><?php echo htmlspecialchars($userData['phone'] ?? 'Not provided'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <strong><i class="fas fa-calendar-alt"></i> Registration Date:</strong> 
                                    <span><?php echo date('F j, Y', strtotime($userData['registration_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <strong><i class="fas fa-check-circle"></i> Status:</strong> 
                                    <span class="badge status-badge <?php echo $userData['verification_status'] === 'verified' ? 'verified' : 'pending'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($userData['verification_status'])); ?>
                                    </span>
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
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>University:</strong> <?php echo htmlspecialchars($userData['university_name'] ?? 'Not provided'); ?></p>
                                <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($userData['graduation_year'] ?? 'Not provided'); ?></p>
                                <p><strong>Enrollment Number:</strong> <?php echo htmlspecialchars($userData['enrollment_number'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Verification Status:</strong> 
                                    <span class="badge bg-<?php echo ($userData['verification_status'] ?? '') === 'verified' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($userData['verification_status'] ?? 'Pending')); ?>
                                    </span>
                                </div>
                                <?php if($userData['verification_date']): ?>
                                    <div class="info-item">
                                        <strong><i class="fas fa-calendar-check"></i> Verified On:</strong> 
                                        <span><?php echo date('F j, Y', strtotime($userData['verification_date'])); ?></span>
                                    </div>
                                <?php endif; ?>
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
                        <div class="row">
                            <div class="col-md-6">
                                
                                <p><strong>Current Status:</strong> <?php echo ucfirst(htmlspecialchars($userData['current_status'] ?? 'Not provided')); ?></p>
                                <p><strong>Company:</strong> <?php echo htmlspecialchars($userData['company_name'] ?? 'Not provided'); ?></p>
                                <p><strong>Position:</strong> <?php echo htmlspecialchars($userData['position'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <!-- Add any additional professional information here -->
                                <?php if($userData['current_status'] === 'employed'): ?>
                                    <p><strong>Employment Status:</strong> 
                                        <span class="badge bg-success">Active</span>
                                    </p>
                                <?php endif; ?>
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
                        <?php foreach ($projects as $project): ?>
                            <p><strong>Title:</strong> <?php echo htmlspecialchars($project['title']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($project['description']); ?></p>
                            <!-- Add more fields as needed -->
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Career Goals -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-bullseye"></i> Career Goals</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($careerGoals as $goal): ?>
                            <p><strong>Goal:</strong> <?php echo htmlspecialchars($goal['description']); ?></p>
                            <!-- Add more fields as needed -->
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Skills and Certifications -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-certificate"></i> Skills and Certifications</h3>
                    </div>
                    <div class="card-body">
                        <h4>Skills</h4>
                        <?php foreach ($skills as $skill): ?>
                            <p><strong>Skill:</strong> <?php echo htmlspecialchars($skill['skill_name']); ?></p>
                            <!-- Add more fields as needed -->
                        <?php endforeach; ?>

                        <h4>Certifications</h4>
                        <?php if (!empty($certifications)): ?>
                            <ul class="list-group">
                                <?php foreach ($certifications as $certification): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-file-alt me-2"></i>
                                            Uploaded on: <?php echo date('F j, Y, g:i a', strtotime($certification['upload_date'])); ?>
                                        </span>
                                        <?php if ($certification['certificate_path']): ?>
                                            <a href="../<?php echo htmlspecialchars($certification['certificate_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Certificate
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Path not available</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No certifications available for this user.</p>
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
