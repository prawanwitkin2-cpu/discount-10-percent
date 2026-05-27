<?php

return [
    'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'discount10',
    'username' => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
];
