<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "p.status != 'discontinued'";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s, $s, $s]);
        }
        if (!empty($filters['category_id'])) { $where .= " AND p.category_id = ?"; $params[] = $filters['category_id']; }
        if (!empty($filters['brand_id'])) { $where .= " AND p.brand_id = ?"; $params[] = $filters['brand_id']; }
        if (!empty($filters['status'])) { $where .= " AND p.status = ?"; $params[] = $filters['status']; }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM products p WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, (int)($filters['per_page'] ?? 20), $page);

        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, b.name as brand_name, u.symbol as unit_symbol
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['products' => $products, 'pagination' => $pagination];
    }

    public function getById($id) {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name, b.name as brand_name, u.name as unit_name, u.symbol as unit_symbol
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE p.id = ?", [$id]
        );
    }

    public function create($data) {
        $sku = !empty($data['sku']) ? $data['sku'] : generateSKU();
        $id = $this->db->insert('products', [
            'sku' => $sku,
            'name' => sanitize($data['name']),
            'category_id' => !empty($data['category_id']) ? $data['category_id'] : null,
            'brand_id' => !empty($data['brand_id']) ? $data['brand_id'] : null,
            'unit_id' => !empty($data['unit_id']) ? $data['unit_id'] : null,
            'description' => sanitize($data['description'] ?? ''),
            'price' => (float)($data['price'] ?? 0),
            'cost' => (float)($data['cost'] ?? 0),
            'min_stock' => (int)($data['min_stock'] ?? 0),
            'reorder_level' => (int)($data['reorder_level'] ?? 0),
            'barcode' => !empty($data['barcode']) ? $data['barcode'] : $sku,
            'status' => $data['status'] ?? 'active',
        ]);

        // Handle image upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $this->uploadImage($_FILES['image'], $id);
            if ($imagePath) {
                $this->db->update('products', ['image' => $imagePath], 'id = ?', [$id]);
            }
        }

        // Generate barcode image (placeholder)
        $barcodePath = $this->generateBarcode($sku, $id);
        if ($barcodePath) {
            $this->db->update('products', ['barcode' => $barcodePath, 'qr_code' => $barcodePath], 'id = ?', [$id]);
        }

        // Initialize inventory for every active branch of the user's store so stock is tracked per branch
        $branchIds = $this->db->fetchAll(
            "SELECT b.id FROM branches b
             WHERE b.status = 'active' AND b.store_id = (SELECT store_id FROM users WHERE id = ?)",
            [$_SESSION['user_id'] ?? 0]
        );
        if (!$branchIds) {
            $branchIds = $this->db->fetchAll("SELECT id FROM branches WHERE status = 'active'");
        }
        foreach ($branchIds as $b) {
            $this->db->insert('inventory', [
                'product_id' => $id,
                'branch_id' => $b['id'],
                'qty' => 0,
                'min_stock' => (int)($data['min_stock'] ?? 0),
                'reorder_level' => (int)($data['reorder_level'] ?? 0),
            ]);
        }
        $this->syncTotals($id);

        logActivity('product_created', 'products', $id, null, $data);
        return $id;
    }

    public function update($id, $data) {
        $old = $this->getById($id);
        $updates = [];
        foreach (['name', 'description', 'price', 'cost', 'min_stock', 'reorder_level', 'status'] as $field) {
            if (isset($data[$field])) $updates[$field] = $data[$field];
        }
        foreach (['category_id', 'brand_id', 'unit_id'] as $field) {
            if (isset($data[$field])) $updates[$field] = !empty($data[$field]) ? $data[$field] : null;
        }
        if (isset($data['sku'])) $updates['sku'] = $data['sku'];
        if (isset($data['barcode'])) $updates['barcode'] = $data['barcode'];

        if (!empty($_FILES['image']['tmp_name'])) {
            $imagePath = $this->uploadImage($_FILES['image'], $id);
            if ($imagePath) $updates['image'] = $imagePath;
        }

        $this->db->update('products', $updates, 'id = ?', [$id]);
        if (isset($data['min_stock']) || isset($data['reorder_level'])) {
            $this->db->update('inventory', [
                'min_stock' => (int)(isset($data['min_stock']) ? $data['min_stock'] : $old['min_stock'] ?? 0),
                'reorder_level' => (int)(isset($data['reorder_level']) ? $data['reorder_level'] : $old['reorder_level'] ?? 0),
            ], 'product_id = ?', [$id]);
        }
        logActivity('product_updated', 'products', $id, $old, $updates);
    }

    public function delete($id) {
        $this->db->update('products', ['status' => 'discontinued'], 'id = ?', [$id]);
        logActivity('product_deleted', 'products', $id);
    }

    public function getByBarcode($barcode) {
        return $this->db->fetch(
            "SELECT p.*, (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id=p.id AND branch_id=?) as stock
             FROM products p WHERE (p.barcode = ? OR p.sku = ?) AND p.status='active'",
            [$_SESSION['user_branch'] ?? 1, $barcode, $barcode]
        );
    }

    public function getStock($productId, $branchId) {
        return $this->db->fetch("SELECT * FROM inventory WHERE product_id=? AND branch_id=?", [$productId, $branchId]);
    }

    public function updateStock($productId, $branchId, $qty, $type, $referenceId = null, $referenceType = null, $notes = null) {
        $inv = $this->getStock($productId, $branchId);
        if (!$inv) {
            $this->db->insert('inventory', ['product_id' => $productId, 'branch_id' => $branchId, 'qty' => $qty]);
        } else {
            $newQty = in_array($type, ['in', 'return']) ? $inv['qty'] + $qty : $inv['qty'] - $qty;
            $this->db->update('inventory', ['qty' => max(0, $newQty)], 'product_id=? AND branch_id=?', [$productId, $branchId]);
        }

        $this->db->insert('stock_logs', [
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => $type,
            'qty' => $qty,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'notes' => $notes,
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);

        // Keep aggregate stock columns in sync across all branches
        $this->syncTotals($productId);

        // Check reorder level
        $this->checkReorderLevel($productId, $branchId);
    }

    public function syncTotals($productId) {
        $row = $this->db->fetch(
            "SELECT IFNULL(SUM(qty), 0) AS total_stock, COUNT(*) AS branch_count
             FROM inventory WHERE product_id = ?",
            [$productId]
        );
        $this->db->update('products', [
            'total_stock' => (int)$row['total_stock'],
            'branch_count' => (int)$row['branch_count'],
        ], 'id = ?', [(int)$productId]);
    }

    private function checkReorderLevel($productId, $branchId) {
        $inv = $this->getStock($productId, $branchId);
        if ($inv && $inv['qty'] <= $inv['reorder_level'] && $inv['reorder_level'] > 0) {
            $product = $this->getById($productId);
            $this->db->insert('notifications', [
                'title' => 'Low Stock Alert',
                'message' => "{$product['name']} is below reorder level ({$inv['qty']} remaining)",
                'type' => 'warning',
                'link' => APP_URL . "/pages/inventory/index.php?product_id={$productId}",
            ]);
        }
    }

    private function uploadImage($file, $productId) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return null;

        $dir = UPLOAD_PATH . '/products/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = "product_{$productId}_" . time() . ".{$ext}";
        $dest = $dir . $filename;
        if (is_uploaded_file($file['tmp_name'])) {
            $ok = move_uploaded_file($file['tmp_name'], $dest);
        } else {
            $ok = rename($file['tmp_name'], $dest);
            if (!$ok) $ok = copy($file['tmp_name'], $dest) && unlink($file['tmp_name']);
        }
        if (!$ok) return null;
        @chmod($dest, 0644);
        return "uploads/products/{$filename}";
    }

    private function generateBarcode($code, $productId) {
        // Simple barcode placeholder - integrate picqer/php-barcode-generator in production
        return null;
    }

    public function exportCSV($filters = []) {
        $where = "p.status != 'discontinued'";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s, $s, $s]);
        }
        if (!empty($filters['category_id'])) { $where .= " AND p.category_id = ?"; $params[] = $filters['category_id']; }
        if (!empty($filters['brand_id'])) { $where .= " AND p.brand_id = ?"; $params[] = $filters['brand_id']; }

        return $this->db->fetchAll(
            "SELECT p.sku, p.name, c.name as category_name, b.name as brand_name, u.symbol as unit_symbol,
                    p.price, p.cost, p.min_stock, p.reorder_level, p.total_stock, p.barcode, p.status
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE {$where}
             ORDER BY p.name ASC",
            $params
        );
    }
}
