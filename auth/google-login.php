<?php
require_once __DIR__ . '/../app/bootstrap.php';

$client = new Google\Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? '');
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI'] ?? '');
$client->addScope('email');
$client->addScope('profile');

header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
exit;
