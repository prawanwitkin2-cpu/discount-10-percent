<?php
declare(strict_types=1);

function writeAuditLog(int $adminId, string $action, string $targetType, ?int $targetId = null, ?array $payload = null): bool {
    global $pdo;
    
    $payloadJson = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $stmt = $pdo->prepare("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, payload_json, created_ip) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$adminId, $action, $targetType, $targetId, $payloadJson, $ip]);
}
