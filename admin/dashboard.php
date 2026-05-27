<?php

require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$totals = dashboard_totals($startDate, $endDate);
$departments = dashboard_departments($startDate, $endDate);
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body class="cafe-admin">
  <main class="admin-shell">
    <?php require BASE_PATH . '/app/views/admin_nav.php'; ?>

    <section class="content">
      <div class="page-head row">
        <div>
          <p class="eyebrow">Admin</p>
          <h1>ภาพรวมการใช้ส่วนลด</h1>
        </div>
      </div>

      <form class="filter-bar" method="get">
        <label><span>เริ่ม</span><input type="date" name="start_date" value="<?= h($startDate) ?>"></label>
        <label><span>ถึง</span><input type="date" name="end_date" value="<?= h($endDate) ?>"></label>
        <button type="submit">กรอง</button>
      </form>

      <div class="metric-grid">
        <article class="metric"><span>ร้อน</span><strong><?= h($totals['hot_cups']) ?></strong></article>
        <article class="metric"><span>เย็น</span><strong><?= h($totals['cold_cups']) ?></strong></article>
        <article class="metric"><span>รายการ</span><strong><?= h($totals['record_count']) ?></strong></article>
      </div>

      <section class="panel">
        <h2>การใช้ส่วนลดแยกตามสำนัก</h2>
        <div class="table-wrap">
          <table>
            <thead><tr><th>สำนัก</th><th>จำนวนรวม</th></tr></thead>
            <tbody>
              <?php foreach ($departments as $row): ?>
                <tr><td><?= h($row['name']) ?></td><td><?= h($row['total_usage']) ?></td></tr>
              <?php endforeach; ?>
              <?php if (!$departments): ?>
                <tr><td colspan="2">ไม่มีข้อมูล</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
