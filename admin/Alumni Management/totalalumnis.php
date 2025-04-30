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
            GROUP_CONCAT(DISTINCT s.language_specialization) as languages,
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
    <!-- <link rel="stylesheet" href="../../assets/css/portfolio.css"> -->
    <link rel="stylesheet" href="../../assets/css/navigation.css">
    <link rel="stylesheet" href="../../assets/css/modal.css"> <!-- Added modal CSS -->
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
        .alumni-actions .btn {
            padding: 0.15rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
        }
        .alumni-actions .btn i {
            font-size: 0.75rem;
            margin-right: 2px;
        }
        .alumni-actions .btn-sm {
            padding: 0.15rem 0.4rem;
        }
        .alumni-actions .delete-alumni {
            padding: 0.1rem 0.3rem;
            font-size: 0.7rem;
        }
        .alumni-actions .delete-alumni i {
            font-size: 0.7rem;
            margin-right: 1px;
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
        .skills-list {
            max-height: 60px;
            overflow-y: auto;
            font-size: 0.8em;
        }
        .skill-item {
            display: inline-flex;
            align-items: center;
            margin-right: 8px;
            white-space: nowrap;
        }
        .skill-item .badge {
            font-size: 0.85em;
            font-weight: normal;
        }
        .skill-item .badge.small {
            font-size: 0.75em;
            padding: 0.2em 0.4em;
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
        .sortable {
            cursor: pointer;
            position: relative;
        }
        .sortable:hover {
            background-color: #e9ecef;
        }
        .sortable i {
            margin-left: 5px;
            font-size: 0.8em;
        }
        .sortable.asc i.fa-sort-up,
        .sortable.desc i.fa-sort-down {
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Custom Modal -->
    <div id="customModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmation</h4>
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
            <!-- <a href="totalalumnis.php" class="active">Total Alumni</a> -->
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Add this after the nav section -->
    <div id="alertContainer"></div>

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

        <hr class="section-divider">

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
                        </optgroup>
                        <optgroup label="Education">
                            <option value="enrollment_number">Enrollment Number</option>
                            <option value="graduation_year">Graduation Year</option>
                        </optgroup>
                        <optgroup label="Professional">
                            <option value="company_name">Company Name</option>
                            <option value="position">Position</option>
                        </optgroup>
                        <optgroup label="Skills & Projects">
                            <option value="language">Language</option>
                            <option value="project_title">Project Title</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="Enter search term...">
                    <select class="form-select" id="languageSelect" name="searchTerm" style="display: none;">
                        <option value="">Select Language</option>
                        <option value="Java">Java</option>
                        <option value="Python">Python</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="C++">C++</option>
                        <option value="C#">C#</option>
                        <option value="PHP">PHP</option>
                        <option value="Ruby">Ruby</option>
                        <option value="Swift">Swift</option>
                        <option value="Kotlin">Kotlin</option>
                        <option value="Go">Go</option>
                        <option value="Rust">Rust</option>
                        <option value="TypeScript">TypeScript</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="SQL">SQL</option>
                        <option value="NoSQL">NoSQL</option>
                        <option value="React">React</option>
                        <option value="Angular">Angular</option>
                        <option value="Vue.js">Vue.js</option>
                        <option value="Node.js">Node.js</option>
                        <option value="Django">Django</option>
                        <option value="Flask">Flask</option>
                        <option value="Spring">Spring</option>
                        <option value="Laravel">Laravel</option>
                    </select>
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
                    <select class="form-select" id="enrollmentFormatFilter" name="enrollmentFormatFilter">
                        <option value="">All Enrollment Formats</option>
                        <option value="yy_course_number">YY|Course|Number Format</option>
                        <option value="15_digit">15-Digit Format</option>
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
                    <select class="form-select" id="recordsPerPage" name="recordsPerPage">
                        <option value="5" selected>5 Records</option>
                        <option value="10">10 Records</option>
                        <option value="25">25 Records</option>
                        <option value="50">50 Records</option>
                        <option value="100">100 Records</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="../Birthdays/wish_birthdays.php" class="btn btn-primary w-100">
                        <i class="fas fa-birthday-cake"></i> Send Birthday Wishes
                    </a>
                </div>
            </div>
        </div>

        <!-- Alumni Table -->
        <div class="table-responsive">
            <table class="alumni-table">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="fullname">Name <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="email">Email <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="enrollment_number">Enrollment Number <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="graduation_year">Graduation Year <i class="fas fa-sort"></i></th>
                        <th class="sortable" data-sort="current_status">Professional Status <i class="fas fa-sort"></i></th>
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
                                if ($alumni['languages']) {
                                    $languages = explode(',', $alumni['languages']);
                                    foreach ($languages as $language) {
                                        // Check if language is a JSON array
                                        $decodedLanguages = json_decode($language, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedLanguages)) {
                                            // Handle array of languages
                                            foreach ($decodedLanguages as $singleLanguage) {
                                                echo "<span class='badge bg-primary me-1'>" . htmlspecialchars(trim($singleLanguage)) . "</span>";
                                            }
                                        } else {
                                            // Handle single language
                                            echo "<span class='badge bg-primary me-1'>" . htmlspecialchars(trim($language)) . "</span>";
                                        }
                                    }
                                } else {
                                    echo "No languages listed";
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
                            <button type="button" class="btn btn-danger btn-sm delete-alumni" 
                                    data-user-id="<?php echo $alumni['user_id']; ?>" 
                                    title="Delete Alumni">
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
    <script src="../../assets/js/modal.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event delegation for delete buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-alumni')) {
                    const button = e.target.closest('.delete-alumni');
                    const userId = button.dataset.userId;
                    deleteAlumni(userId);
                }
            });

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
        let currentSortColumn = 'fullname';
        let currentSortOrder = 'ASC';
        
        // Function to update search results
        function updateSearchResults(page = 1) {
            if (isSearching) return;
            isSearching = true;

            const searchData = {
                searchCategory: $('#searchCategory').val(),
                searchTerm: $('#searchTerm').val(),
                verificationFilter: $('#verificationFilter').val(),
                employmentFilter: $('#employmentFilter').val(),
                enrollmentFormatFilter: $('#enrollmentFormatFilter').val(),
                sortBy: currentSortColumn,
                sortOrder: currentSortOrder,
                page: page,
                limit: itemsPerPage
            };

            $.ajax({
                url: 'search_alumni.php',
                method: 'POST',
                data: searchData,
                dataType: 'json',
                success: function(response) {
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
                if (page && !$(this).parent().hasClass('disabled')) {
                    updateSearchResults(page);
                }
            });
        }
        
        function deleteAlumni(userId) {
            console.log('DEBUG: Starting deleteAlumni function with userId:', userId);
            customConfirm('Are you sure you want to delete this alumni? This will permanently delete all their data including certificates.', () => {
                console.log('DEBUG: User confirmed deletion, making fetch request');
                fetch('delete_alumni.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => {
                    console.log('DEBUG: Received response from server');
                    return response.json();
                })
                .then(data => {
                    console.log('DEBUG: Parsed response data:', data);
                    if (data.status === 'success') {
                        console.log('DEBUG: Success response received');
                        showAlert('success', 'Alumni removed successfully');
                        // Remove the deleted row from the table
                        const deleteButton = document.querySelector(`button[data-user-id="${userId}"]`);
                        console.log('DEBUG: Found delete button:', deleteButton);
                        if (deleteButton) {
                            const row = deleteButton.closest('tr');
                            console.log('DEBUG: Found row to delete:', row);
                            if (row) {
                                console.log('DEBUG: Starting row removal animation');
                                row.style.transition = 'opacity 0.3s';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    console.log('DEBUG: Removing row from DOM');
                                    row.remove();
                                    // Update the total count
                                    const totalCountElement = document.querySelector('.stat-card:first-child p');
                                    console.log('DEBUG: Found total count element:', totalCountElement);
                                    if (totalCountElement) {
                                        const totalCount = parseInt(totalCountElement.textContent) - 1;
                                        console.log('DEBUG: Updating total count to:', totalCount);
                                        totalCountElement.textContent = totalCount;
                                    }
                                    
                                    // Update the record count
                                    const totalRecordsElement = document.getElementById('totalRecords');
                                    console.log('DEBUG: Found total records element:', totalRecordsElement);
                                    if (totalRecordsElement) {
                                        const totalRecords = parseInt(totalRecordsElement.textContent) - 1;
                                        console.log('DEBUG: Updating total records to:', totalRecords);
                                        totalRecordsElement.textContent = totalRecords;
                                        
                                        // If no records left, show a message
                                        if (totalRecords === 0) {
                                            console.log('DEBUG: No records left, showing empty message');
                                            const tbody = document.getElementById('alumniTableBody');
                                            if (tbody) {
                                                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No alumni records found</td></tr>';
                                            }
                                        }
                                    }
                                }, 300);
                            } else {
                                console.log('DEBUG: Could not find parent row');
                            }
                        } else {
                            console.log('DEBUG: Delete button not found');
                        }
                    } else {
                        console.log('DEBUG: Error response received:', data.message);
                        showAlert('error', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('DEBUG: Error in delete process:', error);
                    showAlert('error', 'An error occurred while removing the alumni');
                });
            });
        }

        function verifyAlumni(userId) {
            console.log('Verifying alumni:', userId);
            customConfirm('Are you sure you want to verify this alumni?', () => {
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
                const selectedValue = $(this).val();
                if (selectedValue === 'language') {
                    $('#searchTerm').hide();
                    $('#languageSelect').show();
                } else {
                    $('#searchTerm').show();
                    $('#languageSelect').hide();
                }
            });

            // Handle language select change
            $('#languageSelect').on('change', function() {
                currentPage = 1;
                updateSearchResults();
            });

            // Handle enrollment format filter change
            $('#enrollmentFormatFilter').on('change', function() {
                const selectedFormat = $(this).val();
                // Always trigger search, even when empty (All Enrollment Formats)
                currentPage = 1;
                updateSearchResults();
            });

            // Handle filter changes
            $('#verificationFilter, #employmentFilter, #sortBy').on('change', function() {
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

            // Add click handlers for sortable columns
            $('.sortable').on('click', function() {
                const column = $(this).data('sort');
                
                // Update sort icons
                $('.sortable').removeClass('asc desc');
                $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                
                if (currentSortColumn === column) {
                    // Toggle sort order
                    currentSortOrder = currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
                } else {
                    // New column, default to ascending
                    currentSortColumn = column;
                    currentSortOrder = 'ASC';
                }
                
                // Update sort icon
                $(this).addClass(currentSortOrder.toLowerCase());
                $(this).find('i')
                    .removeClass('fa-sort')
                    .addClass(currentSortOrder === 'ASC' ? 'fa-sort-up' : 'fa-sort-down');
                
                // Update sort dropdown to match
                $('#sortBy').val(column);
                
                // Trigger search with new sort
                updateSearchResults(currentPage);
            });

            // Add debug logging to filter changes
            $('#searchCategory, #verificationFilter, #employmentFilter, #enrollmentFormatFilter, #sortBy').on('change', function() {
                const filterName = $(this).attr('id');
                const filterValue = $(this).val();
                console.log('Filter changed:', filterName, filterValue);
            });
        });
    </script>
</body>
</html>
