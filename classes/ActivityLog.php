<?php
class ActivityLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['user_id'])) { $where .= " AND al.user_id = ?"; $params[] = $filters['user_id']; }
        if (!empty($filters['action'])) { $where .= " AND al.action LIKE ?"; $params[] = "%{$filters['action']}%"; }
        if (!empty($filters['resource'])) { $where .= " AND al.resource = ?"; $params[] = $filters['resource']; }
        if (!empty($filters['date_from'])) { $where .= " AND al.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where .= " AND al.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM activity_logs al WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 50, $page);

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.name as user_name, u.email as user_email
             FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id
             WHERE {$where}
             ORDER BY al.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['logs' => $logs, 'pagination' => $pagination, 'total' => $total];
    }

    public function getStats() {
        return $this->db->fetch("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as this_week
            FROM activity_logs
        ");
    }

    public function getRecentActivity($limit = 20) {
        return $this->db->fetchAll(
            "SELECT al.*, u.name as user_name
             FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC LIMIT ?", [$limit]
        );
    }
}
