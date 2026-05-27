<?php

declare(strict_types=1);

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): void
{
    if (!current_admin()) {
        redirect('/admin/login.php');
    }
}

function attempt_admin_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT id, username, password_hash, display_name FROM admins WHERE username = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'id' => (int) $admin['id'],
        'username' => $admin['username'],
        'display_name' => $admin['display_name'],
    ];

    $update = db()->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?');
    $update->execute([$admin['id']]);

    return true;
}

function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
}

