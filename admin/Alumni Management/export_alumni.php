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
        'E2' => 'Course',
        'F2' => 'Graduation Year',
        
        // Professional Status
        'G1' => 'Professional Status',
        'G2' => 'Current Status',
        'H2' => 'Company Name',
        'I2' => 'Position',
        
        // Technical Skills
        'J1' => 'Technical Skills',
        'J2' => 'Languages/Technologies',
        'K2' => 'Tools',
        'L2' => 'Technologies',
        'M2' => 'Proficiency Level',
        
        // Projects Count (simple column)
        'N1' => 'Projects',
        'N2' => 'Total Projects',
        
        // Career Goals
        'O1' => 'Career Goals',
        'O2' => 'Goals'
    ];

    // Modified SQL query to include course
    $sql = "SELECT 
            u.*,
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
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}BSC[0-9]+$' THEN 'BSC'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}MSC[0-9]+$' THEN 'MSC'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}BCOM[0-9]+$' THEN 'BCOM'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}MCOM[0-9]+$' THEN 'MCOM'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}BE[0-9]+$' THEN 'BE'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}ME[0-9]+$' THEN 'ME'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}BTECH[0-9]+$' THEN 'BTECH'
                WHEN ed.enrollment_number REGEXP '^[0-9]{2}MTECH[0-9]+$' THEN 'MTECH'
                WHEN ed.enrollment_number IS NOT NULL AND ed.enrollment_number != '' THEN 'Other'
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
    $sheet->mergeCells("E1:F1"); // Educational Details
    $sheet->mergeCells("G1:I1"); // Professional Status
    $sheet->mergeCells("J1:M1"); // Technical Skills
    $sheet->mergeCells("N1:N2"); // Projects (single column)
    $sheet->mergeCells("O1:O2"); // Career Goals

    // Add data rows
    $row = 3;
    foreach ($alumni as $record) {
        // Basic Information
        $sheet->setCellValue('A' . $row, $record['fullname'] ?? 'N/A');
        $sheet->setCellValue('B' . $row, $record['email'] ?? 'N/A');
        $sheet->setCellValue('C' . $row, $record['phone'] ?? 'N/A');
        $sheet->setCellValue('D' . $row, $record['dob'] ?? 'N/A');
        
        // Educational Details
        $sheet->setCellValue('E' . $row, strtoupper($record['course'] ?? 'N/A'));
        $sheet->setCellValue('F' . $row, $record['graduation_year'] ?? 'N/A');
        
        // Professional Status
        $sheet->setCellValue('G' . $row, $record['current_status'] ?? 'N/A');
        $sheet->setCellValue('H' . $row, $record['company_name'] ?? 'N/A');
        $sheet->setCellValue('I' . $row, $record['position'] ?? 'N/A');
        
        // Technical Skills
        if (!empty($record['skills'])) {
            $skillsArray = explode(';;', $record['skills']);
            
            // Collect all languages/technologies
            $languages = [];
            $tools = [];
            $technologies = [];
            $proficiencyLevels = [];
            
            foreach ($skillsArray as $skillSet) {
                $skillData = explode('|', $skillSet);
                if (!empty($skillData[0])) $languages[] = $skillData[0];
                if (!empty($skillData[1])) $tools[] = $skillData[1];
                if (!empty($skillData[2])) $technologies[] = $skillData[2];
                if (!empty($skillData[3])) $proficiencyLevels[] = $skillData[3];
            }
            
            $sheet->setCellValue('J' . $row, implode(', ', $languages) ?: 'N/A'); // All Languages
            $sheet->setCellValue('K' . $row, implode(', ', $tools) ?: 'N/A'); // All Tools
            $sheet->setCellValue('L' . $row, implode(', ', $technologies) ?: 'N/A'); // All Technologies
            $sheet->setCellValue('M' . $row, implode(', ', $proficiencyLevels) ?: 'N/A'); // All Proficiency Levels
        } else {
            $sheet->setCellValue('J' . $row, 'N/A');
            $sheet->setCellValue('K' . $row, 'N/A');
            $sheet->setCellValue('L' . $row, 'N/A');
            $sheet->setCellValue('M' . $row, 'N/A');
        }
        
        // Projects Count
        $sheet->setCellValue('N' . $row, ($record['project_count'] ?? '0') . ' Projects');
        
        // Career Goals
        $sheet->setCellValue('O' . $row, $record['career_goals'] ?? 'N/A');
        
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
    $sheet->getStyle("A1:O2")->applyFromArray($headerStyle);

    // Set column widths
    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Add alternating row colors
    for ($i = 3; $i <= $row; $i++) {
        if ($i % 2 == 0) {
            $sheet->getStyle("A{$i}:O{$i}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('F5F5F5'));
        }
    }

    // Enable text wrapping for cells that might contain long content
    $sheet->getStyle('L3:L' . $row)->getAlignment()->setWrapText(true); // Technologies
    $sheet->getStyle('O3:O' . $row)->getAlignment()->setWrapText(true); // Career Goals

    // Set the filename to be more company-focused
    header('Content-Disposition: attachment;filename="alumni_professional_profiles_' . date('Y-m-d') . '.xlsx"');

    // Create Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
} 