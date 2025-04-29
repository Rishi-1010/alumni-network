<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = $_POST['title'];
                $description = $_POST['description'];
                $event_date = $_POST['event_date'];
                $event_time = $_POST['event_time'];
                $location = $_POST['location'];
                $event_type = $_POST['event_type'];
                $max_attendees = $_POST['max_attendees'];
                $registration_deadline = $_POST['registration_deadline'];
                
                $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, location, event_type, max_attendees, registration_deadline, status) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'upcoming')");
                $stmt->execute([$title, $description, $event_date, $event_time, $location, $event_type, $max_attendees, $registration_deadline]);
                break;
                
            case 'update':
                $event_id = $_POST['event_id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $event_date = $_POST['event_date'];
                $event_time = $_POST['event_time'];
                $location = $_POST['location'];
                $event_type = $_POST['event_type'];
                $max_attendees = $_POST['max_attendees'];
                $registration_deadline = $_POST['registration_deadline'];
                $status = $_POST['status'];
                
                $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, event_type=?, max_attendees=?, registration_deadline=?, status=? WHERE event_id=?");
                $stmt->execute([$title, $description, $event_date, $event_time, $location, $event_type, $max_attendees, $registration_deadline, $status, $event_id]);
                break;
                
            case 'delete':
                $event_id = $_POST['event_id'];
                $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
                $stmt->execute([$event_id]);
                break;
        }
    }
}

// Fetch all events
$stmt = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/navigation.css">
    <link rel="stylesheet" href="../../assets/css/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        @media (max-width: 768px) {
            .dashboard-nav {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .logo {
                width: 100%;
                justify-content: center;
            }

            .dashboard-navbar {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .dashboard-navbar a {
                padding: 0.5rem;
                font-size: 0.9rem;
            }
        }

        .events-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .event-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .event-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }
        
        .event-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .event-actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
        }
        
        .event-details {
            margin-bottom: 1.5rem;
            color: #555;
            line-height: 1.6;
        }
        
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.25rem;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }
        
        .event-meta-item i {
            color: #007bff;
            font-size: 1.1rem;
        }
        
        .event-status {
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .status-upcoming {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-ongoing {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-past {
            background: #f5f5f5;
            color: #616161;
        }
        
        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            border-radius: 12px 12px 0 0;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-outline-primary {
            border: 1px solid #007bff;
            color: #007bff;
        }
        
        .btn-outline-primary:hover {
            background: #007bff;
            color: white;
        }
        
        .btn-outline-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }
        
        @media (max-width: 768px) {
            .events-container {
                padding: 1rem;
            }
            
            .event-meta {
                gap: 1rem;
            }
            
            .event-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .event-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        /* Add these new styles */
        .event-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-right: 1rem;
        }

        .event-type-physical {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .event-type-virtual {
            background: #e3f2fd;
            color: #1976d2;
        }

        .event-meta-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
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
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <div class="events-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Events Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                <i class="fas fa-plus"></i> Create New Event
            </button>
        </div>

        <!-- Events List -->
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-6 mb-4">
                <div class="event-card">
                    <div class="event-header">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-actions">
                            <button class="btn btn-sm btn-outline-primary edit-event" data-event-id="<?php echo $event['event_id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-event" data-event-id="<?php echo $event['event_id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="event-details">
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                    </div>
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
                    <div class="event-meta-footer">
                        <div class="d-flex align-items-center">
                            <span class="event-type-badge <?php echo $event['event_type'] === 'physical' ? 'event-type-physical' : 'event-type-virtual'; ?>">
                                <i class="fas <?php echo $event['event_type'] === 'physical' ? 'fa-building' : 'fa-video'; ?>"></i>
                                <?php echo ucfirst($event['event_type']); ?>
                            </span>
                            <span class="event-status status-<?php echo $event['status']; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        <span class="text-muted">
                            <i class="fas fa-users"></i> Max Attendees: <?php echo $event['max_attendees']; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Date</label>
                                <input type="date" class="form-control" name="event_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Time</label>
                                <input type="time" class="form-control" name="event_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Type</label>
                            <select class="form-select" name="event_type" required>
                                <option value="physical">Physical</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Attendees</label>
                            <input type="number" class="form-control" name="max_attendees" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Registration Deadline</label>
                            <input type="datetime-local" class="form-control" name="registration_deadline" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="event_id" id="edit_event_id">
                        <div class="mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Date</label>
                                <input type="date" class="form-control" name="event_date" id="edit_event_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Time</label>
                                <input type="time" class="form-control" name="event_time" id="edit_event_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" id="edit_location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Type</label>
                            <select class="form-select" name="event_type" id="edit_event_type" required>
                                <option value="physical">Physical</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Attendees</label>
                            <input type="number" class="form-control" name="max_attendees" id="edit_max_attendees" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Registration Deadline</label>
                            <input type="datetime-local" class="form-control" name="registration_deadline" id="edit_registration_deadline" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="upcoming">Upcoming</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="past">Past</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="customModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="modalConfirm">Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/modal.js"></script>
    <script>
        // Edit Event
        document.querySelectorAll('.edit-event').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.dataset.eventId;
                // Fetch event details and populate modal
                fetch(`get_event.php?id=${eventId}`)
                    .then(response => response.json())
                    .then(event => {
                        document.getElementById('edit_event_id').value = event.event_id;
                        document.getElementById('edit_title').value = event.title;
                        document.getElementById('edit_description').value = event.description;
                        document.getElementById('edit_event_date').value = event.event_date;
                        document.getElementById('edit_event_time').value = event.event_time;
                        document.getElementById('edit_location').value = event.location;
                        document.getElementById('edit_event_type').value = event.event_type;
                        document.getElementById('edit_max_attendees').value = event.max_attendees;
                        document.getElementById('edit_registration_deadline').value = event.registration_deadline;
                        document.getElementById('edit_status').value = event.status;
                        
                        new bootstrap.Modal(document.getElementById('editEventModal')).show();
                    });
            });
        });

        // Delete Event
        document.querySelectorAll('.delete-event').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.dataset.eventId;
                const eventTitle = this.closest('.event-card').querySelector('h3').textContent;
                
                customConfirm(`Are you sure you want to delete the event "${eventTitle}"?`, () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="event_id" value="${eventId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });

        // Show success/error messages
        <?php if (isset($_SESSION['message'])): ?>
            customConfirm('<?php echo $_SESSION['message']; ?>', () => {
                <?php unset($_SESSION['message']); ?>
            });
        <?php endif; ?>
    </script>
</body>
</html> 