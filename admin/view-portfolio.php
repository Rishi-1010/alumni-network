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
    // First get basic user info and educational details
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
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include your sidebar here if needed -->
        
        <div class="main-content">
            <div class="portfolio-header">
                <h1>Alumni Portfolio</h1>
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
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($userData['fullname']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($userData['phone'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Registration Date:</strong> <?php echo date('F j, Y', strtotime($userData['registration_date'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $userData['verification_status'] === 'verified' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($userData['verification_status'])); ?>
                                    </span>
                                </p>
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
                                </p>
                                <?php if($userData['verification_date']): ?>
                                    <p><strong>Verified On:</strong> <?php echo date('F j, Y', strtotime($userData['verification_date'])); ?></p>
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
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 