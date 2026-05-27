<?php

require dirname(__DIR__) . '/app/bootstrap.php';
require_admin();

$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'create') {
        $result = create_department_admin(input_string('name', 120));
        $flash = $result['message'];
    } elseif ($action === 'update') {
        $result = update_department_admin((int) ($_POST['id'] ?? 0), input_string('name', 120));
        $flash = $result['message'];
    } elseif ($action === 'delete') {
        $result = delete_department_admin((int) ($_POST['id'] ?? 0));
        $flash = $result['message'];
    }
}

$departments = list_all_departments_admin();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Departments</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body class="cafe-admin">
  <main class="admin-shell">
    <?php require BASE_PATH . '/app/views/admin_nav.php'; ?>
    <section class="content">
      <div class="page-head">
        <p class="eyebrow">Admin</p>
        <h1>จัดการสำนัก (CRUD)</h1>
      </div>

      <?php if ($flash): ?><div class="notice success"><?= h($flash) ?></div><?php endif; ?>

      <section class="panel">
        <h2>เพิ่มสำนัก</h2>
        <form method="post" class="filter-bar">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="create">
          <label><span>ชื่อสำนัก</span><input type="text" name="name" maxlength="120" required></label>
          <button type="submit">เพิ่ม</button>
        </form>
      </section>

      <section class="panel">
        <h2>รายการสำนัก</h2>
        <div class="table-wrap">
          <table>
            <thead><tr><th>ID</th><th>ชื่อสำนัก</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
            <tbody>
              <?php foreach ($departments as $department): ?>
                <tr>
                  <td><?= h($department['id']) ?></td>
                  <td>
                    <form method="post" class="inline-form">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="id" value="<?= h($department['id']) ?>">
                      <input type="text" name="name" value="<?= h($department['name']) ?>" maxlength="120" required>
                      <button type="submit">บันทึก</button>
                    </form>
                  </td>
                  <td><?= (int) $department['is_active'] === 1 ? 'ใช้งาน' : 'ปิดใช้งาน' ?></td>
                  <td>
                    <form method="post" onsubmit="return confirm('ยืนยันลบ/ปิดใช้งานสำนักนี้?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= h($department['id']) ?>">
                      <button type="submit" class="secondary">ลบ/ปิดใช้งาน</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$departments): ?>
                <tr><td colspan="4">ไม่มีข้อมูล</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
