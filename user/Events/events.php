<?php
session_start();
require_once '../../config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authentication/Login/login.php");
    exit();
}

// Get events data
$db = new Database();
$conn = $db->connect();

try {
    // Fetch user's enrolled events
    $enrolledStmt = $conn->prepare("
        SELECT e.*, er.registration_date 
        FROM events e 
        INNER JOIN event_registrations er ON e.event_id = er.event_id 
        WHERE er.user_id = ? 
        ORDER BY e.event_date ASC
    ");
    $enrolledStmt->execute([$_SESSION['user_id']]);
    $enrolledEvents = $enrolledStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all events, ordered by date
    $stmt = $conn->prepare("
        SELECT * FROM events 
        ORDER BY 
            CASE 
                WHEN status = 'upcoming' THEN 1
                WHEN status = 'ongoing' THEN 2
                WHEN status = 'past' THEN 3
                WHEN status = 'cancelled' THEN 4
            END,
            event_date ASC
    ");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Alumni Network</title>
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

        /* Container and Layout */
        .events-container {
            padding: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .events-header h1 {
            margin: 0;
            font-size: 2rem;
            color: #333;
        }

        /* Filters */
        .events-filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 20px;
            background: #f8f9fa;
            color: #666;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-weight: 500;
        }

        .filter-btn:hover {
            background: #e9ecef;
        }

        .filter-btn.active {
            background: #007bff;
            color: white;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2.5rem;
            padding: 0.5rem;
        }

        /* Event Card */
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Event Header */
        .event-header {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .event-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            line-height: 1.4;
        }

        /* Event Meta */
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
            font-size: 0.95rem;
        }

        .event-meta-item i {
            color: #007bff;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Event Body */
        .event-body {
            flex: 1;
        }

        .event-description {
            color: #555;
            line-height: 1.6;
            margin: 0;
            font-size: 1rem;
        }

        /* Event Footer */
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            gap: 1.5rem;
            margin-top: auto;
        }

        .event-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        /* Event Type and Status Badges */
        .event-type,
        .event-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 0.875rem;
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

        .status-upcoming {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-ongoing {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-past {
            background: #f5f5f5;
            color: #616161;
        }

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }

        /* Register Button */
        .register-btn {
            padding: 0.75rem 2rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .register-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .events-grid {
                grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .events-container {
                padding: 2rem 1.5rem;
            }

            .events-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .events-filters {
                width: 100%;
                justify-content: flex-start;
                gap: 0.75rem;
            }

            .filter-btn {
                padding: 0.6rem 1.25rem;
                font-size: 0.9rem;
            }

            .events-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                padding: 0;
            }

            .event-card {
                padding: 1.5rem;
                gap: 1.5rem;
            }

            .event-header {
                gap: 1rem;
                padding-bottom: 1rem;
            }

            .event-meta {
                gap: 1rem;
            }

            .event-footer {
                flex-direction: column;
                gap: 1rem;
            }

            .event-badges {
                width: 100%;
                justify-content: center;
            }

            .register-btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Enrolled Events Section */
        .enrolled-events {
            margin-bottom: 4rem;
        }

        .enrolled-events-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .enrolled-events-header h2 {
            font-size: 1.5rem;
            color: #333;
            margin: 0;
        }

        .enrolled-events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }

        .enrolled-event-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .enrolled-event-info {
            flex: 1;
        }

        .enrolled-event-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .enrolled-event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .enrolled-event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .enrolled-event-meta-item i {
            color: #007bff;
            width: 16px;
            text-align: center;
        }

        .enrolled-date {
            font-size: 0.875rem;
            color: #666;
        }

        .section-divider {
            height: 1px;
            background: #eee;
            margin: 3rem 0;
        }

        @media (max-width: 768px) {
            .enrolled-events-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .enrolled-event-card {
                flex-direction: column;
                padding: 1.25rem;
                gap: 1rem;
                text-align: center;
            }

            .enrolled-event-meta {
                justify-content: center;
            }
        }

        .event-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-end;
        }

        .btn-cancel-registration {
            padding: 0.6rem 1.2rem;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-cancel-registration:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .registration-notice {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .event-actions {
                align-items: center;
                width: 100%;
            }

            .btn-cancel-registration,
            .registration-notice {
                width: 100%;
                justify-content: center;
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
            <a href="events.php" class="active">Events</a>
            <a href="../../Authentication/Login/logout.php">Logout</a>
        </div>
    </nav>

    <div class="events-container">
        <?php if (!empty($enrolledEvents)): ?>
        <div class="enrolled-events">
            <div class="enrolled-events-header">
                <h2>Your Enrolled Events</h2>
            </div>
            <div class="enrolled-events-grid">
                <?php foreach ($enrolledEvents as $event): ?>
                    <div class="enrolled-event-card">
                        <div class="enrolled-event-info">
                            <div class="enrolled-event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="enrolled-event-meta">
                                <div class="enrolled-event-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="enrolled-event-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                                </div>
                            </div>
                            <div class="enrolled-date">
                                Registered on <?php echo date('M j, Y', strtotime($event['registration_date'])); ?>
                            </div>
                        </div>
                        <div class="event-actions">
                            <div class="event-badges">
                                <span class="event-type <?php echo $event['event_type']; ?>">
                                    <i class="fas <?php echo $event['event_type'] === 'physical' ? 'fa-building' : 'fa-video'; ?>"></i>
                                    <?php echo ucfirst($event['event_type']); ?>
                                </span>
                                <span class="event-status status-<?php echo $event['status']; ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </div>
                            <?php if ($event['status'] === 'upcoming'): ?>
                                <button class="btn-cancel-registration" data-event-id="<?php echo $event['event_id']; ?>">
                                    <i class="fas fa-times"></i> Cancel Registration
                                </button>
                            <?php elseif ($event['status'] === 'ongoing'): ?>
                                <div class="registration-notice">
                                    <i class="fas fa-info-circle"></i> Cannot cancel ongoing event registration
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="section-divider"></div>
        <?php endif; ?>

        <div class="events-header">
            <h1>Alumni Events</h1>
            <div class="events-filters">
                <button class="filter-btn active" data-filter="all">All Events</button>
                <button class="filter-btn" data-filter="upcoming">Upcoming</button>
                <button class="filter-btn" data-filter="ongoing">Ongoing</button>
                <button class="filter-btn" data-filter="past">Past</button>
            </div>
        </div>

        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card" data-status="<?php echo $event['status']; ?>">
                    <div class="event-header">
                        <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                        <div class="event-meta">
                            <div class="event-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                            </div>
                            <div class="event-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="event-body">
                        <p class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </p>
                    </div>
                    <div class="event-footer">
                        <div class="event-badges">
                            <span class="event-type <?php echo $event['event_type']; ?>">
                                <i class="fas <?php echo $event['event_type'] === 'physical' ? 'fa-building' : 'fa-video'; ?>"></i>
                                <?php echo ucfirst($event['event_type']); ?>
                            </span>
                            <span class="event-status status-<?php echo $event['status']; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        <?php if ($event['status'] === 'upcoming'): ?>
                            <a href="register_event.php?id=<?php echo $event['event_id']; ?>" class="register-btn">Register Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="customModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCancel">No, Keep Registration</button>
                <button type="button" class="btn btn-primary" id="modalConfirm">Yes, Cancel Registration</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../assets/js/modal.js"></script>
    <script>
        // Event filtering
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                document.querySelector('.filter-btn.active').classList.remove('active');
                button.classList.add('active');

                const filter = button.dataset.filter;
                const cards = document.querySelectorAll('.event-card');

                cards.forEach(card => {
                    if (filter === 'all' || card.dataset.status === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Event registration cancellation
        document.querySelectorAll('.btn-cancel-registration').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.dataset.eventId;
                const eventTitle = this.closest('.enrolled-event-card').querySelector('.enrolled-event-title').textContent;
                
                customConfirm(`Are you sure you want to cancel your registration for "${eventTitle}"?`, function() {
                    window.location.href = `cancel_registration.php?event_id=${eventId}`;
                });
            });
        });
    </script>
</body>
</html> 