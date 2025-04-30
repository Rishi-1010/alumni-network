<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

$db = new Database();
$conn = $db->connect();

try {
    // Base query
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

    $whereConditions = [];
    $params = [];

    // Handle search parameters
    $searchCategory = $_POST['searchCategory'] ?? '';
    $searchTerm = $_POST['searchTerm'] ?? '';
    $verificationFilter = $_POST['verificationFilter'] ?? '';
    $employmentFilter = $_POST['employmentFilter'] ?? '';
    $sortBy = $_POST['sortBy'] ?? 'fullname';
    $sortOrder = $_POST['sortOrder'] ?? 'ASC';
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10; // Default to 10 records
    $offset = ($page - 1) * $limit;

    // Validate sort order
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    // Validate sort column to prevent SQL injection
    $allowedSortColumns = ['fullname', 'email', 'enrollment_number', 'graduation_year', 'current_status', 'verification_status'];
    if (!in_array($sortBy, $allowedSortColumns)) {
        $sortBy = 'fullname';
    }

    // Handle search term
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
            case 'graduation_year':
                $whereConditions[] = "ed.graduation_year = ?";
                $params[] = $searchTerm;
                break;
            case 'skill_name':
                $whereConditions[] = "s.language_specialization LIKE ?";
                $params[] = "%$searchTerm%";
                break;
            case 'project_title':
                $whereConditions[] = "p.title LIKE ?";
                $params[] = "%$searchTerm%";
                break;
            case 'company_name':
                $whereConditions[] = "ps.company_name LIKE ?";
                $params[] = "%$searchTerm%";
                break;
            case 'position':
                $whereConditions[] = "ps.position LIKE ?";
                $params[] = "%$searchTerm%";
                break;
            case 'all':
                $whereConditions[] = "(u.fullname LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR 
                                      ed.enrollment_number LIKE ? OR s.language_specialization LIKE ? OR 
                                      p.title LIKE ? OR ps.company_name LIKE ? OR ps.position LIKE ?)";
                $params = array_merge($params, [
                    "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", 
                    "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", 
                    "%$searchTerm%", "%$searchTerm%"
                ]);
                break;
        }
    }

    // Handle filters
    if ($verificationFilter !== '') {
        $whereConditions[] = "ed.verification_status = ?";
        $params[] = $verificationFilter;
    }

    if ($employmentFilter !== '') {
        $whereConditions[] = "ps.current_status = ?";
        $params[] = $employmentFilter;
    }

    // Handle enrollment format filter
    if (!empty($_POST['enrollmentFormatFilter'])) {
        $format = $_POST['enrollmentFormatFilter'];
        
        if ($format === 'yy_course_number') {
            // Filter for YY|Course|Number format (e.g., 05BCA69)
            $whereConditions[] = "ed.enrollment_number REGEXP '^[0-9]{2}[A-Za-z]+[0-9]+$'";
        } elseif ($format === '15_digit') {
            // Filter for 15-digit format (e.g., 202107100510074)
            $whereConditions[] = "ed.enrollment_number REGEXP '^[0-9]{15}$'";
        }
    }

    // Add WHERE clause if conditions exist
    if (!empty($whereConditions)) {
        $baseQuery .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Add GROUP BY
    $baseQuery .= " GROUP BY u.user_id";

    // Count total results for pagination
    $countQuery = "SELECT COUNT(DISTINCT u.user_id) as total FROM users u
                   LEFT JOIN educational_details ed ON u.user_id = ed.user_id
                   LEFT JOIN professional_status ps ON u.user_id = ps.user_id
                   LEFT JOIN skills s ON u.user_id = s.user_id
                   LEFT JOIN projects p ON u.user_id = p.user_id";
    
    if (!empty($whereConditions)) {
        $countQuery .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    $stmt = $conn->prepare($countQuery);
    $stmt->execute($params);
    $totalResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalResults / $limit);
    
    // Calculate record range
    $startRecord = ($page - 1) * $limit + 1;
    $endRecord = min($startRecord + $limit - 1, $totalResults);
    
    // If no results, set range to 0
    if ($totalResults == 0) {
        $startRecord = 0;
        $endRecord = 0;
    }

    // Add ORDER BY with special handling for enrollment_number
    if ($sortBy === 'enrollment_number') {
        // For enrollment numbers, we need to handle both formats
        $baseQuery .= " ORDER BY 
            CASE 
                WHEN ed.enrollment_number REGEXP '^[0-9]{15}$' THEN 1 
                ELSE 2 
            END,
            ed.enrollment_number " . $sortOrder;
    } else {
        $baseQuery .= " ORDER BY " . ($sortBy === 'registration_date' ? 'u.registration_date' : 
                       ($sortBy === 'graduation_year' ? 'ed.graduation_year' : 
                       ($sortBy === 'current_status' ? 'ps.current_status' : 'u.' . $sortBy))) . 
                       " " . $sortOrder;
    }

    // Add LIMIT for pagination
    $baseQuery .= " LIMIT $limit OFFSET $offset";

    $stmt = $conn->prepare($baseQuery);
    $stmt->execute($params);
    $alumniMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Start output buffering
    ob_start();
    
    // Generate HTML for results
    if (count($alumniMembers) > 0) {
        foreach ($alumniMembers as $alumni): ?>
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
                    <button onclick="deleteAlumni(<?php echo $alumni['user_id']; ?>)" 
                            class="btn btn-danger btn-sm" title="Delete Alumni">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php } else { ?>
        <tr>
            <td colspan="8" class="text-center">No alumni found matching your criteria</td>
        </tr>
    <?php }
    
    // Get the buffered content
    $html = ob_get_clean();
    
    // Return JSON response with HTML and pagination info
    echo json_encode([
        'html' => $html,
        'pagination' => [
            'total' => $totalResults,
            'pages' => $totalPages,
            'current' => $page,
            'limit' => $limit,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 