<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once BASE_PATH . '/app/Controllers/GoogleAuthController.php';

$controller = new GoogleAuthController();
$controller->redirect();
