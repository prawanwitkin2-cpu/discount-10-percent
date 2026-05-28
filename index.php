<?php

require __DIR__ . '/app/bootstrap.php';

$departmentNames = list_active_department_names();
$errors = [];
$success = false;

$old = [
    'nickname' => '',
    'department_name' => '',
    'branch_name' => '',
    'hot_cups' => 0,
    'cold_cups' => 0,
    'pdpa_accepted' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'เซสชันหมดอายุ (Session Expired) หรือโทเคนไม่ถูกต้อง กรุณากดปุ่ม "บันทึกข้อมูล" อีกครั้งเพื่อลองใหม่';
        // สร้าง Token ใหม่เพื่อไม่ให้มันค้าง
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $old = [
            'nickname' => input_string('nickname', 80),
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
            $old = [
                'nickname' => '',
                'department_name' => '',
                'branch_name' => '',
                'hot_cups' => 0,
                'cold_cups' => 0,
                'pdpa_accepted' => false, // keep it false for new entry
            ];
        }
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
  <style>
    .fade-out {
      animation: fadeOut 300ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .fade-in {
      animation: fadeIn 400ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeOut {
      from { opacity: 1; transform: translateY(0); }
      to { opacity: 0; transform: translateY(-10px); }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
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
        <div class="notice error">
          <?= isset($errors['csrf']) ? h($errors['csrf']) : 'กรุณาตรวจสอบข้อมูลให้ครบถ้วน' ?>
        </div>
      <?php endif; ?>

          <form method="post" class="stepper-form" data-stepper-form data-start-step="<?= $errors ? '1' : '0' ?>">
            <?= csrfField() ?>


            <section class="step active" id="step-0" data-step="0">
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
                <span>ชื่อเล่น</span>
                <input type="text" name="nickname" maxlength="80" value="<?= h($old['nickname']) ?>" required>
              </label>
              <?php if (isset($errors['nickname'])): ?><p class="field-error"><?= h($errors['nickname']) ?></p><?php endif; ?>

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
                  <span><i class="fa-solid fa-fire" style="color:#f97316" aria-hidden="true"></i> ร้อน (Hot)</span>
                  <input type="tel" name="hot_cups" inputmode="numeric" pattern="[0-9]*"
                    placeholder="0"
                    value="<?= ($old['hot_cups'] > 0) ? h($old['hot_cups']) : '' ?>"
                    onfocus="this.select()">
                </label>
                <label>
                  <span><i class="fa-solid fa-snowflake" style="color:#60a5fa" aria-hidden="true"></i> เย็น (Ice)</span>
                  <input type="tel" name="cold_cups" inputmode="numeric" pattern="[0-9]*"
                    placeholder="0"
                    value="<?= ($old['cold_cups'] > 0) ? h($old['cold_cups']) : '' ?>"
                    onfocus="this.select()">
                </label>
              </div>
              <?php if (isset($errors['cups'])): ?><p class="field-error"><?= h($errors['cups']) ?></p><?php endif; ?>
            </section>

            <div class="actions" hidden>
              <!-- ย้อนกลับไว้ซ้าย -->
              <button type="button" class="secondary" data-prev hidden aria-label="ย้อนกลับ"><i class="fa-solid fa-arrow-left"></i></button>
              <!-- บันทึกไว้ขวา -->
              <button type="submit" data-submit hidden aria-label="บันทึกข้อมูล"><i class="fa-solid fa-floppy-disk"></i></button>
            </div>
          </form>
      <?php if ($success): ?>
        <div id="save-success-flag" data-save-success="1" hidden></div>
      <?php endif; ?>
    </section>
  </main>
  <script src="/public/assets/js/floating-bg.js"></script>
  <script src="/public/assets/js/stepper.js"></script>
</body>
</html>
