<?php
class Supplier {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (company_name LIKE ? OR contact_person LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s, $s, $s, $s]);
        }
        if (!empty($filters['status'])) {
            $where .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM suppliers WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $suppliers = $this->db->fetchAll(
            "SELECT s.*,
                    (SELECT COUNT(*) FROM purchases WHERE supplier_id = s.id) as purchase_count,
                    (SELECT IFNULL(SUM(due),0) FROM purchases WHERE supplier_id = s.id AND due > 0) as outstanding_due,
                    (SELECT MAX(created_at) FROM purchases WHERE supplier_id = s.id) as last_transaction
             FROM suppliers s
             WHERE {$where}
             ORDER BY s.company_name ASC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['suppliers' => $suppliers, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM suppliers WHERE id = ?", [$id]);
    }

    public function getSummary($id) {
        $supplier = $this->getById($id);
        if (!$supplier) return null;

        $purchases = $this->db->fetch(
            "SELECT COUNT(*) as total_purchases, IFNULL(SUM(total),0) as total_amount,
                    IFNULL(SUM(paid),0) as total_paid, IFNULL(SUM(due),0) as total_due
             FROM purchases WHERE supplier_id = ?", [$id]
        );

        $recentPurchases = $this->db->fetchAll(
            "SELECT p.*, u.name as created_by_name
             FROM purchases p LEFT JOIN users u ON p.created_by = u.id
             WHERE p.supplier_id = ? ORDER BY p.created_at DESC LIMIT 10", [$id]
        );

        $productsSupplied = $this->db->fetchAll(
            "SELECT DISTINCT pr.id, pr.name, pr.sku
             FROM purchase_items pi JOIN products pr ON pi.product_id = pr.id
             JOIN purchases p ON pi.purchase_id = p.id
             WHERE p.supplier_id = ?", [$id]
        );

        return [
            'supplier' => $supplier,
            'stats' => $purchases,
            'recent_purchases' => $recentPurchases,
            'products_supplied' => $productsSupplied,
        ];
    }

    public function create($data) {
        $id = $this->db->insert('suppliers', [
            'company_name' => sanitize($data['company_name']),
            'contact_person' => sanitize($data['contact_person'] ?? ''),
            'email' => sanitize($data['email'] ?? ''),
            'phone' => sanitize($data['phone'] ?? ''),
            'address' => sanitize($data['address'] ?? ''),
            'tax_id' => sanitize($data['tax_id'] ?? ''),
            'rating' => (float)($data['rating'] ?? 0),
            'status' => $data['status'] ?? 'active',
        ]);
        logActivity('supplier_created', 'suppliers', $id, null, $data);
        return $id;
    }

    public function update($id, $data) {
        $old = $this->getById($id);
        $updates = [];
        foreach (['company_name', 'contact_person', 'email', 'phone', 'address', 'tax_id', 'status'] as $field) {
            if (isset($data[$field])) $updates[$field] = sanitize($data[$field]);
        }
        if (isset($data['rating'])) $updates['rating'] = (float)$data['rating'];

        $this->db->update('suppliers', $updates, 'id = ?', [$id]);
        logActivity('supplier_updated', 'suppliers', $id, $old, $updates);
    }

    public function delete($id) {
        $hasPurchases = $this->db->count('purchases', 'supplier_id = ?', [$id]);
        if ($hasPurchases > 0) {
            return ['success' => false, 'message' => 'Cannot delete supplier with existing purchases'];
        }
        $this->db->delete('suppliers', 'id = ?', [$id]);
        logActivity('supplier_deleted', 'suppliers', $id);
        return ['success' => true];
    }

    public function updateRating($id, $rating) {
        $this->db->update('suppliers', ['rating' => (float)$rating], 'id = ?', [$id]);
    }

    /** Credit balance held with this supplier (Phase 14). Never negative. */
    public function getBalance($id) {
        $row = $this->db->fetch("SELECT balance FROM suppliers WHERE id = ?", [$id]);
        return $row ? (float)$row['balance'] : 0.0;
    }

    public function addBalance($id, $amount) {
        $amount = round((float)$amount, 2);
        if ($amount <= 0) return;
        $this->db->query('UPDATE suppliers SET balance = balance + ? WHERE id = ?', [$amount, (int)$id]);
    }

    public function deductBalance($id, $amount) {
        $amount = round((float)$amount, 2);
        if ($amount <= 0) return;
        $this->db->query(
            'UPDATE suppliers SET balance = GREATEST(balance - ?, 0) WHERE id = ?',
            [$amount, (int)$id]
        );
    }
}
