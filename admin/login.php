<?php

require dirname(__DIR__) . '/app/bootstrap.php';

if (current_admin()) {
    redirect('/admin/dashboard.php');
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/public/assets/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f9f9f9; }
    .login-box { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
    .login-box h1 { margin-top: 0; font-size: 1.5rem; color: #333; }
    .btn-google { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background-color: #4285F4; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; width: 100%; margin-top: 20px; font-size: 1.1rem; }
    .btn-google:hover { background-color: #357ae8; }
  </style>
</head>
<body>
  <div class="login-box">
    <h1>Admin Console</h1>
    <p style="color: #666; margin-bottom: 20px;">เข้าสู่ระบบด้วยบัญชีแอดมินที่ได้รับอนุญาต</p>
    
    <?php if ($error): ?>
      <div class="notice error" style="margin-bottom: 20px; text-align: left;"><?= h($error) ?></div>
    <?php endif; ?>

    <a href="/auth/google-login.php" class="btn-google">
        <i class="fa-brands fa-google"></i> Login with Google
    </a>
  </div>
</body>
</html>
