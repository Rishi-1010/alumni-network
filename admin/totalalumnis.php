<?php
session_start();
require_once '../config/db_connection.php';

// Make sure this is the very first check after session_start()
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

// Additional security: ensure the header redirect happens
if (headers_sent()) {
    die("Redirect failed. Please <a href='../Authentication/AdminLogin/login.php'>click here</a>");
}

// Prevent regular users from accessing admin dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Initialize counts
$totalAlumni = 0;
$verifiedAlumni = 0;

try {
    // Count total alumni
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM users");
    $totalAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Count verified alumni
    $stmt = $conn->query("SELECT COUNT(*) AS verified FROM educational_details WHERE verification_status = 'verified'");
    $verifiedAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['verified'];

    // Fetch all alumni details
    $stmt = $conn->query("
SELECT u.*, ed.*, ed.verification_status
        FROM users u
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
    ");
    $alumniMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Alumni - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/portfolio.css"> <!-- Added portfolio CSS for consistent nav -->
    <style>
        /* Keep existing inline styles */
        .alumni-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .alumni-table th, .alumni-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .alumni-table th {
            background-color: #f4f4f4;
        }
        .alumni-actions {
            display: flex;
            flex-direction: column; /* Stack buttons vertically */
            align-items: flex-start; /* Align items to the start */
        }

        .alumni-actions a, .alumni-actions button {
            margin-bottom: 5px; /* Add spacing between buttons */
            text-decoration: none;
            display: block; /* Make them block-level elements */
            width: 100%; /* Make them take the full width of the cell */
            text-align: center; /* Center the text inside the button */
        }
    </style>
</head>
<body>
    <!-- Navigation and other dashboard components -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-navbar" id="navLinks">
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <!-- <a href="connections.php">Connections</a>
            <a href="jobs.php">Jobs</a> -->
            <a href="totalalumnis.php" class="active">Total Alumni</a>
            <a href="../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container container-fluid">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Total Alumni Members</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3>Total Alumni</h3>
                <p><?php echo $totalAlumni; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3>Verified Alumni</h3>
                <p><?php echo $verifiedAlumni; ?></p>
            </div>
            <!-- Add more stat cards as needed -->
        </div>

        <!-- Alumni Table -->
        <div class="alumni-list">
            <table class="alumni-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Enrollment Number</th>
                        <th>Verification Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($alumniMembers) && is_array($alumniMembers) && count($alumniMembers) > 0): ?>
                        <?php foreach ($alumniMembers as $alumni): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumni['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                                <td><?php echo htmlspecialchars($alumni['enrollment_number']); ?></td>
                                <td>
                                    <?php if ($alumni['verification_status'] === 'verified'): ?>
                                        <i class="fas fa-check-circle" style="color: green;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-circle" style="color: yellow;"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="alumni-actions">
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <a href="view-portfolio.php?id=<?php echo $alumni['user_id']; ?>" class="btn btn-primary btn-sm">View Portfolio</a>
                                        <div style="display: flex; justify-content: space-between; width: 100%;">
                                            <?php if ($alumni['verification_status'] !== 'verified'): ?>
                                                <form method="post" action="verify_alumni.php" onsubmit="return confirmVerification(<?php echo $alumni['user_id']; ?>)">
                                                    <input type="hidden" name="user_id" value="<?php echo $alumni['user_id']; ?>">
                                                    <button type="submit" class="btn btn-success btn-sm">Verify</button>
                                                </form>
                                            <?php endif; ?>
                                            <button class="btn btn-danger btn-sm delete-alumni" data-id="<?php echo $alumni['user_id']; ?>">Remove</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No alumni members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
        });

        document.querySelectorAll('.delete-alumni').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to remove this alumni member?')) {
                    window.location.href = 'delete_alumni.php?id=' + userId;
                }
            });
        });

        function confirmVerification(userId) {
            return confirm('Are you sure you want to verify this user?');
        }
    </script>
</body>
</html>
