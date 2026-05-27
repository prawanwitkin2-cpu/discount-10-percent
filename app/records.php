<?php

declare(strict_types=1);

function list_active_departments(): array
{
    return db()->query('SELECT id, name FROM departments WHERE is_active = 1 ORDER BY name')->fetchAll();
}

function list_active_department_names(): array
{
    return db()->query('SELECT name FROM departments WHERE is_active = 1 ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
}

function list_active_branches(): array
{
    return db()->query('SELECT id, name FROM branches WHERE is_active = 1 ORDER BY name')->fetchAll();
}

function allowed_branch_names(): array
{
    return ['สาขาริมน้ำ', 'สาขาลานส้มสุก', 'Delivery'];
}

function validate_discount_payload(array $payload): array
{
    $errors = [];

    if (empty($payload['user_id'])) {
        $errors['auth'] = 'กรุณาเข้าสู่ระบบก่อนทำรายการ';
    }
    if ($payload['department_name'] === '') {
        $errors['department_name'] = 'กรุณากรอกสำนัก';
    }
    if ($payload['branch_name'] === '') {
        $errors['branch_name'] = 'กรุณาเลือกสาขาร้านกาแฟ';
    }
    if (!in_array($payload['branch_name'], allowed_branch_names(), true)) {
        $errors['branch_name'] = 'สาขาร้านกาแฟไม่ถูกต้อง';
    }
    if (($payload['hot_cups'] + $payload['cold_cups']) <= 0) {
        $errors['cups'] = 'กรุณากรอกจำนวนอย่างน้อย 1 รายการ';
    }
    if (empty($payload['pdpa_accepted'])) {
        $errors['pdpa'] = 'กรุณายอมรับ PDPA ก่อนบันทึก';
    }

    return $errors;
}

function create_discount_record(array $payload): void
{
    $departmentId = resolve_department_id_by_name($payload['department_name']);
    $branchId = resolve_allowed_branch_id_by_name($payload['branch_name']);
    $userId = (int) $payload['user_id'];

    // Update user's department_id for next time
    $updUser = db()->prepare("UPDATE users SET department_id = ? WHERE id = ?");
    $updUser->execute([$departmentId, $userId]);
    $_SESSION['department_id'] = $departmentId;

    $stmt = db()->prepare(
        'INSERT INTO discount_records
        (user_id, nickname, department_id, branch_id, hot_cups, cold_cups, delivery_count, pdpa_accepted, created_ip, user_agent, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
    );

    $stmt->execute([
        $userId,
        $payload['user_name'] ?? '',
        $departmentId,
        $branchId,
        $payload['hot_cups'],
        $payload['cold_cups'],
        0,
        $payload['pdpa_accepted'] ? 1 : 0,
        $payload['created_ip'],
        $payload['user_agent'],
    ]);
}

function dashboard_totals(?string $startDate, ?string $endDate): array
{
    [$where, $params] = date_filter_sql($startDate, $endDate, 'r.created_at');
    $stmt = db()->prepare(
        "SELECT
            COALESCE(SUM(r.hot_cups), 0) AS hot_cups,
            COALESCE(SUM(r.cold_cups), 0) AS cold_cups,
            COUNT(*) AS record_count
        FROM discount_records r
        $where"
    );
    $stmt->execute($params);
    return $stmt->fetch() ?: ['hot_cups' => 0, 'cold_cups' => 0, 'record_count' => 0];
}

function dashboard_departments(?string $startDate, ?string $endDate): array
{
    [$where, $params] = date_filter_sql($startDate, $endDate, 'r.created_at');
    $stmt = db()->prepare(
        "SELECT d.name, COALESCE(SUM(r.hot_cups + r.cold_cups), 0) AS total_usage
        FROM discount_records r
        INNER JOIN departments d ON d.id = r.department_id
        $where
        GROUP BY d.id, d.name
        ORDER BY total_usage DESC, d.name ASC"
    );
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function paginated_records(array $filters): array
{
    $allowedPerPage = [5, 15, 25, 50];
    $perPage = in_array((int) ($filters['per_page'] ?? 15), $allowedPerPage, true) ? (int) $filters['per_page'] : 15;
    $page = max(1, (int) ($filters['page'] ?? 1));

    [$where, $params] = record_filter_sql($filters);
    $countStmt = db()->prepare("SELECT COUNT(*) FROM discount_records r $where");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $stmt = db()->prepare(
        "SELECT r.*, d.name AS department_name, b.name AS branch_name
        FROM discount_records r
        INNER JOIN departments d ON d.id = r.department_id
        INNER JOIN branches b ON b.id = r.branch_id
        $where
        ORDER BY r.created_at DESC, r.id DESC
        LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);

    return [
        'rows' => $stmt->fetchAll(),
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
    ];
}

function date_filter_sql(?string $startDate, ?string $endDate, string $column): array
{
    $clauses = [];
    $params = [];

    if ($startDate) {
        $clauses[] = "$column >= ?";
        $params[] = $startDate . ' 00:00:00';
    }
    if ($endDate) {
        $clauses[] = "$column <= ?";
        $params[] = $endDate . ' 23:59:59';
    }

    return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
}

function resolve_department_id_by_name(string $name): int
{
    $clean = trim($name);
    $select = db()->prepare('SELECT id FROM departments WHERE name = ? LIMIT 1');
    $select->execute([$clean]);
    $id = $select->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $insert = db()->prepare('INSERT INTO departments (name, is_active, created_at, updated_at) VALUES (?, 1, NOW(), NOW())');
    $insert->execute([$clean]);
    return (int) db()->lastInsertId();
}

function resolve_allowed_branch_id_by_name(string $name): int
{
    if (!in_array($name, allowed_branch_names(), true)) {
        throw new InvalidArgumentException('Invalid branch name');
    }

    $select = db()->prepare('SELECT id FROM branches WHERE name = ? LIMIT 1');
    $select->execute([$name]);
    $id = $select->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $insert = db()->prepare('INSERT INTO branches (name, is_active, created_at, updated_at) VALUES (?, 1, NOW(), NOW())');
    $insert->execute([$name]);
    return (int) db()->lastInsertId();
}

function list_all_departments_admin(): array
{
    return db()->query('SELECT id, name, is_active FROM departments ORDER BY is_active DESC, name ASC')->fetchAll();
}

function create_department_admin(string $name): array
{
    $clean = trim($name);
    if ($clean === '') {
        return ['ok' => false, 'message' => 'กรุณากรอกชื่อสำนัก'];
    }
    $stmt = db()->prepare('SELECT id FROM departments WHERE name = ? LIMIT 1');
    $stmt->execute([$clean]);
    if ($stmt->fetchColumn()) {
        return ['ok' => false, 'message' => 'มีสำนักนี้อยู่แล้ว'];
    }
    $ins = db()->prepare('INSERT INTO departments (name, is_active, created_at, updated_at) VALUES (?, 1, NOW(), NOW())');
    $ins->execute([$clean]);
    $newId = (int) db()->lastInsertId();
    
    if (function_exists('writeAuditLog')) {
        writeAuditLog($_SESSION['admin_id'] ?? 0, 'CREATE_DEPARTMENT', 'departments', $newId, ['name' => $clean]);
    }
    
    return ['ok' => true, 'message' => 'เพิ่มสำนักเรียบร้อย'];
}

function update_department_admin(int $id, string $name): array
{
    $clean = trim($name);
    if ($clean === '') {
        return ['ok' => false, 'message' => 'กรุณากรอกชื่อสำนัก'];
    }
    $dup = db()->prepare('SELECT id FROM departments WHERE name = ? AND id <> ? LIMIT 1');
    $dup->execute([$clean, $id]);
    if ($dup->fetchColumn()) {
        return ['ok' => false, 'message' => 'ชื่อสำนักซ้ำกับรายการอื่น'];
    }
    $upd = db()->prepare('UPDATE departments SET name = ?, updated_at = NOW() WHERE id = ?');
    $upd->execute([$clean, $id]);

    if (function_exists('writeAuditLog')) {
        writeAuditLog($_SESSION['admin_id'] ?? 0, 'UPDATE_DEPARTMENT', 'departments', $id, ['name' => $clean]);
    }

    return ['ok' => true, 'message' => 'แก้ไขชื่อสำนักเรียบร้อย'];
}

function delete_department_admin(int $id): array
{
    $use = db()->prepare('SELECT COUNT(*) FROM discount_records WHERE department_id = ?');
    $use->execute([$id]);
    $usedCount = (int) $use->fetchColumn();

    if ($usedCount > 0) {
        $deactivate = db()->prepare('UPDATE departments SET is_active = 0, updated_at = NOW() WHERE id = ?');
        $deactivate->execute([$id]);
        if (function_exists('writeAuditLog')) {
            writeAuditLog($_SESSION['admin_id'] ?? 0, 'DEACTIVATE_DEPARTMENT', 'departments', $id);
        }
        return ['ok' => true, 'message' => 'ปิดการใช้งานสำนักแล้ว (มีข้อมูลเดิมอ้างอิงอยู่)'];
    }

    $del = db()->prepare('DELETE FROM departments WHERE id = ?');
    $del->execute([$id]);
    if (function_exists('writeAuditLog')) {
        writeAuditLog($_SESSION['admin_id'] ?? 0, 'DELETE_DEPARTMENT', 'departments', $id);
    }
    return ['ok' => true, 'message' => 'ลบสำนักเรียบร้อย'];
}

function record_filter_sql(array $filters): array
{
    $clauses = [];
    $params = [];

    if (!empty($filters['q'])) {
        $clauses[] = 'r.nickname LIKE ?';
        $params[] = '%' . $filters['q'] . '%';
    }
    if (!empty($filters['department_id'])) {
        $clauses[] = 'r.department_id = ?';
        $params[] = (int) $filters['department_id'];
    }
    if (!empty($filters['branch_id'])) {
        $clauses[] = 'r.branch_id = ?';
        $params[] = (int) $filters['branch_id'];
    }
    if (!empty($filters['start_date'])) {
        $clauses[] = 'r.created_at >= ?';
        $params[] = $filters['start_date'] . ' 00:00:00';
    }
    if (!empty($filters['end_date'])) {
        $clauses[] = 'r.created_at <= ?';
        $params[] = $filters['end_date'] . ' 23:59:59';
    }

    return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
}
