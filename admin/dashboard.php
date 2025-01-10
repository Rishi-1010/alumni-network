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

// Initialize counts
$totalAlumni = 0;
$verifiedAlumni = 0;

// Function to get course details
function getCourseDetails($courseCode) {
    $courses = [
        '0051' => ['course' => 'BCA', 'department' => 'BVPICS', 'duration' => 3],
        // Add more courses as needed
    ];
    return $courses[$courseCode] ?? null;
}

try {
    // Count total alumni
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM users");
    $totalAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Count verified alumni
    $stmt = $conn->query("SELECT COUNT(*) AS verified FROM educational_details WHERE verification_status = 'verified'");
    $verifiedAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['verified'];
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

$alumni = null;
$parsedInfo = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_enrollment'])) {
    $enrollmentNumber = $_POST['search_enrollment'];

    // Validate enrollment number format
    if (preg_match('/^\d{15}$/', $enrollmentNumber)) {
        $enrolledYear = substr($enrollmentNumber, 0, 4);
        $courseCode = substr($enrollmentNumber, 7, 4);
        $uniqueNumber = substr($enrollmentNumber, 12, 3);

        $courseDetails = getCourseDetails($courseCode);

        if ($courseDetails) {
            $graduationYear = intval($enrolledYear) + $courseDetails['duration'];
            $parsedInfo = [
                "course" => $courseDetails['course'],
                "department" => $courseDetails['department'],
                "enrolled_year" => $enrolledYear,
                "graduation_year" => $graduationYear,
            ];
        }

        try {
            $stmt = $conn->prepare("
                SELECT u.*, ed.*, ps.*
                FROM users u
                LEFT JOIN educational_details ed ON u.user_id = ed.user_id
                LEFT JOIN professional_status ps ON u.user_id = ps.user_id
                WHERE ed.enrollment_number = ?
            ");
            $stmt->execute([$enrollmentNumber]);
            $alumni = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        $error = "Invalid enrollment number format. It must be a 15-digit number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">  
</head>
<body>
    <!-- Navigation and other dashboard components -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Alumni Network Logo">
            <span>Alumni Network</span>
        </div>
        <div class="nav-links">
            <a href="#" class="active">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="connections.php">Connections</a>
            <a href="jobs.php">Jobs</a>
            <div class="user-menu">
                <img src="<?php echo $user['profile_picture'] ?? '../assets/img/default-avatar.png'; ?>" 
                     alt="Profile" class="profile-pic">
                <div class="dropdown-content">
                    <a href="profile-settings.php">Settings</a>
                    <a href="../Authentication/Login/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, Admin!</h1>
            <p>Manage alumni data and perform administrative tasks.</p>
        </div>

        <!-- Search Form -->
        <form method="POST" class="search-form">
            <input type="text" name="search_enrollment" placeholder="Enter Enrollment Number" required>
            <button type="submit">Search</button>
        </form>

        <!-- Display Alumni Information if Found -->
        <?php if ($alumni): ?>
            <div class="alumni-card">
                <h3><?php echo htmlspecialchars($alumni['fullname']); ?></h3>
                <p>Email: <?php echo htmlspecialchars($alumni['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($alumni['phone']); ?></p>
                <p>Enrollment Number: <?php echo htmlspecialchars($alumni['enrollment_number']); ?></p>
                <div class="actions">
                    <a href="view-portfolio.php?id=<?php echo $alumni['user_id']; ?>" class="btn">View Portfolio</a>
                    <a href="verify.php?id=<?php echo $alumni['user_id']; ?>" class="btn">Verify</a>
                    <button class="btn delete-alumni" data-id="<?php echo $alumni['user_id']; ?>">Remove</button>
                </div>
            </div>
        <?php elseif ($parsedInfo): ?>
            <div class="alumni-card">
                <h3>Alumni Not Found</h3>
                <p>Enrolled Year: <?php echo htmlspecialchars($parsedInfo['enrolled_year']); ?></p>
                <p>Department: <?php echo htmlspecialchars($parsedInfo['department']); ?></p>
                <p>Course: <?php echo htmlspecialchars($parsedInfo['course']); ?></p>
                <p>Expected Graduation Year: <?php echo htmlspecialchars($parsedInfo['graduation_year']); ?></p>
            </div>
        <?php elseif ($error): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
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

        <div class="activity-section">
            <h2>Recent Activity</h2>
            <div class="activity-feed">
                <!-- Add your activity items here -->
                <div class="activity-item">
                    <i class="fas fa-user-plus"></i>
                    <div class="activity-content">
                        <p>Profile created</p>
                        <span class="activity-date">2023-10-01</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/rollnumformat.js"></script>
</body>
</html>
