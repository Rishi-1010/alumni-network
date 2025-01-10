<?php
session_start();
require_once '../config/db_connection.php';
require_once '../send_otp/PHPMailerFunction.php';  // Include the OTP email sending function

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Function to determine course details based on course code
function getCourseDetails($courseCode) {
    $courses = [
        "0051" => ["course" => "Bachelor of Computer Applications (BCA)", "department" => "BVPICS", "duration" => 3],
        "0052" => ["course" => "Master of Computer Applications (MCA)", "department" => "SRIMCA", "duration" => 2],
        "0101" => ["course" => "Bachelor of Business Administration (BBA)", "department" => "BVPIC", "duration" => 3],
        "0011" => ["course" => "Master of Science in Information Technology M.Sc. (IT)", "department" => "BMIIT", "duration" => 3],
    ];
    return $courses[$courseCode] ?? null; // Return the details or null if course code is invalid
}

// Handle form submission for enrollment number
$studentDetails = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_number'])) {
    $enrollmentNumber = $_POST['enrollment_number'];

    if (preg_match('/^\d{15}$/', $enrollmentNumber)) {
        $enrolledYear = substr($enrollmentNumber, 0, 4);
        $courseCode = substr($enrollmentNumber, 7, 4);
        $uniqueNumber = substr($enrollmentNumber, 12, 3);

        $courseDetails = getCourseDetails($courseCode);

        if ($courseDetails) {
            $studentDetails = [
                "course" => $courseDetails['course'],
                "department" => $courseDetails['department'],
                "graduated_year" => intval($enrolledYear) + $courseDetails['duration'],
                "enrollment_id" => $enrollmentNumber,
                "enrolled_year" => $enrolledYear,
            ];
        } else {
            $error = "Invalid course code.";
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
                <h1>Welcome to Admin Panel</h1>
            </div>
        </header>

        <!-- Send OTP Form -->
        <div class="data-section">
            <h2>Send OTP to Alumni</h2>
            <form method="POST" class="form-inline">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter Alumni Email" required>
                    <button type="submit" class="btn btn-primary">Send OTP</button>
                </div>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
                $email = $_POST['email'];
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;

                require_once '../send_otp/PHPMailerFunction.php';
                $emailStatus = sendInvitationEmail($email, $otp);

                if ($emailStatus === true) {
                    echo "<div class='alert alert-success'>Invitation OTP sent to $email successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error sending OTP: $emailStatus</div>";
                }
            }
            ?>
        </div>

        <!-- Enrollment Number Input Section -->
        <div class="data-section">
            <h2>Search Student by Enrollment Number</h2>
            <form method="POST" class="form-inline">
                <div class="input-group mb-3">
                    <input type="text" name="enrollment_number" class="form-control" placeholder="Enter 15-digit Enrollment Number" required>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>

        <!-- Display Student Details -->
        <?php if ($studentDetails): ?>
            <div class="data-section">
                <h3>Student Details</h3>
                <table class="table table-bordered">
                    <tr>
                        <th>Enrollment ID</th>
                        <td><?php echo $studentDetails['enrollment_id']; ?></td>
                    </tr>
                    <tr>
                        <th>Course</th>
                        <td><?php echo $studentDetails['course']; ?></td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td><?php echo $studentDetails['department']; ?></td>
                    </tr>
                    <tr>
                        <th>Enrolled Year</th>
                        <td><?php echo $studentDetails['enrolled_year']; ?></td>
                    </tr>
                    <tr>
                        <th>Graduation Year</th>
                        <td><?php echo $studentDetails['graduated_year']; ?></td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>
