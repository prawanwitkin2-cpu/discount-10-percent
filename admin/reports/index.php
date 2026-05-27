<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';
require_admin();

require_once BASE_PATH . '/app/Services/ReportService.php';

$reportService = new ReportService();

$startDate = $_GET['start_date'] ?? date('Y-m-01'); // Default to 1st of current month
$endDate = $_GET['end_date'] ?? date('Y-m-t'); // Default to last day of current month
$departmentId = !empty($_GET['department_id']) ? (int) $_GET['department_id'] : null;

$departments = list_all_departments_admin();
$reportData = $reportService->getUserStatistics($startDate, $endDate, $departmentId);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Reports - Admin</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body class="cafe-admin">
  <main class="admin-shell">
    <?php require BASE_PATH . '/app/views/admin_nav.php'; ?>
    <section class="content">
      <div class="page-head">
        <p class="eyebrow">Admin</p>
        <h1>รายงานสถิติตามรายชื่อผู้ใช้</h1>
      </div>

      <section class="panel">
        <h2>ตัวกรองข้อมูล (Filters)</h2>
        <form method="get" class="filter-bar" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
          <label>
            <span>ตั้งแต่วันที่</span>
            <input type="date" name="start_date" value="<?= h($startDate) ?>">
          </label>
          <label>
            <span>ถึงวันที่</span>
            <input type="date" name="end_date" value="<?= h($endDate) ?>">
          </label>
          <label>
            <span>สำนัก</span>
            <select name="department_id">
              <option value="">-- ทั้งหมด --</option>
              <?php foreach ($departments as $dept): ?>
                <option value="<?= h($dept['id']) ?>" <?= $departmentId === $dept['id'] ? 'selected' : '' ?>>
                  <?= h($dept['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
          <button type="submit">ค้นหา</button>
          
          <a href="/admin/reports/export.php?start_date=<?= h($startDate) ?>&end_date=<?= h($endDate) ?>&department_id=<?= h($departmentId) ?>" 
             class="button secondary" style="margin-left: auto; background-color: #2e7d32; color: white;">
            <i class="fa-solid fa-file-csv"></i> Export CSV
          </a>
        </form>
      </section>

      <section class="panel">
        <h2>ผลลัพธ์รายงาน</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ชื่อผู้ใช้ (Google)</th>
                <th>อีเมล</th>
                <th>สำนัก</th>
                <th>จำนวนครั้งที่ใช้สิทธิ์</th>
                <th>ร้อน (แก้ว)</th>
                <th>เย็น (แก้ว)</th>
                <th>รวมทั้งหมด (แก้ว)</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $sumOrders = 0; $sumHot = 0; $sumCold = 0; $sumTotal = 0;
              foreach ($reportData as $row): 
                  $sumOrders += $row['total_orders'];
                  $sumHot += $row['total_hot'];
                  $sumCold += $row['total_cold'];
                  $sumTotal += $row['total_cups'];
              ?>
                <tr>
                  <td><?= h($row['user_name'] ?? 'ผู้ใช้ทั่วไป (ไม่ระบุตัวตน)') ?></td>
                  <td><?= h($row['user_email'] ?? '-') ?></td>
                  <td><?= h($row['department_name']) ?></td>
                  <td><?= number_format($row['total_orders']) ?></td>
                  <td><?= number_format($row['total_hot']) ?></td>
                  <td><?= number_format($row['total_cold']) ?></td>
                  <td><strong><?= number_format($row['total_cups']) ?></strong></td>
                </tr>
              <?php endforeach; ?>
              
              <?php if (!$reportData): ?>
                <tr><td colspan="7" style="text-align: center;">ไม่มีข้อมูลในช่วงเวลานี้</td></tr>
              <?php else: ?>
                <tr style="background-color: #f1f0eb; font-weight: bold;">
                  <td colspan="3" style="text-align: right;">รวมทั้งหมด:</td>
                  <td><?= number_format($sumOrders) ?></td>
                  <td><?= number_format($sumHot) ?></td>
                  <td><?= number_format($sumCold) ?></td>
                  <td><?= number_format($sumTotal) ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
