<?php
session_start();
require_once '../../config/db_connection.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

// Security check
if (!isset($_SESSION['admin_id'])) {
    die('Unauthorized access');
}

$db = new Database();
$conn = $db->connect();

try {
    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('Alumni Network System')
        ->setLastModifiedBy('Alumni Network System')
        ->setTitle('Alumni Records Export')
        ->setSubject('Alumni Records Export')
        ->setDescription('Alumni records exported on ' . date('Y-m-d H:i:s'));

    // Headers structure with main categories and subheaders
    $headers = [
        // Basic Information
        'A1' => 'Basic Information',
        'A2' => 'Full Name',
        'B2' => 'Email',
        'C2' => 'Phone',
        'D2' => 'Date of Birth',
        
        // Educational Details
        'E1' => 'Educational Details',
        'E2' => 'University Name',
        'F2' => 'Course',
        'G2' => 'Graduation Year',
        
        // Professional Status
        'H1' => 'Professional Status',
        'H2' => 'Current Status',
        'I2' => 'Company Name',
        'J2' => 'Position',
        
        // Technical Skills
        'K1' => 'Technical Skills',
        'K2' => 'Language/Technology',
        'L2' => 'Tools',
        'M2' => 'Technologies',
        'N2' => 'Proficiency Level',
        
        // Projects Count (simple column)
        'O1' => 'Projects',
        'O2' => 'Total Projects',
        
        // Career Goals
        'P1' => 'Career Goals',
        'P2' => 'Goals'
    ];

    // Modified SQL query to include course
    $sql = "SELECT 
            u.*,
            ed.university_name,
            ed.graduation_year,
            ed.enrollment_number,
            ps.current_status,
            ps.company_name,
            ps.position,
            GROUP_CONCAT(DISTINCT CONCAT(s.language_specialization, '|', s.tools, '|', s.technologies, '|', s.proficiency_level) SEPARATOR ';;') as skills,
            COUNT(DISTINCT p.title) as project_count,
            GROUP_CONCAT(DISTINCT cg.description SEPARATOR ';;') as career_goals,
            CASE 
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}BCA[0-9]+$' THEN 'BCA'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}MCA[0-9]+$' THEN 'MCA'
                ELSE 'Unknown'
            END as course
            FROM users u 
            LEFT JOIN educational_details ed ON u.user_id = ed.user_id 
            LEFT JOIN professional_status ps ON u.user_id = ps.user_id 
            LEFT JOIN skills s ON u.user_id = s.user_id 
            LEFT JOIN projects p ON u.user_id = p.user_id 
            LEFT JOIN career_goals cg ON u.user_id = cg.user_id
            GROUP BY u.user_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $alumni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
    }

    // Merge main header cells
    $sheet->mergeCells("A1:D1"); // Basic Information
    $sheet->mergeCells("E1:G1"); // Educational Details
    $sheet->mergeCells("H1:J1"); // Professional Status
    $sheet->mergeCells("K1:N1"); // Technical Skills
    $sheet->mergeCells("O1:O2"); // Projects (single column)
    $sheet->mergeCells("P1:P2"); // Career Goals

    // Add data rows
    $row = 3;
    foreach ($alumni as $record) {
        // Basic Information
        $sheet->setCellValue('A' . $row, $record['fullname'] ?? 'N/A');
        $sheet->setCellValue('B' . $row, $record['email'] ?? 'N/A');
        $sheet->setCellValue('C' . $row, $record['phone'] ?? 'N/A');
        $sheet->setCellValue('D' . $row, $record['dob'] ?? 'N/A');
        
        // Educational Details
        $sheet->setCellValue('E' . $row, $record['university_name'] ?? 'N/A');
        $sheet->setCellValue('F' . $row, $record['course'] ?? 'N/A');
        $sheet->setCellValue('G' . $row, $record['graduation_year'] ?? 'N/A');
        
        // Professional Status
        $sheet->setCellValue('H' . $row, $record['current_status'] ?? 'N/A');
        $sheet->setCellValue('I' . $row, $record['company_name'] ?? 'N/A');
        $sheet->setCellValue('J' . $row, $record['position'] ?? 'N/A');
        
        // Technical Skills
        if (!empty($record['skills'])) {
            $skillsArray = explode(';;', $record['skills']);
            $skillData = explode('|', $skillsArray[0]); // Get first skill set
            $sheet->setCellValue('K' . $row, $skillData[0] ?? 'N/A'); // Language
            $sheet->setCellValue('L' . $row, $skillData[1] ?? 'N/A'); // Tools
            $sheet->setCellValue('M' . $row, $skillData[2] ?? 'N/A'); // Technologies
            $sheet->setCellValue('N' . $row, $skillData[3] ?? 'N/A'); // Proficiency
        } else {
            $sheet->setCellValue('K' . $row, 'N/A');
            $sheet->setCellValue('L' . $row, 'N/A');
            $sheet->setCellValue('M' . $row, 'N/A');
            $sheet->setCellValue('N' . $row, 'N/A');
        }
        
        // Projects Count
        $sheet->setCellValue('O' . $row, ($record['project_count'] ?? '0') . ' Projects');
        
        // Career Goals
        $sheet->setCellValue('P' . $row, $record['career_goals'] ?? 'N/A');
        
        $row++;
    }

    // Style the headers
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2F75B5'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];

    // Apply styles to all headers
    $sheet->getStyle("A1:P2")->applyFromArray($headerStyle);

    // Set column widths
    foreach (range('A', 'P') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Add alternating row colors
    for ($i = 3; $i <= $row; $i++) {
        if ($i % 2 == 0) {
            $sheet->getStyle("A{$i}:P{$i}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('F5F5F5'));
        }
    }

    // Enable text wrapping for cells that might contain long content
    $sheet->getStyle('M3:M' . $row)->getAlignment()->setWrapText(true); // Technologies
    $sheet->getStyle('P3:P' . $row)->getAlignment()->setWrapText(true); // Career Goals

    // Set the filename to be more company-focused
    header('Content-Disposition: attachment;filename="alumni_professional_profiles_' . date('Y-m-d') . '.xlsx"');

    // Create Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
} 