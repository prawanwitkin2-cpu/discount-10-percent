<?php
declare(strict_types=1);

class ReportService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getUserStatistics(?string $startDate = null, ?string $endDate = null, ?int $departmentId = null): array
    {
        $conditions = [];
        $params = [];

        if ($startDate) {
            $conditions[] = "r.created_at >= ?";
            $params[] = $startDate . ' 00:00:00';
        }
        if ($endDate) {
            $conditions[] = "r.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
        }
        if ($departmentId) {
            $conditions[] = "r.department_id = ?";
            $params[] = $departmentId;
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT 
                u.name AS user_name,
                u.email AS user_email,
                d.name AS department_name,
                COUNT(r.id) AS total_orders,
                SUM(r.hot_cups) AS total_hot,
                SUM(r.cold_cups) AS total_cold,
                SUM(r.hot_cups + r.cold_cups) AS total_cups
            FROM discount_records r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN departments d ON r.department_id = d.id
            $whereClause
            GROUP BY u.id, d.id
            ORDER BY total_cups DESC, u.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
