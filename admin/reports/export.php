<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';
require_admin();
require_once BASE_PATH . '/app/Services/ReportService.php';

$reportService = new ReportService();

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$departmentId = !empty($_GET['department_id']) ? (int) $_GET['department_id'] : null;

$reportData = $reportService->getUserStatistics($startDate, $endDate, $departmentId);

$filename = 'discount_report_' . date('Ymd_His') . '.csv';

// Set headers to force download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 compatibility
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Write Headers
fputcsv($output, ['ชื่อผู้ใช้ (Google)', 'อีเมล', 'สำนัก', 'จำนวนครั้งที่ใช้สิทธิ์', 'ร้อน (แก้ว)', 'เย็น (แก้ว)', 'รวมทั้งหมด (แก้ว)']);

// Write Data
foreach ($reportData as $row) {
    fputcsv($output, [
        $row['user_name'] ?? 'ผู้ใช้ทั่วไป (ไม่ระบุตัวตน)',
        $row['user_email'] ?? '-',
        $row['department_name'],
        $row['total_orders'],
        $row['total_hot'],
        $row['total_cold'],
        $row['total_cups']
    ]);
}

fclose($output);
exit;
