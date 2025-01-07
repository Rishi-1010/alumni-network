<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Authentication/Login/login.php");
    exit();
}

// Get user data
$db = new Database();
$conn = $db->connect();

try {
    $stmt = $conn->prepare("
        SELECT u.*, ed.*, ps.*
        FROM users u
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
        LEFT JOIN professional_status ps ON u.user_id = ps.user_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="assets/img/logo.png" alt="Alumni Network Logo">
            <span>Alumni Network</span>
        </div>
        <div class="nav-links">
            <a href="#" class="active">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="connections.php">Connections</a>
            <a href="jobs.php">Jobs</a>
            <div class="user-menu">
                <img src="<?php echo $user['profile_picture'] ?? 'assets/img/default-avatar.png'; ?>" 
                     alt="Profile" class="profile-pic">
                <div class="dropdown-content">
                    <a href="profile-settings.php">Settings</a>
                    <a href="Authentication/Login/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
            <p>Last login: <?php echo $user['last_login']; ?></p>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3>Alumni Status</h3>
                <p><?php echo $user['verification_status']; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3>Current Status</h3>
                <p><?php echo ucfirst($user['current_status']); ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-building"></i>
                <h3>Company</h3>
                <p><?php echo $user['company_name'] ?? 'Not specified'; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-network-wired"></i>
                <h3>Connections</h3>
                <p>0</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-section">
            <h2>Recent Activity</h2>
            <div class="activity-feed">
                <!-- Add your activity items here -->
                <div class="activity-item">
                    <i class="fas fa-user-plus"></i>
                    <div class="activity-content">
                        <p>Profile created</p>
                        <span class="activity-date"><?php echo $user['registration_date']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="profile-edit.php" class="action-btn">
                    <i class="fas fa-user-edit"></i>
                    Update Profile
                </a>
                <a href="connections.php" class="action-btn">
                    <i class="fas fa-users"></i>
                    Find Alumni
                </a>
                <a href="jobs.php" class="action-btn">
                    <i class="fas fa-search"></i>
                    Browse Jobs
                </a>
            </div>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html> 