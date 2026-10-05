<?php
class Brand {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll($search = '', $status = '') {
        $where = "1=1"; $params = [];
        if ($search) { $where .= " AND b.name LIKE ?"; $params[] = "%{$search}%"; }
        if ($status) { $where .= " AND b.status = ?"; $params[] = $status; }
        return $this->db->fetchAll(
            "SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count
             FROM brands b WHERE {$where} ORDER BY b.name", $params
        );
    }

    public function getById($id) { return $this->db->fetch("SELECT * FROM brands WHERE id=?", [$id]); }

    public function create($data) {
        $id = $this->db->insert('brands', ['name' => sanitize($data['name']), 'status' => $data['status'] ?? 'active']);
        logActivity('brand_created', 'brands', $id, null, $data);
        return $id;
    }

    public function update($id, $data) {
        $old = $this->getById($id);
        $this->db->update('brands', ['name' => sanitize($data['name']), 'status' => $data['status'] ?? 'active'], 'id=?', [$id]);
        logActivity('brand_updated', 'brands', $id, $old, $data);
    }

    public function delete($id) {
        $this->db->update('brands', ['status' => 'inactive'], 'id=?', [$id]);
        logActivity('brand_deleted', 'brands', $id);
    }
}
