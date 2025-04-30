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

// Function to get course details
function getCourseDetails($courseCode) {
    $courses = [
        '0051' => ['course' => 'BCA', 'department' => 'BVPICS', 'duration' => 3, 'semesters' => 6],
        '0461' => ['course' => 'MCA', 'department' => 'SRIMCA', 'duration' => 2, 'semesters' => 4],
        // Add more courses as needed
    ];

    // Define patterns for different enrollment numbers
    $patterns = [
        'BCA' => [
            '/^2023051\d{7}$/', // Example pattern for BCA
            '/^2022031\d{7}$/', // Another pattern for BCA
            
        ],
        'MCA' => [
            '/^2024041\d{7}$/', // Example pattern for MCA
            // '/^2022071\d{7}$/', Another pattern for MCA
            // Add more patterns for MCA as needed
        ],
        // Add more courses and their patterns as needed
    ];

    // Check if the course code matches any predefined course
    if (isset($courses[$courseCode])) {
        return $courses[$courseCode];
    }

    // Check if the course code matches any pattern
    foreach ($patterns as $course => $coursePatterns) {
        foreach ($coursePatterns as $pattern) {
            if (preg_match($pattern, $courseCode)) {
                return $courses[array_search($course, array_column($courses, 'course'))];
            }
        }
    }

    return null;
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

    // Update the validation to be more flexible
    if (!empty($enrollmentNumber)) {
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
            
            if (!$alumni) {
                $error = "No alumni found with this enrollment number.";
            }
        } catch(PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        $error = "Please enter an enrollment number.";
    }
}

// Add this after the existing session checks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $alumniEmail = $_POST['alumni_emails'];
    
    // Generate OTP
    $otp = sprintf("%06d", mt_rand(100000, 999999));
    
    try {
        // Store email and OTP in session
        $_SESSION['temp_email'] = $alumniEmail;
        $_SESSION['otp'] = $otp;
        
        // Include PHPMailer function
        require_once '../send_otp/PHPMailerFunction.php';
        
        $emails = explode(',', $alumniEmail);
        $emails = array_map('trim', $emails); // Remove whitespace

        // Send OTP
        $mailResult = sendInvitationEmail($emails);

        if ($mailResult === true) {
            $_SESSION['success'] = "OTP sent successfully.";
        } elseif (is_string($mailResult)) {
            $_SESSION['error'] = $mailResult;
        } else {
            $_SESSION['error'] = "Failed to send Link.";
        }
    } catch(Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
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
    <link rel="stylesheet" href="../assets/css/invite.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../assets/css/portfolio.css"> -->
    <link rel="stylesheet" href="../assets/css/navigation.css">
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="../assets/js/jquery-ui/jquery-ui.min.css">
    
    <!-- jQuery (load first) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- jQuery UI (load after jQuery) -->
    <script src="../assets/js/jquery-ui/jquery-ui.min.js"></script>

    <style>
        /* Only keep notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #444;
            color: #fff;
            border-radius: 5px;
            z-index: 1000;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }
        .notification.success { background-color: #4caf50; }
        .notification.error { background-color: #f44336; }
        .notification.fade-out { animation: fadeOut 0.5s forwards; }
        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; } }

        /* Add section divider style */
        .section-divider {
            border: none;
            border-top: 2px solid #e0e0e0;
            margin: 35px 0;
            width: 100%;
        }

        /* Section title styling */
        .section-title {
            margin: 35px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .section-title h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
            font-weight: 500;
        }
        .title-line {
            flex-grow: 1;
            height: 1px;
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <!-- Navigation and other dashboard components -->
    <nav class="dashboard-nav">
        <div href="dashboard.php" class="logo">
            <img src="../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        
        <!-- <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button> -->
        <div class="dashboard-navbar" id="navLinks">
            <a href="#" class="active">Dashboard</a>
            <a href="profile.php">Profile</a>
            <!-- <a href="connections.php">Connections</a>
            <a href="jobs.php">Jobs</a> -->
            <!-- <a href="Alumni Management/totalalumnis.php">Total Alumni</a> -->
            <a href="../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, Admin!</h1>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3>Total Alumni</h3>
                <p><?php echo $totalAlumni; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check"></i>
                <h3>Verified Alumni</h3>
                <p><?php echo $verifiedAlumni; ?></p>
            </div>
        </div>

        <!-- Section Divider -->
        <hr class="section-divider">

        <!-- Section Title -->
        <div class="section-title">
            <h2>Quick Actions</h2>
            <div class="title-line"></div>
        </div>

        <!-- Action Cards -->
        <div class="action-cards">
            <a href="Invite Alumni/invite.php" class="action-card">
                <i class="fas fa-user-plus"></i>
                <h3>Invite Alumni</h3>
                <p>Send invitations to new alumni members</p>
            </a>

            <a href="Alumni Management/totalalumnis.php" class="action-card">
                <i class="fas fa-users"></i>
                <h3>Manage Alumni</h3>
                <p>View, verify, search and manage all alumni records</p>
            </a>

            <a href="Analytics/reports.php" class="action-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Reports & Analytics</h3>
                <p>View statistics and generate reports about alumni data</p>
            </a>

            <a href="Events/events.php" class="action-card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Events Management</h3>
                <p>Create and manage alumni events, reunions, and meetups</p>
            </a>

            <a href="jobs/manage.php" class="action-card">
                <i class="fas fa-briefcase"></i>
                <h3>Job Portal</h3>
                <p>Manage job postings and career opportunities for alumni</p>
            </a>

            <a href="news/manage.php" class="action-card">
                <i class="fas fa-newspaper"></i>
                <h3>News & Updates</h3>
                <p>Post and manage alumni news, achievements and updates</p>
            </a>

            <a href="communications/manage.php" class="action-card">
                <i class="fas fa-envelope"></i>
                <h3>Communications</h3>
                <p>Send newsletters and manage alumni communications</p>
            </a>

            <a href="gallery/manage.php" class="action-card">
                <i class="fas fa-images"></i>
                <h3>Gallery</h3>
                <p>Manage photos and media from alumni events</p>
            </a>

            <a href="settings/manage.php" class="action-card">
                <i class="fas fa-cog"></i>
                <h3>Settings</h3>
                <p>Configure system settings and manage admin accounts</p>
            </a>
        </div>

        <!-- Message Container -->
        <div class="message-container">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success" id="successMessage">
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notification Container -->
        <div id="notificationContainer"></div>

        <!-- Display Alumni Information if Found -->
        <?php if ($alumni): ?>
            <div class="alumni-card">
                <h3><?php echo htmlspecialchars($alumni['fullname']); ?></h3>
                <p>Email: <?php echo htmlspecialchars($alumni['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($alumni['phone']); ?></p>
                <p>Enrollment Number: <?php echo htmlspecialchars($alumni['enrollment_number']); ?></p>
                <div class="actions">
                    <a href="view-portfolio.php?id=<?php echo $alumni['user_id']; ?>" class="btn">View Portfolio</a>
                    <?php if ($alumni['verification_status'] !== 'verified'): ?>
                        <form method="post" action="verify_alumni.php" onsubmit="return confirmVerification(<?php echo $alumni['user_id']; ?>)">
                            <input type="hidden" name="user_id" value="<?php echo $alumni['user_id']; ?>">
                            <button type="submit" class="btn">Verify</button>
                        </form>
                    <?php endif; ?>
                    <script>
                        function confirmVerification(userId) {
                            customConfirm('Are you sure you want to verify this user?', () => {
                                // Add your verification logic here
                                return true;
                            });
                            return false; // Prevent form submission until confirmation
                        }
                    </script>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/rollnumformat.js"></script>
    <script src="../assets/js/enrollment-autocomplete.js"></script>

    <!-- Modal HTML Structure -->
    <div id="customModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <p id="modalMessage"></p>
            <div class="modal-buttons">
                <button id="modalConfirm" class="btn btn-primary">Confirm</button>
                <button id="modalCancel" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-buttons {
            margin-top: 20px;
            text-align: right;
        }
    </style>

    <script>
        // document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        //     document.getElementById('navLinks').classList.toggle('active');
        // });

        // Hide success message after a few seconds
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000); // 5000 milliseconds = 5 seconds

        $(document).ready(function() {
            console.log('jQuery version:', $.fn.jquery);
            console.log('jQuery UI version:', $.ui ? $.ui.version : 'not loaded');
            console.log('jQuery UI widget factory:', $.widget ? 'available' : 'not available');
            
            if (typeof $.ui !== 'undefined' && $.ui.autocomplete) {
                console.log('jQuery UI autocomplete is available');
                $("#enrollmentSearch").autocomplete({
                    source: function(request, response) {
                        console.log('Autocomplete request:', request.term);
                        $.ajax({
                            url: "search_enrollment.php",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                console.log('Autocomplete response:', data);
                                response(data);
                            },
                            error: function(xhr, status, error) {
                                console.error("Autocomplete error:", error);
                            }
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        console.log('Selected item:', ui.item);
                        event.preventDefault();
                        $(this).val(ui.item.value);
                    }
                });
            } else {
                console.error("jQuery UI autocomplete is not available");
                console.log('jQuery UI object:', $.ui);
                console.log('jQuery UI widget factory:', $.widget);
            }
        });
    </script>
</body>
</html>
