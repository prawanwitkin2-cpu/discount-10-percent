<?php

require dirname(__DIR__) . '/app/bootstrap.php';

if (current_admin()) {
    redirect('/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = input_string('username', 80);
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_admin_login($username, $password)) {
        redirect('/admin/dashboard.php');
    }

    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body class="cafe-admin">
  <main class="auth-shell">
    <form method="post" class="panel auth-panel">
      <?= csrf_field() ?>
      <p class="eyebrow">Cafe Admin</p>
      <h1>เข้าสู่ระบบ</h1>
      <?php if ($error): ?><div class="notice error"><?= h($error) ?></div><?php endif; ?>
      <label>
        <span>Username</span>
        <input type="text" name="username" required autofocus>
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required>
      </label>
      <button type="submit">Login</button>
    </form>
  </main>
</body>
</html>
