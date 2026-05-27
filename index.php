<?php

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/csrf.php';

$departmentNames = list_active_department_names();
$errors = [];
$success = false;

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$userDepartmentId = $_SESSION['department_id'] ?? null;
$defaultDepartmentName = '';

if ($userDepartmentId) {
    $stmt = db()->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->execute([$userDepartmentId]);
    $defaultDepartmentName = (string) $stmt->fetchColumn();
}

$old = [
    'department_name' => $defaultDepartmentName,
    'branch_name' => '',
    'hot_cups' => 0,
    'cold_cups' => 0,
    'pdpa_accepted' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF Token');
    }

    $old = [
        'user_id' => $_SESSION['user_id'],
        'user_name' => $userName,
        'department_name' => input_string('department_name', 120),
        'branch_name' => input_string('branch_name', 120),
        'hot_cups' => input_int('hot_cups'),
        'cold_cups' => input_int('cold_cups'),
        'pdpa_accepted' => isset($_POST['pdpa_accepted']),
        'created_ip' => substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ];

    $errors = validate_discount_payload($old);

    if (!$errors) {
        create_discount_record($old);
        $success = true;
        // Reset form except department
        $old['branch_name'] = '';
        $old['hot_cups'] = 0;
        $old['cold_cups'] = 0;
    }
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Discount 10 Percent</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body class="cafe-public">
  <div class="floating-cafe-bg" aria-hidden="true"></div>
  <main class="public-shell">
    <section class="panel public-card">
      <div class="page-head">
        <div class="hero-badge" aria-hidden="true">☕</div>
        <p class="eyebrow">Cafe Staff Benefit</p>
        <h1>บันทึกส่วนลด 10%</h1>
      </div>

      <?php if ($errors): ?>
        <div class="notice error">กรุณาตรวจสอบข้อมูลให้ครบถ้วน</div>
      <?php endif; ?>

      <?php if (!$isLoggedIn): ?>
        <div class="login-prompt" style="text-align: center; margin-top: 2rem;">
            <p style="margin-bottom: 1rem; color: #555;">กรุณาเข้าสู่ระบบด้วยบัญชี Google ของคุณเพื่อบันทึกสิทธิ์</p>
            <a href="/auth/google-login.php" class="button" style="display: inline-block; background-color: #4285F4; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fa-brands fa-google" style="margin-right: 8px;"></i> Login with Google
            </a>
        </div>
      <?php else: ?>
          <div class="user-greeting" style="text-align: center; margin-bottom: 1rem; color: #6d4c41; font-weight: 500;">
              สวัสดีคุณ <?= h($userName) ?> 
          </div>

          <form method="post" class="stepper-form" data-stepper-form>
            <?= csrfField() ?>

            <ol class="steps" aria-label="ขั้นตอนการบันทึก">
              <li class="active" data-step-indicator="0">
                <span class="step-number">1</span>
                <span class="step-label">PDPA</span>
              </li>
              <li data-step-indicator="1">
                <span class="step-number">2</span>
                <span class="step-label">ฟอร์ม</span>
              </li>
            </ol>

            <section class="step active" data-step="0">
              <div class="pdpa-notice muted">
                <p class="pdpa-title">ประกาศแจ้งการเก็บ ใช้ หรือเปิดเผยข้อมูลส่วนบุคคล*</p>
                <p>สำหรับผู้ใช้บริการระบบรับส่วนลด ร้านกาแฟ Koonie CAFE</p>
                <p>
                  Koonie Cafe เก็บและใช้ข้อมูลของท่านเท่าที่จำเป็นสำหรับการตรวจสอบสิทธิ
                  บันทึกการใช้บริการ และจัดทำสถิติในภาพรวมเพื่อพัฒนาคุณภาพการให้บริการ
                </p>
                <p>ข้อมูลของท่านจะถูกเก็บอย่างปลอดภัย และไม่เปิดเผยต่อบุคคลภายนอกโดยไม่ได้รับอนุญาต</p>
                <p class="pdpa-link-line">
                  <a href="https://www.hrdi.or.th/hrdi-admin/public/images/FILE-20260522-1558DYQ155ZR2KXL.pdf" target="_blank" rel="noopener noreferrer">🔗 อ่านประกาศความเป็นส่วนตัวฉบับเต็ม</a>
                </p>
              </div>
              <label class="check-row">
                <input type="checkbox" name="pdpa_accepted" value="1" <?= !empty($old['pdpa_accepted']) ? 'checked' : '' ?>>
                <span>ข้าพเจ้าอ่าน และยินยอมให้ใช้ข้อมูลส่วนบุคคลตามวัตถุประสงค์ข้างต้น</span>
              </label>
              <?php if (isset($errors['pdpa'])): ?><p class="field-error"><?= h($errors['pdpa']) ?></p><?php endif; ?>
            </section>

            <section class="step" data-step="1">
              <h2>ฟอร์ม</h2>
              <label>
                <span>สำนัก</span>
                <input type="text" name="department_name" list="department-options" maxlength="120" value="<?= h($old['department_name']) ?>" autocomplete="off" required>
                <datalist id="department-options">
                  <?php foreach ($departmentNames as $departmentName): ?>
                    <option value="<?= h($departmentName) ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </label>
              <?php if (isset($errors['department_name'])): ?><p class="field-error"><?= h($errors['department_name']) ?></p><?php endif; ?>
              <label>
                <span>สาขาร้านกาแฟ</span>
                <select name="branch_name" required>
                  <option value="">เลือกสาขาร้านกาแฟ</option>
                  <?php foreach (allowed_branch_names() as $branchName): ?>
                    <option value="<?= h($branchName) ?>" <?= $old['branch_name'] === $branchName ? 'selected' : '' ?>><?= h($branchName) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <?php if (isset($errors['branch_name'])): ?><p class="field-error"><?= h($errors['branch_name']) ?></p><?php endif; ?>
              <div class="count-grid">
                <label>
                  <span><i class="fa-solid fa-fire text-orange-500" aria-hidden="true"></i> ร้อน (Hot)</span>
                  <input type="number" name="hot_cups" min="0" step="1" inputmode="numeric" pattern="[0-9]*" value="<?= h($old['hot_cups']) ?>">
                </label>
                <label>
                  <span><i class="fa-solid fa-snowflake text-blue-500" aria-hidden="true"></i> เย็น (Ice)</span>
                  <input type="number" name="cold_cups" min="0" step="1" inputmode="numeric" pattern="[0-9]*" value="<?= h($old['cold_cups']) ?>">
                </label>
              </div>
              <?php if (isset($errors['cups'])): ?><p class="field-error"><?= h($errors['cups']) ?></p><?php endif; ?>
            </section>

            <div class="actions">
              <button type="button" class="secondary" data-prev hidden><- ย้อนกลับ</button>
              <button type="button" data-next>ถัดไป -></button>
              <button type="submit" data-submit hidden>บันทึกข้อมูล</button>
            </div>
          </form>
      <?php endif; ?>
      <?php if ($success): ?>
        <div id="save-success-flag" data-save-success="1" hidden></div>
      <?php endif; ?>
    </section>
  </main>
  <script src="/public/assets/js/floating-bg.js"></script>
  <script src="/public/assets/js/stepper.js"></script>
</body>
</html>
