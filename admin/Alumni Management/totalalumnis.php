<?php
session_start();
require_once '../../config/db_connection.php';

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

    // Base query for alumni details with all necessary joins
    $baseQuery = "
        SELECT 
            u.*,
            ed.*,
            ps.current_status,
            ps.company_name,
            ps.position,
            GROUP_CONCAT(DISTINCT CONCAT(s.language_specialization, ' (', s.proficiency_level, ')')) as skills,
            GROUP_CONCAT(DISTINCT p.title) as projects
        FROM users u
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
        LEFT JOIN professional_status ps ON u.user_id = ps.user_id
        LEFT JOIN skills s ON u.user_id = s.user_id
        LEFT JOIN projects p ON u.user_id = p.user_id
    ";

    // Handle search and filters if submitted
    $whereConditions = [];
    $params = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $searchCategory = $_POST['searchCategory'] ?? '';
        $searchTerm = $_POST['searchTerm'] ?? '';
        $verificationFilter = $_POST['verificationFilter'] ?? '';
        $employmentFilter = $_POST['employmentFilter'] ?? '';
        $sortBy = $_POST['sortBy'] ?? 'fullname';

        if ($searchTerm !== '') {
            switch($searchCategory) {
                case 'fullname':
                case 'email':
                case 'phone':
                    $whereConditions[] = "u.$searchCategory LIKE ?";
                    $params[] = "%$searchTerm%";
                    break;
                case 'enrollment_number':
                    $whereConditions[] = "ed.enrollment_number LIKE ?";
                    $params[] = "%$searchTerm%";
                    break;
                case 'skill_name':
                    $whereConditions[] = "s.language_specialization LIKE ?";
                    $params[] = "%$searchTerm%";
                    break;
                case 'project_title':
                    $whereConditions[] = "p.title LIKE ?";
                    $params[] = "%$searchTerm%";
                    break;
                // Add more cases as needed
            }
        }

        if ($verificationFilter !== '') {
            $whereConditions[] = "ed.verification_status = ?";
            $params[] = $verificationFilter;
        }

        if ($employmentFilter !== '') {
            $whereConditions[] = "ps.current_status = ?";
            $params[] = $employmentFilter;
        }
    }

    // Add WHERE clause if conditions exist
    if (!empty($whereConditions)) {
        $baseQuery .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Add GROUP BY to handle the GROUP_CONCAT
    $baseQuery .= " GROUP BY u.user_id";

    // Add ORDER BY
    $sortBy = $_POST['sortBy'] ?? 'fullname';
    $baseQuery .= " ORDER BY " . ($sortBy === 'registration_date' ? 'u.registration_date' : 
                   ($sortBy === 'graduation_year' ? 'ed.graduation_year' : 'u.fullname')) . 
                   ($sortBy === 'registration_date' ? ' DESC' : ' ASC');

    $stmt = $conn->prepare($baseQuery);
    $stmt->execute($params);
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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/portfolio.css">
    <style>
        .alumni-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 0.9rem;
        }
        .alumni-table th, .alumni-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .alumni-table th {
            background-color: #f4f4f4;
            font-weight: 600;
        }
        .alumni-actions {
            display: flex;
            gap: 3px;
        }
        .search-controls {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .filter-controls {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        .badge-verified {
            background-color: #28a745;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: black;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card i {
            font-size: 1.5em;
            color: #007bff;
            margin-bottom: 5px;
        }
        .export-btn {
            margin-left: auto;
        }
        .skills-list, .projects-list {
            max-height: 60px;
            overflow-y: auto;
            font-size: 0.8em;
        }
        .tooltip-content {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-size: 0.8em;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .pagination {
            margin-top: 10px;
            justify-content: center;
        }
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .welcome-section {
            margin-bottom: 15px;
        }
        .welcome-section h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .dashboard-container {
            padding: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation and other dashboard components -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        
        <!-- <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button> -->
        <div class="dashboard-navbar" id="navLinks">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../profile.php">Profile</a>
            <!-- <a href="../connections.php">Connections</a>
            <a href="../jobs.php">Jobs</a> -->
            <a href="totalalumnis.php" class="active">Total Alumni</a>
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container container-fluid">
        <!-- Welcome Section -->

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3>Total Alumni</h3>
                <p><?php echo $totalAlumni; ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Verified Alumni</h3>
                <p><?php echo $verifiedAlumni; ?></p>
            </div>
        </div>

        <!-- Add this after your welcome section -->
        <div class="search-controls mb-4">
            <form id="searchForm" class="row g-3">
                <!-- Basic Info Search -->
                <div class="col-md-4">
                    <select class="form-select" id="searchCategory" name="searchCategory">
                        <option value="">Select Search Category</option>
                        <optgroup label="Basic Information">
                            <option value="fullname">Full Name</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="registration_date">Registration Date</option>
                        </optgroup>
                        <optgroup label="Education">
                            <option value="enrollment_number">Enrollment Number</option>
                            <option value="graduation_year">Graduation Year</option>
                            <option value="verification_status">Verification Status</option>
                        </optgroup>
                        <optgroup label="Professional">
                            <option value="current_status">Employment Status</option>
                            <option value="company_name">Company Name</option>
                            <option value="position">Position</option>
                        </optgroup>
                        <optgroup label="Skills & Projects">
                            <option value="skill_name">Skill</option>
                            <option value="project_title">Project Title</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="Enter search term...">
                </div>
                <div class="col-md-2">
                    <button type="reset" class="btn btn-secondary w-100">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Advanced Filters -->
        <div class="filter-controls mb-3">
            <div class="row g-3">
                <div class="col-md-2">
                    <select class="form-select" id="verificationFilter" name="verificationFilter">
                        <option value="">All Verification Status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="employmentFilter" name="employmentFilter">
                        <option value="">All Professional Status</option>
                        <option value="employed">Employed</option>
                        <option value="seeking">Seeking Opportunities</option>
                        <option value="student">Further Studies</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="sortBy" name="sortBy">
                        <option value="fullname">Sort by Name</option>
                        <option value="registration_date">Sort by Registration Date</option>
                        <option value="graduation_year">Sort by Graduation Year</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="sortOrder" name="sortOrder">
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="recordsPerPage" name="recordsPerPage">
                        <option value="5" selected>5 Records</option>
                        <option value="10">10 Records</option>
                        <option value="25">25 Records</option>
                        <option value="50">50 Records</option>
                        <option value="100">100 Records</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Alumni Table -->
        <div class="table-responsive">
            <table class="alumni-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Enrollment Number</th>
                        <th>Graduation Year</th>
                        <th>Professional Status</th>
                        <th>Skills</th>
                        <th>Verification</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="alumniTableBody">
                        <?php foreach ($alumniMembers as $alumni): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumni['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                                <td><?php echo htmlspecialchars($alumni['enrollment_number']); ?></td>
                        <td><?php echo htmlspecialchars($alumni['graduation_year'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($alumni['current_status'] === 'employed'): ?>
                                <div class="employment-info">
                                    <span class="badge bg-success">Employed</span>
                                    <div class="tooltip-content">
                                        Company: <?php echo htmlspecialchars($alumni['company_name']); ?><br>
                                        Position: <?php echo htmlspecialchars($alumni['position']); ?>
                                    </div>
                                </div>
                                    <?php else: ?>
                                <span class="badge bg-secondary"><?php echo ucfirst(htmlspecialchars($alumni['current_status'] ?? 'N/A')); ?></span>
                                    <?php endif; ?>
                                </td>
                        <td>
                            <div class="skills-list">
                                <?php
                                if ($alumni['skills']) {
                                    $skills = explode(',', $alumni['skills']);
                                    foreach ($skills as $skill) {
                                        echo "<span class='badge bg-info me-1'>" . htmlspecialchars($skill) . "</span>";
                                    }
                                } else {
                                    echo "No skills listed";
                                }
                                ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $alumni['verification_status'] === 'verified' ? 'badge-verified' : 'badge-pending'; ?>">
                                <?php echo ucfirst($alumni['verification_status']); ?>
                            </span>
                        </td>
                                <td class="alumni-actions">
                            <a href="view-portfolio.php?id=<?php echo $alumni['user_id']; ?>" 
                               class="btn btn-primary btn-sm" title="View Portfolio">
                                <i class="fas fa-eye"></i>
                            </a>
                                            <?php if ($alumni['verification_status'] !== 'verified'): ?>
                                <button onclick="verifyAlumni(<?php echo $alumni['user_id']; ?>)" 
                                        class="btn btn-success btn-sm" title="Verify Alumni">
                                    <i class="fas fa-check"></i>
                                </button>
                                            <?php endif; ?>
                            <button type="button" onclick="deleteAlumni(<?php echo $alumni['user_id']; ?>)" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Compact Pagination -->
            <div class="d-flex justify-content-center mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm" id="pagination">
                        <!-- Pagination will be dynamically inserted here -->
                    </ul>
                </nav>
            </div>
            
            <!-- Record Range Information -->
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="record-info">
                    Showing <span id="recordRange">records</span> of <span id="totalRecords">0</span> total records
                </div>
                <!-- <div class="export-info">
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportToExcel()">
                        <i class="fas fa-file-export"></i> Export to Excel
                    </button>
                </div> -->
            <button class="btn btn-success export-btn" onclick="exportToExcel()">
                <i class="fas fa-file-export"></i> Export to Excel
            </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if mobile menu button exists before adding event listener
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('active');
                });
            }
        });

        // Employment info tooltip
        document.querySelectorAll('.employment-info').forEach(info => {
            info.addEventListener('mouseenter', function() {
                this.querySelector('.tooltip-content').style.display = 'block';
            });
            info.addEventListener('mouseleave', function() {
                this.querySelector('.tooltip-content').style.display = 'none';
            });
        });

        // Define these functions in the global scope
        let currentPage = 1;
        let itemsPerPage = 5; // Changed default from 10 to 5
        let isSearching = false;
        let searchTimeout;
        
        // Function to update search results
        function updateSearchResults(page = 1) {
            if (isSearching) return;
            isSearching = true;

            const searchData = {
                searchCategory: $('#searchCategory').val(),
                searchTerm: $('#searchTerm').val(),
                verificationFilter: $('#verificationFilter').val(),
                employmentFilter: $('#employmentFilter').val(),
                sortBy: $('#sortBy').val(),
                sortOrder: $('#sortOrder').val(),
                page: page,
                limit: itemsPerPage
            };

            $.ajax({
                url: 'search_alumni.php',
                method: 'POST',
                data: searchData,
                dataType: 'json',
                success: function(response) {
                    console.log('Search response:', response);
                    if (response.error) {
                        showAlert('error', response.error);
                        return;
                    }

                    $('#alumniTableBody').html(response.html);
                    updatePagination(response.pagination);
                    updateRecordInfo(response.pagination);
                    
                    currentPage = page;
                    isSearching = false;
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', xhr.responseText, status, error);
                    showAlert('error', 'An error occurred while searching. Please try again.');
                    isSearching = false;
                }
            });
        }
        
        // Function to update record range information
        function updateRecordInfo(pagination) {
            const total = pagination.total;
            const start = pagination.startRecord;
            const end = pagination.endRecord;
            
            $('#totalRecords').text(total);
            
            if (total === 0) {
                $('#recordRange').text('0-0');
            } else {
                $('#recordRange').text(`${start}-${end}`);
            }
        }
        
        // Function to update pagination
        function updatePagination(pagination) {
            const totalPages = pagination.pages;
            const currentPage = pagination.current;
            const $pagination = $('#pagination');
            
            $pagination.empty();
            
            if (totalPages <= 1) {
                return; // Don't show pagination if there's only one page
            }
            
            // Previous button
            if (currentPage > 1) {
                $pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                    </li>
                `);
            } else {
                $pagination.append(`
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                `);
            }
            
            // Page numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            if (startPage > 1) {
                $pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </li>
                `);
                
                if (startPage > 2) {
                    $pagination.append(`
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    `);
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    $pagination.append(`
                        <li class="page-item active">
                            <span class="page-link">${i}</span>
                        </li>
                    `);
                } else {
                    $pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    $pagination.append(`
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    `);
                }
                
                $pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `);
            }
            
            // Next button
            if (currentPage < totalPages) {
                $pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                    </li>
                `);
            } else {
                $pagination.append(`
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                `);
            }
            
            // Add click event to pagination links
            $pagination.find('.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    updateSearchResults(page);
                }
            });
        }
        
        function deleteAlumni(userId) {
            if (confirm('Are you sure you want to delete this alumni? This will permanently delete all their data including certificates.')) {
                $.ajax({
                    url: 'delete_alumni.php',
                    type: 'POST',
                    data: { user_id: userId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', xhr.responseText);
                        alert('An error occurred while deleting the alumni. Please check the console for details.');
                    }
                });
            }
            return false;
        }

        function verifyAlumni(userId) {
            if (!confirm('Are you sure you want to verify this alumni?')) return;

            $.ajax({
                url: 'verify_alumni.php',
                method: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        showAlert('error', response.error);
                    } else {
                        showAlert('success', 'Alumni verified successfully');
                        updateSearchResults(currentPage);
                    }
                },
                error: function() {
                    showAlert('error', 'An error occurred while verifying the alumni');
                }
            });
        }

        function showAlert(type, message) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            const $alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
            
            $('#alertContainer').html($alert);
            setTimeout(() => $alert.alert('close'), 5000);
        }

        // Export functionality
        function exportToExcel() {
            window.location.href = 'export_alumni.php?' + $('#searchForm').serialize();
        }

        $(document).ready(function() {
            // Initialize search functionality
            console.log('Initializing search functionality');
            
            // Handle search form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                updateSearchResults();
            });

            // Handle search input with debounce
            $('#searchTerm').on('input', function() {
                clearTimeout(searchTimeout);
                // Reduce debounce time to 100ms for more responsive search
                searchTimeout = setTimeout(() => {
                    currentPage = 1;
                    updateSearchResults();
                }, 100);
            });

            // Handle category dropdown change
            $('#searchCategory').on('change', function() {
                // Clear the search term when category changes
                $('#searchTerm').val('');
                // Trigger search immediately when category changes
                currentPage = 1;
                updateSearchResults();
            });

            // Handle filter changes
            $('#verificationFilter, #employmentFilter, #sortBy, #sortOrder').on('change', function() {
                currentPage = 1;
                updateSearchResults();
            });

            // Handle records per page change
            $('#recordsPerPage').on('change', function() {
                itemsPerPage = parseInt($(this).val());
                currentPage = 1; // Reset to first page when changing records per page
                updateSearchResults();
            });

            // Handle pagination clicks
            $('#pagination').on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && !$(this).parent().hasClass('disabled')) {
                    updateSearchResults(page);
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initial search
            updateSearchResults();
        });
    </script>
</body>
</html>
