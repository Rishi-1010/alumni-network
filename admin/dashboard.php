<?php
session_start();
require_once '../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch dashboard statistics
try {
    // Total Alumni Count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Pending Verifications
    $stmt = $conn->query("SELECT COUNT(*) as pending FROM educational_details WHERE verification_status = 'pending'");
    $pendingVerifications = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];

    // Recent Registrations
    $stmt = $conn->query("
        SELECT u.user_id, u.fullname, u.email, ed.graduation_year, ed.verification_status 
        FROM users u 
        JOIN educational_details ed ON u.user_id = ed.user_id 
        ORDER BY u.registration_date DESC 
        LIMIT 5
    ");
    $recentRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Alumni Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img style="width: 100px; height: 100px;" src="../assets/img/logo.png" alt="Logo"><br><br>
                <span>Admin Panel</span>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="verifications.php">
                    <i class="fas fa-user-check"></i> Verifications
                    <?php if($pendingVerifications > 0): ?>
                        <span class="badge"><?php echo $pendingVerifications; ?></span>
                    <?php endif; ?>
                </a>
                <a href="alumni-list.php">
                    <i class="fas fa-users"></i> Alumni List
                </a>
                <a href="reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../Authentication/AdminLogin/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <div class="header-content">
                    <h1>Welcome, Admin</h1>
                    <div class="admin-profile">
                        <span><?php echo $_SESSION['admin_username']; ?></span>
                        <img src="../assets/img/admin-avatar.png" alt="Admin">
                    </div>
                </div>
            </header>

            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Alumni</h3>
                        <p><?php echo $totalAlumni; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pending Verifications</h3>
                        <p><?php echo $pendingVerifications; ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations Table -->
            <div class="data-section">
                <div class="section-header">
                    <h2>Recent Registrations</h2>
                    <a href="alumni-list.php" class="view-all">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Graduation Year</th>
                                <th>Status</th>
                                <th>Portfolio</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($recentRegistrations): ?>
                                <?php foreach($recentRegistrations as $alumni): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($alumni['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                                    <td><?php echo htmlspecialchars($alumni['graduation_year']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo htmlspecialchars($alumni['verification_status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($alumni['verification_status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view-portfolio.php?id=<?php echo htmlspecialchars($alumni['user_id']); ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                    <td>
                                        <?php if($alumni['verification_status'] === 'pending'): ?>
                                            <a href="verify-alumni.php?id=<?php echo htmlspecialchars($alumni['user_id']); ?>" 
                                               class="btn btn-sm btn-primary">
                                                Verify
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                Verified
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Add Delete Button -->
                                        <button class="btn btn-sm btn-danger delete-alumni" 
                                                data-id="<?php echo htmlspecialchars($alumni['user_id']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No recent registrations</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html> 