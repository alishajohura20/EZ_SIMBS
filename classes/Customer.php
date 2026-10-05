<?php
class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ? OR c.membership_id LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s, $s, $s, $s]);
        }
        if (!empty($filters['is_vip'])) {
            $where .= " AND c.is_vip = 1";
        }
        if (!empty($filters['status'])) {
            $where .= " AND c.status = ?";
            $params[] = $filters['status'];
        }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM customers c WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $customers = $this->db->fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM sales WHERE customer_id = c.id) as purchase_count,
                    (SELECT IFNULL(SUM(grand_total),0) FROM sales WHERE customer_id = c.id AND status='completed') as total_spent,
                    (SELECT MAX(created_at) FROM sales WHERE customer_id = c.id) as last_purchase
             FROM customers c
             WHERE {$where}
             ORDER BY c.name ASC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['customers' => $customers, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM customers WHERE id = ?", [$id]);
    }

    public function getProfile($id) {
        $customer = $this->getById($id);
        if (!$customer) return null;

        $purchases = $this->db->fetchAll(
            "SELECT s.*, u.name as cashier_name
             FROM sales s LEFT JOIN users u ON s.created_by = u.id
             WHERE s.customer_id = ? AND s.status = 'completed'
             ORDER BY s.created_at DESC LIMIT 20", [$id]
        );

        $stats = $this->db->fetch(
            "SELECT COUNT(*) as total_orders, IFNULL(SUM(grand_total),0) as total_spent,
                    IFNULL(AVG(grand_total),0) as avg_order
             FROM sales WHERE customer_id = ? AND status = 'completed'", [$id]
        );

        return [
            'customer' => $customer,
            'purchases' => $purchases,
            'stats' => $stats,
        ];
    }

    public function create($data) {
        $membershipId = !empty($data['membership_id']) ? $data['membership_id'] : ('MEM-' . strtoupper(substr(uniqid(), -6)));
        $id = $this->db->insert('customers', [
            'name' => sanitize($data['name']),
            'phone' => sanitize($data['phone'] ?? ''),
            'email' => sanitize($data['email'] ?? ''),
            'address' => sanitize($data['address'] ?? ''),
            'membership_id' => $membershipId,
            'loyalty_points' => (int)($data['loyalty_points'] ?? 0),
            'is_vip' => (int)($data['is_vip'] ?? 0),
            'notes' => sanitize($data['notes'] ?? ''),
            'status' => $data['status'] ?? 'active',
        ]);
        logActivity('customer_created', 'customers', $id, null, $data);
        return $id;
    }

    public function update($id, $data) {
        $updates = [];
        foreach (['name', 'phone', 'email', 'address', 'notes', 'status'] as $field) {
            if (isset($data[$field])) $updates[$field] = sanitize($data[$field]);
        }
        if (isset($data['is_vip'])) $updates['is_vip'] = (int)$data['is_vip'];
        if (isset($data['loyalty_points'])) $updates['loyalty_points'] = (int)$data['loyalty_points'];

        $this->db->update('customers', $updates, 'id = ?', [$id]);
        logActivity('customer_updated', 'customers', $id, null, $updates);
    }

    public function delete($id) {
        $hasSales = $this->db->count('sales', 'customer_id = ?', [$id]);
        if ($hasSales > 0) {
            return ['success' => false, 'message' => 'Cannot delete customer with existing sales'];
        }
        $this->db->delete('customers', 'id = ?', [$id]);
        logActivity('customer_deleted', 'customers', $id);
        return ['success' => true];
    }

    public function addLoyaltyPoints($id, $points) {
        $this->db->query("UPDATE customers SET loyalty_points = loyalty_points + ? WHERE id = ?", [$points, $id]);
    }

    public function deductBalance($id, $amount) {
        $customer = $this->getById($id);
        if ($customer['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient balance'];
        }
        $this->db->query("UPDATE customers SET balance = balance - ? WHERE id = ?", [$amount, $id]);
        return ['success' => true];
    }

    public function getByPhone($phone) {
        return $this->db->fetch("SELECT * FROM customers WHERE phone = ? AND status = 'active'", [$phone]);
    }
}
