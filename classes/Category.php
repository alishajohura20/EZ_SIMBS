<?php
class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($search = '', $status = '') {
        $where = "1=1";
        $params = [];
        if ($search) { $where .= " AND c.name LIKE ?"; $params[] = "%{$search}%"; }
        if ($status) { $where .= " AND c.status = ?"; $params[] = $status; }
        return $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count,
                    p.name as parent_name
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE {$where} ORDER BY c.name ASC", $params
        );
    }

    public function getById($id) {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public function create($data) {
        $id = $this->db->insert('categories', [
            'name' => sanitize($data['name']),
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
            'status' => $data['status'] ?? 'active',
        ]);
        logActivity('category_created', 'categories', $id, null, $data);
        return $id;
    }

    public function update($id, $data) {
        $old = $this->getById($id);
        $this->db->update('categories', [
            'name' => sanitize($data['name']),
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
            'status' => $data['status'] ?? 'active',
        ], 'id = ?', [$id]);
        logActivity('category_updated', 'categories', $id, $old, $data);
    }

    public function delete($id) {
        $this->db->update('categories', ['status' => 'inactive'], 'id = ?', [$id]);
        logActivity('category_deleted', 'categories', $id);
    }

    public function getTree() {
        $all = $this->db->fetchAll("SELECT id, name, parent_id FROM categories WHERE status='active' ORDER BY name");
        return $this->buildTree($all);
    }

    private function buildTree($items, $parentId = null) {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $item['children'] = $this->buildTree($items, $item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
