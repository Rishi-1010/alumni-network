<?php
session_start();
require_once '../../config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authentication/Login/login.php");
    exit();
}

// Check if event ID is provided
if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit();
}

$db = new Database();
$conn = $db->connect();
$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if user is already registered
    $checkStmt = $conn->prepare("
        SELECT * FROM event_registrations 
        WHERE event_id = ? AND user_id = ?
    ");
    $checkStmt->execute([$event_id, $user_id]);
    $existing_registration = $checkStmt->fetch();

    // Get event details
    $eventStmt = $conn->prepare("
        SELECT * FROM events 
        WHERE event_id = ? AND status = 'upcoming'
    ");
    $eventStmt->execute([$event_id]);
    $event = $eventStmt->fetch();

    if (!$event) {
        $_SESSION['error'] = "Event not found or registration closed.";
        header("Location: events.php");
        exit();
    }

    // Handle registration submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($existing_registration) {
            $_SESSION['error'] = "You are already registered for this event.";
        } else {
            // Check if event is full
            $countStmt = $conn->prepare("
                SELECT COUNT(*) as count 
                FROM event_registrations 
                WHERE event_id = ?
            ");
            $countStmt->execute([$event_id]);
            $registration_count = $countStmt->fetch()['count'];

            if ($registration_count >= $event['max_attendees']) {
                $_SESSION['error'] = "Sorry, this event is already full.";
            } else {
                // Register the user
                $regStmt = $conn->prepare("
                    INSERT INTO event_registrations (event_id, user_id, registration_date) 
                    VALUES (?, ?, NOW())
                ");
                $regStmt->execute([$event_id, $user_id]);

                $_SESSION['success'] = "Successfully registered for the event!";
                header("Location: events.php");
                exit();
            }
        }
    }

} catch(PDOException $e) {
    $_SESSION['error'] = "An error occurred. Please try again.";
    header("Location: events.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration - Alumni Network</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/modal.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Navigation Styles */
        .dashboard-nav {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .logo span {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }

        .dashboard-navbar {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .dashboard-navbar a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .dashboard-navbar a:hover {
            background: #f8f9fa;
            color: #007bff;
        }

        .dashboard-navbar a.active {
            background: #007bff;
            color: white;
        }

        /* Main Container */
        .registration-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        /* Event Details Card */
        .event-details {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .event-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            line-height: 1.4;
        }

        .event-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #555;
            font-size: 1rem;
        }

        .info-item i {
            color: #007bff;
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .event-description {
            color: #666;
            line-height: 1.6;
            font-size: 1rem;
            margin: 0;
        }

        /* Registration Form Card */
        .registration-form {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.5rem;
            color: #333;
            margin: 0 0 1rem 0;
        }

        .spots-left {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: #666;
            font-size: 1.1rem;
            margin: 0;
        }

        .spots-left i {
            color: #007bff;
        }

        /* Submit Button */
        form {
            display: flex;
            justify-content: center;
        }

        .submit-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 200px;
        }

        .submit-btn:hover:not(:disabled) {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .submit-btn:disabled {
            background: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .registration-container {
                margin: 2rem auto;
                padding: 0 1.5rem;
                gap: 1.5rem;
            }

            .event-details,
            .registration-form {
                padding: 1.5rem;
            }

            .event-title {
                font-size: 1.5rem;
            }

            .event-info {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .info-item {
                font-size: 0.95rem;
            }

            .submit-btn {
                width: 100%;
                padding: 0.875rem 2rem;
            }

            .spots-left {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
            <span>Alumni Network</span>
        </div>
        <div class="dashboard-navbar">
            <a href="../../alumni_dashboard.php">Dashboard</a>
            <a href="../../profile.php">Profile</a>
            <a href="../../connections.php">Connections</a>
            <a href="events.php">Events</a>
            <a href="../../Authentication/Login/logout.php">Logout</a>
        </div>
    </nav>

    <div class="registration-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="event-details">
            <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
            <div class="event-info">
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas <?php echo $event['event_type'] === 'physical' ? 'fa-building' : 'fa-video'; ?>"></i>
                    <span><?php echo ucfirst($event['event_type']); ?> Event</span>
                </div>
            </div>
            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
        </div>

        <div class="registration-form">
            <div class="form-header">
                <h2>Event Registration</h2>
                <?php if ($existing_registration): ?>
                    <p class="alert alert-success">You are already registered for this event.</p>
                <?php else: ?>
                    <?php
                        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?");
                        $countStmt->execute([$event_id]);
                        $registration_count = $countStmt->fetch()['count'];
                        $spots_left = $event['max_attendees'] - $registration_count;
                    ?>
                    <p class="spots-left">
                        <i class="fas fa-user-friends"></i>
                        <?php echo $spots_left; ?> spots left out of <?php echo $event['max_attendees']; ?>
                    </p>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <button type="submit" class="submit-btn" <?php echo ($existing_registration || $spots_left <= 0) ? 'disabled' : ''; ?>>
                    <?php echo $existing_registration ? 'Already Registered' : ($spots_left <= 0 ? 'Event Full' : 'Confirm Registration'); ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html> 