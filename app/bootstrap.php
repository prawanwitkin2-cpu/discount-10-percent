<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Simple .env parser
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, '"\''); // Remove quotes
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

$appConfig = require BASE_PATH . '/config/app.php';
date_default_timezone_set($appConfig['timezone']);

ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');

// ใช้ค่าเริ่มต้นของ PHP Session เพื่อป้องกันปัญหาเบราว์เซอร์บล็อก Cookie
// session_name($appConfig['session_name']);
// session_set_cookie_params([
//     'lifetime' => 0,
//     'path' => '/',
//     'httponly' => true,
//     'samesite' => 'Lax',
// ]);
session_start();

require BASE_PATH . '/app/helpers.php';
require BASE_PATH . '/app/csrf.php';
require BASE_PATH . '/app/logger.php';
require BASE_PATH . '/app/db.php';
require BASE_PATH . '/app/auth.php';
require BASE_PATH . '/app/records.php';

