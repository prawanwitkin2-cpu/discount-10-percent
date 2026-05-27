<?php

require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'department_id' => (int) ($_GET['department_id'] ?? 0),
    'branch_id' => (int) ($_GET['branch_id'] ?? 0),
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'page' => (int) ($_GET['page'] ?? 1),
    'per_page' => (int) ($_GET['per_page'] ?? 15),
];

$departments = list_active_departments();
$branches = list_active_branches();
$pageData = paginated_records($filters);

function records_url(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    return '/admin/records.php?' . http_build_query($params);
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Records</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body class="cafe-admin">
  <main class="admin-shell">
    <?php require BASE_PATH . '/app/views/admin_nav.php'; ?>

    <section class="content">
      <div class="page-head">
        <p class="eyebrow">Admin</p>
        <h1>ข้อมูลที่บันทึกทั้งหมด</h1>
      </div>

      <form class="filter-bar" method="get">
        <label><span>ค้นหา</span><input type="search" name="q" value="<?= h($filters['q']) ?>"></label>
        <label>
          <span>สำนัก</span>
          <select name="department_id">
            <option value="0">ทั้งหมด</option>
            <?php foreach ($departments as $department): ?>
              <option value="<?= h($department['id']) ?>" <?= $filters['department_id'] === (int) $department['id'] ? 'selected' : '' ?>><?= h($department['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>
          <span>สาขาร้านกาแฟ</span>
          <select name="branch_id">
            <option value="0">ทั้งหมด</option>
            <?php foreach ($branches as $branch): ?>
              <option value="<?= h($branch['id']) ?>" <?= $filters['branch_id'] === (int) $branch['id'] ? 'selected' : '' ?>><?= h($branch['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label><span>แสดง</span>
          <select name="per_page">
            <?php foreach ([5, 15, 25, 50] as $size): ?>
              <option value="<?= $size ?>" <?= $pageData['per_page'] === $size ? 'selected' : '' ?>><?= $size ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button type="submit">กรอง</button>
      </form>

      <section class="panel">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>วันที่</th>
                <th>ชื่อเล่น</th>
                <th>สำนัก</th>
                <th>สาขาร้านกาแฟ</th>
                <th>ร้อน</th>
                <th>เย็น</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pageData['rows'] as $row): ?>
                <tr>
                  <td><?= h($row['created_at']) ?></td>
                  <td><?= h($row['nickname']) ?></td>
                  <td><?= h($row['department_name']) ?></td>
                  <td><?= h($row['branch_name']) ?></td>
                  <td><?= h($row['hot_cups']) ?></td>
                  <td><?= h($row['cold_cups']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$pageData['rows']): ?>
                <tr><td colspan="6">ไม่มีข้อมูล</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="pagination">
          <span>หน้า <?= h($pageData['page']) ?> / <?= h($pageData['total_pages']) ?> (<?= h($pageData['total']) ?> รายการ)</span>
          <div>
            <?php if ($pageData['page'] > 1): ?>
              <a class="button-link" href="<?= h(records_url(['page' => $pageData['page'] - 1])) ?>">ก่อนหน้า</a>
            <?php endif; ?>
            <?php if ($pageData['page'] < $pageData['total_pages']): ?>
              <a class="button-link" href="<?= h(records_url(['page' => $pageData['page'] + 1])) ?>">ถัดไป</a>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
