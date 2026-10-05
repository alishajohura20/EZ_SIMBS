<?php
class Unit {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll($search = '', $used = '') {
        $where = "1=1"; $params = [];
        if ($search) { $where .= " AND (u.name LIKE ? OR u.symbol LIKE ?)"; $params[] = "%{$search}%"; $params[] = "%{$search}%"; }
        $rows = $this->db->fetchAll(
            "SELECT u.*, (SELECT COUNT(*) FROM products p WHERE p.unit_id = u.id) as product_count
             FROM units u WHERE {$where} ORDER BY u.name", $params
        );
        if ($used === 'used') { $rows = array_values(array_filter($rows, fn($r) => (int)$r['product_count'] > 0)); }
        if ($used === 'unused') { $rows = array_values(array_filter($rows, fn($r) => (int)$r['product_count'] === 0)); }
        return $rows;
    }
    public function getById($id) { return $this->db->fetch("SELECT * FROM units WHERE id=?", [$id]); }
    public function create($data) {
        $id = $this->db->insert('units', ['name' => sanitize($data['name']), 'symbol' => sanitize($data['symbol'])]);
        logActivity('unit_created', 'units', $id, null, $data);
        return $id;
    }
    public function update($id, $data) {
        $old = $this->getById($id);
        $this->db->update('units', ['name' => sanitize($data['name']), 'symbol' => sanitize($data['symbol'])], 'id=?', [$id]);
        logActivity('unit_updated', 'units', $id, $old, $data);
    }
    public function delete($id) {
        $this->db->delete('units', 'id=?', [$id]);
        logActivity('unit_deleted', 'units', $id);
    }
}
