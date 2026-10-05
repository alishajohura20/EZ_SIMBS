<?php
class Store {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($data) {
        $storeName  = trim($data['store_name'] ?? '');
        $ownerName  = trim($data['name'] ?? '');
        $email      = trim($data['email'] ?? '');
        $phone      = trim($data['phone'] ?? '');
        $address    = trim($data['address'] ?? '');
        $password   = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';

        if ($storeName === '' || $ownerName === '' || $email === '') {
            return ['success' => false, 'message' => 'Store name, your name and email are required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        if ($password !== $passwordConfirm) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        if ($this->db->fetch("SELECT id FROM stores WHERE email = ?", [$email])) {
            return ['success' => false, 'message' => 'An account with this email already exists'];
        }
        if ($this->db->fetch("SELECT id FROM users WHERE email = ?", [$email])) {
            return ['success' => false, 'message' => 'An account with this email already exists'];
        }

        $storeId = $this->db->insert('stores', [
            'name'       => sanitize($storeName),
            'owner_name' => sanitize($ownerName),
            'email'      => sanitize($email),
            'phone'      => sanitize($phone),
            'address'    => sanitize($address),
            'plan'       => 'free',
            'status'     => 'active',
        ]);

        $adminRoleId = (int)($this->db->fetch("SELECT id FROM roles WHERE name = 'admin'")['id'] ?? 1);

        $branchId = $this->db->insert('branches', [
            'store_id' => $storeId,
            'name'     => sanitize($storeName . ' (Main)'),
            'address'  => sanitize($address),
            'phone'    => sanitize($phone),
            'status'   => 'active',
        ]);

        $userId = $this->db->insert('users', [
            'name'     => sanitize($ownerName),
            'email'    => sanitize($email),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id'  => $adminRoleId,
            'store_id' => $storeId,
            'branch_id'=> $branchId,
            'phone'    => sanitize($phone),
            'status'   => 'active',
        ]);

        logActivity('store_registered', 'stores', $storeId, null, [
            'store_name' => $storeName,
            'owner'      => $ownerName,
        ]);

        return [
            'success'  => true,
            'store_id' => $storeId,
            'user_id'  => $userId,
            'email'    => $email,
        ];
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM stores WHERE id = ?", [(int)$id]);
    }

    public function getForUser($userId) {
        return $this->db->fetch(
            "SELECT s.* FROM stores s JOIN users u ON u.store_id = s.id WHERE u.id = ?",
            [(int)$userId]
        );
    }

    public function getByEmail($email) {
        return $this->db->fetch("SELECT * FROM stores WHERE email = ?", [trim($email)]);
    }

    public function getTeamMembers($storeId) {
        return $this->db->fetchAll(
            "SELECT u.id, u.name, u.email, u.phone, u.status, r.name AS role_name, b.name AS branch_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN branches b ON b.id = u.branch_id
             WHERE u.store_id = ?
             ORDER BY r.id, u.name",
            [(int)$storeId]
        );
    }

    public function getTeamCounts($storeId) {
        $rows = $this->db->fetchAll(
            "SELECT r.name AS role_name, COUNT(*) AS total
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.store_id = ?
             GROUP BY r.id, r.name ORDER BY r.id",
            [(int)$storeId]
        );
        $counts = [];
        foreach ($rows as $r) {
            $counts[$r['role_name']] = (int)$r['total'];
        }
        return $counts;
    }

    public function getBranchCount($storeId) {
        return $this->db->count('branches', 'store_id = ?', [(int)$storeId]);
    }

    public function getStats($storeId) {
        $branchId = $this->db->fetch("SELECT id FROM branches WHERE store_id = ? ORDER BY id LIMIT 1", [(int)$storeId])['id'] ?? null;

        $dash = new Dashboard();
        $stats = $branchId ? $dash->getStats($branchId) : $dash->getStats();

        $teamCounts = $this->getTeamCounts($storeId);
        $totalStaff = array_sum($teamCounts);

        return array_merge($stats, [
            'total_staff'  => $totalStaff,
            'team_counts'  => $teamCounts,
            'branch_count' => $this->getBranchCount($storeId),
        ]);
    }

    public function getBranchPerformance($storeId) {
        $performance = $this->db->fetchAll("
            SELECT b.id, b.name, b.status,
                IFNULL(SUM(s.grand_total), 0) as sales_total,
                IFNULL(SUM(CASE WHEN DATE(s.created_at) = CURDATE() THEN s.grand_total ELSE 0 END), 0) as today_sales,
                COUNT(s.id) as sales_count
            FROM branches b
            LEFT JOIN sales s ON s.branch_id = b.id AND s.status = 'completed'
            WHERE b.store_id = ?
            GROUP BY b.id, b.name, b.status
            ORDER BY sales_total DESC",
            [(int)$storeId]
        );

        $cogsRows = $this->db->fetchAll("
            SELECT s.branch_id, IFNULL(SUM(si.qty * p.cost), 0) as cogs
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id AND s.status = 'completed'
            JOIN products p ON si.product_id = p.id
            WHERE s.branch_id IN (SELECT id FROM branches WHERE store_id = ?)
            GROUP BY s.branch_id",
            [(int)$storeId]
        );

        $cogsMap = [];
        foreach ($cogsRows as $c) {
            $cogsMap[$c['branch_id']] = (float)$c['cogs'];
        }

        foreach ($performance as &$r) {
            $r['cogs']   = $cogsMap[$r['id']] ?? 0;
            $r['profit'] = (float)$r['sales_total'] - (float)$r['cogs'];
        }

        return $performance;
    }

    public function getTopProduct($storeId) {
        return $this->db->fetch("
            SELECT p.id, p.name, p.sku, p.image,
                SUM(si.qty) as total_sold, SUM(si.total) as revenue
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            JOIN products p ON si.product_id = p.id
            WHERE s.status = 'completed' AND s.branch_id IN (SELECT id FROM branches WHERE store_id = ?)
            GROUP BY p.id
            ORDER BY total_sold DESC, revenue DESC
            LIMIT 1",
            [(int)$storeId]
        );
    }

    public function update($id, $data) {
        $allowed = ['name','phone','address','currency','currency_symbol'];
        $safe = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $allowed)) $safe[$k] = sanitize($v);
        }
        if (empty($safe)) return 0;
        return $this->db->update('stores', $safe, 'id = ?', [(int)$id]);
    }
}
