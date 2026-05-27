<?php

declare(strict_types=1);

$pdo = new PDO(
    'mysql:host=localhost;dbname=discount10;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

$updates = [
    ['table' => 'departments', 'id' => 1, 'name' => 'สำนักตัวอย่าง 1'],
    ['table' => 'departments', 'id' => 2, 'name' => 'สำนักตัวอย่าง 2'],
    ['table' => 'branches', 'id' => 1, 'name' => 'สาขาตัวอย่าง 1'],
    ['table' => 'branches', 'id' => 2, 'name' => 'สาขาตัวอย่าง 2'],
];

foreach ($updates as $row) {
    $stmt = $pdo->prepare("UPDATE {$row['table']} SET name = ? WHERE id = ?");
    $stmt->execute([$row['name'], $row['id']]);
}

echo "Thai seed text updated.\n";
