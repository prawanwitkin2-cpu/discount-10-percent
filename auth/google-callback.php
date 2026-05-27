<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once BASE_PATH . '/app/Controllers/GoogleAuthController.php';

if (isset($_GET['code'])) {
    $controller = new GoogleAuthController();
    $controller->handleCallback($_GET['code']);
} else {
    header('Location: /');
    exit;
}
