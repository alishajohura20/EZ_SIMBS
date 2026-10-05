<?php
class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($title, $message, $type = 'info', $userId = null, $link = null) {
        return $this->db->insert('notifications', [
            'user_id' => $userId,
            'title' => sanitize($title),
            'message' => sanitize($message),
            'type' => $type,
            'link' => $link,
        ]);
    }

    public function createForAll($title, $message, $type = 'info', $link = null) {
        $users = $this->db->fetchAll("SELECT id FROM users WHERE status = 'active'");
        foreach ($users as $user) {
            $this->create($title, $message, $type, $user['id'], $link);
        }
    }

    public function createForRole($title, $message, $roles, $type = 'info', $link = null) {
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $users = $this->db->fetchAll(
            "SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name IN ({$placeholders}) AND u.status = 'active'",
            $roles
        );
        foreach ($users as $user) {
            $this->create($title, $message, $type, $user['id'], $link);
        }
    }

    public function getForUser($userId, $limit = 50, $unreadOnly = false) {
        $where = "user_id = ?";
        $params = [$userId];
        if ($unreadOnly) { $where .= " AND is_read = 0"; }

        return $this->db->fetchAll(
            "SELECT * FROM notifications WHERE {$where} ORDER BY created_at DESC LIMIT ?",
            array_merge($params, [$limit])
        );
    }

    public function getUnreadCount($userId) {
        return $this->db->fetch(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        )['count'];
    }

    public function markRead($id, $userId) {
        $this->db->update('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$id, $userId]);
    }

    public function markAllRead($userId) {
        $this->db->update('notifications', ['is_read' => 1], 'user_id = ? AND is_read = 0', [$userId]);
    }

    public function delete($id, $userId) {
        $this->db->delete('notifications', 'id = ? AND user_id = ?', [$id, $userId]);
    }

    public function deleteAll($userId) {
        $this->db->delete('notifications', 'user_id = ?', [$userId]);
    }

    public function checkLowStock() {
        $lowStockItems = $this->db->fetchAll("
            SELECT p.name, p.sku, i.qty, i.reorder_level, b.name as branch_name
            FROM inventory i JOIN products p ON i.product_id = p.id JOIN branches b ON i.branch_id = b.id
            WHERE i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status = 'active'
            AND i.product_id NOT IN (
                SELECT product_id FROM notifications WHERE title = 'Low Stock Alert' AND is_read = 0
            )
        ");

        foreach ($lowStockItems as $item) {
            $this->createForRole(
                'Low Stock Alert',
                "{$item['name']} ({$item['sku']}) is low on stock: {$item['qty']} remaining (reorder at {$item['reorder_level']}) in {$item['branch_name']}",
                ['admin', 'manager', 'branch_manager'],
                'warning',
                APP_URL . '/pages/inventory/'
            );
        }

        return count($lowStockItems);
    }

    public function checkOutOfStock() {
        $outItems = $this->db->fetchAll("
            SELECT p.name, p.sku, b.name as branch_name, i.reorder_level
            FROM inventory i JOIN products p ON i.product_id = p.id JOIN branches b ON i.branch_id = b.id
            WHERE i.qty = 0 AND p.status = 'active'
            AND i.product_id NOT IN (
                SELECT product_id FROM notifications WHERE title = 'Out of Stock' AND is_read = 0
            )
        ");

        foreach ($outItems as $item) {
            $this->createForRole(
                'Out of Stock',
                "{$item['name']} ({$item['sku']}) is OUT OF STOCK in {$item['branch_name']}",
                ['admin', 'manager', 'branch_manager'],
                'danger',
                APP_URL . '/pages/inventory/'
            );
        }

        return count($outItems);
    }

    public function checkDuePayments() {
        $duePayments = $this->db->fetchAll("
            SELECT p.id, p.po_number, s.company_name, p.due, p.created_at
            FROM purchases p JOIN suppliers s ON p.supplier_id = s.id
            WHERE p.due > 0 AND p.status != 'cancelled'
        ");

        $count = 0;
        foreach ($duePayments as $payment) {
            if ($payment['due'] > 0) {
                $this->createForRole(
                    'Payment Due',
                    "PO #{$payment['po_number']} from {$payment['company_name']} has outstanding balance of $" . number_format($payment['due'], 2),
                    ['admin', 'manager'],
                    'warning',
                    APP_URL . '/pages/purchases/view.php?id=' . $payment['id']
                );
                $count++;
            }
        }

        return $count;
    }
}
