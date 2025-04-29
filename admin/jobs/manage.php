<?php
session_start();
require_once '../../config/db_connection.php';

// Security check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch statistics
try {
    // Get total jobs count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM jobs");
    $totalJobs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get pending company verifications
    $stmt = $conn->query("SELECT COUNT(*) as pending FROM companies WHERE verification_status = 'pending'");
    $pendingCompanies = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];

    // Get active job postings
    $stmt = $conn->query("SELECT COUNT(*) as active FROM jobs WHERE status = 'active'");
    $activeJobs = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

    // Get total applications
    $stmt = $conn->query("SELECT COUNT(*) as total FROM job_applications");
    $totalApplications = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/modal.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .job-portal-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 1rem;
        }

        .management-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .section-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .section-card h3 {
            margin-bottom: 1rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-card h3 i {
            color: #007bff;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .action-list li {
            margin-bottom: 1rem;
        }

        .action-list a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #555;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.2s ease;
        }

        .action-list a:hover {
            background: #f8f9fa;
            color: #007bff;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .job-portal-container {
                padding: 1rem;
            }

            .management-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        <div class="dashboard-navbar">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../profile.php">Profile</a>
            <a href="manage.php" class="active">Job Portal</a>
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <div class="job-portal-container">
        <h1>Job Portal Management</h1>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3>Total Jobs</h3>
                <p><?php echo $totalJobs; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-building"></i>
                <h3>Pending Companies</h3>
                <p><?php echo $pendingCompanies; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Active Jobs</h3>
                <p><?php echo $activeJobs; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <h3>Total Applications</h3>
                <p><?php echo $totalApplications; ?></p>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="management-sections">
            <!-- Company Management -->
            <div class="section-card">
                <h3><i class="fas fa-building"></i> Company Management</h3>
                <ul class="action-list">
                    <li><a href="companies/verify.php"><i class="fas fa-check"></i> Verify Companies</a></li>
                    <li><a href="companies/list.php"><i class="fas fa-list"></i> View All Companies</a></li>
                    <li><a href="companies/reports.php"><i class="fas fa-chart-bar"></i> Company Reports</a></li>
                </ul>
            </div>

            <!-- Job Posting Management -->
            <div class="section-card">
                <h3><i class="fas fa-briefcase"></i> Job Management</h3>
                <ul class="action-list">
                    <li><a href="jobs/review.php"><i class="fas fa-tasks"></i> Review Job Postings</a></li>
                    <li><a href="jobs/categories.php"><i class="fas fa-tags"></i> Manage Categories</a></li>
                    <li><a href="jobs/reports.php"><i class="fas fa-chart-line"></i> Job Analytics</a></li>
                </ul>
            </div>

            <!-- Application Management -->
            <div class="section-card">
                <h3><i class="fas fa-file-alt"></i> Application Management</h3>
                <ul class="action-list">
                    <li><a href="applications/track.php"><i class="fas fa-search"></i> Track Applications</a></li>
                    <li><a href="applications/statistics.php"><i class="fas fa-chart-pie"></i> Application Statistics</a></li>
                    <li><a href="applications/feedback.php"><i class="fas fa-comment"></i> Application Feedback</a></li>
                </ul>
            </div>

            <!-- Settings & Configuration -->
            <div class="section-card">
                <h3><i class="fas fa-cog"></i> Portal Settings</h3>
                <ul class="action-list">
                    <li><a href="settings/email.php"><i class="fas fa-envelope"></i> Email Templates</a></li>
                    <li><a href="settings/notifications.php"><i class="fas fa-bell"></i> Notification Settings</a></li>
                    <li><a href="settings/permissions.php"><i class="fas fa-lock"></i> Permission Settings</a></li>
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="jobs/create.php" class="action-btn">
                <i class="fas fa-plus"></i> Create New Job Posting
            </a>
            <a href="companies/invite.php" class="action-btn">
                <i class="fas fa-envelope"></i> Invite Companies
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/modal.js"></script>
</body>
</html> 