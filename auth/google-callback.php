<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (empty($_GET['code'])) {
    redirect('/');
}

$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? '');
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? '');

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    $_SESSION['login_error'] = 'เกิดข้อผิดพลาดในการดึงข้อมูลจาก Google';
    redirect('/admin/login.php');
}

$client->setAccessToken($token['access_token']);
$google_oauth = new Google\Service\Oauth2($client);
$email = $google_oauth->userinfo->get()->email;

if (attempt_admin_login_by_email($email)) {
    redirect('/admin/dashboard.php');
}

$_SESSION['login_error'] = "อีเมลนี้ ($email) ไม่มีสิทธิ์เข้าใช้งานระบบแอดมิน";
redirect('/admin/login.php');
