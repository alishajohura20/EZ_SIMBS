<?php
class Branch {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        if (is_string($filters)) $filters = ['search' => $filters];
        $search = $filters['search'] ?? '';
        $where = "1=1"; $params = [];
        if ($search) { $where .= " AND (b.name LIKE ? OR b.address LIKE ?)"; $s = "%{$search}%"; $params[] = $s; $params[] = $s; }

        $total = $this->db->fetch("
            SELECT COUNT(*) as t FROM branches b WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 10, $page);

        $branches = $this->db->fetchAll("
            SELECT b.*, u.name as manager_name,
                (SELECT COUNT(*) FROM inventory WHERE branch_id = b.id) as product_count,
                (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE branch_id = b.id) as total_stock,
                (SELECT COUNT(*) FROM sales WHERE branch_id = b.id AND status='completed' AND DATE(created_at)=CURDATE()) as today_sales,
                (SELECT IFNULL(SUM(grand_total),0) FROM sales WHERE branch_id = b.id AND status='completed' AND DATE(created_at)=CURDATE()) as today_revenue
            FROM branches b LEFT JOIN users u ON b.manager_id = u.id
            WHERE {$where} ORDER BY b.name
            LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}", $params
        );

        return ['branches' => $branches, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        return $this->db->fetch("SELECT b.*, u.name as manager_name FROM branches b LEFT JOIN users u ON b.manager_id = u.id WHERE b.id = ?", [$id]);
    }

    public function getSummary($id) {
        $branch = $this->getById($id);
        if (!$branch) return null;

        $inventory = $this->db->fetch("
            SELECT COUNT(*) as products, IFNULL(SUM(qty),0) as total_qty, IFNULL(SUM(qty * p.cost),0) as stock_value
            FROM inventory i JOIN products p ON i.product_id = p.id WHERE i.branch_id = ?", [$id]
        );

        $sales = $this->db->fetch("
            SELECT COUNT(*) as total_sales, IFNULL(SUM(grand_total),0) as total_revenue
            FROM sales WHERE branch_id = ? AND status = 'completed'", [$id]
        );

        $lowStock = $this->db->fetch("
            SELECT COUNT(*) as count FROM inventory
            WHERE branch_id = ? AND qty <= reorder_level AND reorder_level > 0", [$id]
        )['count'];

        $staff = $this->db->fetch("SELECT COUNT(*) as count FROM users WHERE branch_id = ? AND status = 'active'", [$id])['count'];

        return [
            'branch' => $branch,
            'inventory' => $inventory,
            'sales' => $sales,
            'low_stock' => $lowStock,
            'staff_count' => $staff,
        ];
    }

    public function create($data) {
        $id = $this->db->insert('branches', [
            'name' => sanitize($data['name']),
            'address' => sanitize($data['address'] ?? ''),
            'phone' => sanitize($data['phone'] ?? ''),
            'manager_id' => $data['manager_id'] ?: null,
            'status' => $data['status'] ?? 'active',
        ]);
        logActivity('branch_created', 'branches', $id);
        return $id;
    }

    public function update($id, $data) {
        $updates = [];
        if (isset($data['name'])) $updates['name'] = sanitize($data['name']);
        if (isset($data['address'])) $updates['address'] = sanitize($data['address']);
        if (isset($data['phone'])) $updates['phone'] = sanitize($data['phone']);
        if (isset($data['manager_id'])) $updates['manager_id'] = $data['manager_id'] ?: null;
        if (isset($data['status'])) $updates['status'] = $data['status'];
        $this->db->update('branches', $updates, 'id = ?', [$id]);
        logActivity('branch_updated', 'branches', $id);
    }

    public function delete($id) {
        $hasInventory = $this->db->count('inventory', 'branch_id = ? AND qty > 0', [$id]);
        if ($hasInventory > 0) return ['success' => false, 'message' => 'Cannot delete branch with stock'];
        $hasSales = $this->db->count('sales', 'branch_id = ?', [$id]);
        if ($hasSales > 0) return ['success' => false, 'message' => 'Cannot delete branch with sales history'];
        $this->db->update('branches', ['status' => 'inactive'], 'id = ?', [$id]);
        logActivity('branch_deactivated', 'branches', $id);
        return ['success' => true];
    }

    public function getBranchInventory($branchId, $filters = []) {
        $where = "i.branch_id = ?"; $params = [$branchId];
        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $s = "%{$filters['search']}%"; $params[] = $s; $params[] = $s;
        }

        return $this->db->fetchAll("
            SELECT i.*, p.name, p.sku, p.price, p.cost, c.name as category, u.symbol as unit_symbol,
                (i.qty * p.cost) as stock_value,
                CASE WHEN i.qty = 0 THEN 'out' WHEN i.qty <= i.reorder_level THEN 'low' ELSE 'ok' END as stock_status
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN units u ON p.unit_id = u.id
            WHERE {$where} ORDER BY i.qty ASC", $params
        );
    }

    public function getTransferSuggestions($branchId) {
        return $this->db->fetchAll("
            SELECT p.name, p.sku, i1.product_id, i1.qty as needed_qty, i1.reorder_level,
                    i2.branch_id as from_branch_id, b.name as from_branch, i2.qty as available_qty,
                    (i1.reorder_level * 2 - i1.qty) as transfer_qty
            FROM inventory i1
            JOIN products p ON i1.product_id = p.id
            JOIN inventory i2 ON i1.product_id = i2.product_id AND i2.branch_id != i1.branch_id
            JOIN branches b ON i2.branch_id = b.id
            WHERE i1.branch_id = ? AND i1.qty <= i1.reorder_level AND i1.reorder_level > 0
            AND i2.qty > i1.reorder_level
            ORDER BY (i1.qty / i1.reorder_level) ASC
        ", [$branchId]);
    }
}
