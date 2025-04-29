<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Authentication/Login/login.php");
    exit();
}

// Add this new check - redirect admin users to admin login
if (isset($_SESSION['admin_id'])) {
    header("Location: Authentication/AdminLogin/login.php");
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

    // Debug: Print the current date for reference
    echo "<!-- Current Date: " . date('Y-m-d') . " -->";

    // Fetch upcoming events - simplified query to match existing data
    $eventsQuery = "
        SELECT * FROM events 
        WHERE status = 'upcoming'
        ORDER BY event_date ASC 
        LIMIT 3
    ";
    
    $eventsStmt = $conn->prepare($eventsQuery);
    $eventsStmt->execute();
    $upcomingEvents = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Print the number of events found
    echo "<!-- Number of events found: " . count($upcomingEvents) . " -->";
    if (empty($upcomingEvents)) {
        echo "<!-- No events found in database -->";
    } else {
        foreach ($upcomingEvents as $event) {
            echo "<!-- Event found: " . htmlspecialchars($event['title']) . 
                 " Date: " . $event['event_date'] . 
                 " Status: " . $event['status'] . " -->";
        }
    }

} catch(PDOException $e) {
    echo "<!-- Database Error: " . $e->getMessage() . " -->";
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
    <style>
        /* Events Section Styles */
        .events-section {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .events-section h2 {
            margin-bottom: 1.5rem;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .view-all-btn {
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .view-all-btn:hover {
            color: #0056b3;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .event-header {
            margin-bottom: 1rem;
        }

        .event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-type {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .event-type.physical {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .event-type.virtual {
            background: #e3f2fd;
            color: #1976d2;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .register-btn {
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .register-btn:hover {
            background: #0056b3;
        }

        .spots-left {
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="assets/img/logo.png" alt="Alumni Network Logo">
            <span>Alumni Network</span>
        </div>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links" id="navLinks">
            <a href="alumni_dashboard.php" class="active">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="connections.php">Connections</a>
            <a href="jobs.php">Jobs</a>
            <a href="Authentication/Login/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Hi, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
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

        <!-- Events Section -->
        <div class="events-section">
            <h2>
                Upcoming Events
                <a href="user/Events/events.php" class="view-all-btn">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </h2>
            <div class="events-grid">
                <?php if (!empty($upcomingEvents)): ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="event-card">
                            <div class="event-header">
                                <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                                    </div>
                                </div>
                                <div class="event-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </div>
                            </div>
                            <div class="event-footer">
                                <span class="event-type <?php echo $event['event_type']; ?>">
                                    <i class="fas <?php echo $event['event_type'] === 'physical' ? 'fa-building' : 'fa-video'; ?>"></i>
                                    <?php echo ucfirst($event['event_type']); ?>
                                </span>
                                <a href="event_registration.php?id=<?php echo $event['event_id']; ?>" class="register-btn">
                                    Register Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="event-card">
                        <p>No upcoming events at the moment.</p>
                    </div>
                <?php endif; ?>
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
    <script>
        // Add event listeners to logout buttons
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButtons = document.querySelectorAll('a[href="Authentication/Login/logout.php"]');
            logoutButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    window.location.href = "Authentication/Login/logout.php";
                });
            });
        });
    </script>
</body>
</html> 