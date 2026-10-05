<?php
class Inventory {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getStock($productId, $branchId = null) {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        return $this->db->fetch(
            "SELECT i.*, p.name as product_name, p.sku, p.reorder_level as product_reorder,
                    p.min_stock as product_min_stock, b.name as branch_name
             FROM inventory i
             JOIN products p ON i.product_id = p.id
             JOIN branches b ON i.branch_id = b.id
             WHERE i.product_id = ? AND i.branch_id = ?",
            [$productId, $branchId]
        );
    }

    public function getAllStock($branchId = null, $filters = []) {
        $where = "1=1";
        $params = [];
        if (!empty($branchId)) {
            $where = "i.branch_id = ?";
            $params = [$branchId];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $s = "%{$filters['search']}%";
            $params[] = $s;
            $params[] = $s;
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'low') {
                $where .= " AND i.qty > 0 AND i.qty <= i.reorder_level AND i.reorder_level > 0";
            } elseif ($filters['status'] === 'out') {
                $where .= " AND i.qty = 0";
            } elseif ($filters['status'] === 'overstock') {
                $where .= " AND i.qty > i.reorder_level * 2";
            }
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as t FROM inventory i JOIN products p ON i.product_id=p.id WHERE {$where}",
            $params
        )['t'];

        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $stock = $this->db->fetchAll(
            "SELECT i.*, p.name as product_name, p.sku, p.price, p.cost, p.image,
                    c.name as category_name, u.symbol as unit_symbol, b.name as branch_name,
                    (i.qty * p.cost) as stock_value
             FROM inventory i
             JOIN products p ON i.product_id = p.id
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN units u ON p.unit_id = u.id
             JOIN branches b ON i.branch_id = b.id
             WHERE {$where}
             ORDER BY i.qty ASC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['stock' => $stock, 'pagination' => $pagination, 'total' => $total];
    }

    public function getStockSummary($branchId = null) {
        $where = !empty($branchId) ? "WHERE i.branch_id = ?" : "";
        $params = !empty($branchId) ? [$branchId] : [];
        return $this->db->fetch(
            "SELECT
                COUNT(DISTINCT i.product_id) as total_products,
                SUM(i.qty) as total_items,
                SUM(i.qty * p.cost) as total_value,
                SUM(CASE WHEN i.qty = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN i.qty > 0 AND i.qty <= i.reorder_level AND i.reorder_level > 0 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN i.qty > i.reorder_level * 2 THEN 1 ELSE 0 END) as overstock
             FROM inventory i
             JOIN products p ON i.product_id = p.id
             {$where}",
            $params
        );
    }

    public function stockIn($productId, $qty, $branchId = null, $referenceId = null, $referenceType = null, $notes = '') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $product = new Product();
        $product->updateStock($productId, $branchId, $qty, 'in', $referenceId, $referenceType, $notes);
        logActivity('stock_in', 'inventory', $productId, null, ['qty' => $qty, 'branch_id' => $branchId]);
        return true;
    }

    public function stockOut($productId, $qty, $branchId = null, $referenceId = null, $referenceType = null, $notes = '') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $inv = $this->getStock($productId, $branchId);
        if (!$inv || $inv['qty'] < $qty) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        $product = new Product();
        $product->updateStock($productId, $branchId, $qty, 'out', $referenceId, $referenceType, $notes);
        logActivity('stock_out', 'inventory', $productId, null, ['qty' => $qty, 'branch_id' => $branchId]);
        return ['success' => true];
    }

    public function adjustStock($productId, $newQty, $branchId = null, $reason = '') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $inv = $this->getStock($productId, $branchId);
        $oldQty = $inv ? $inv['qty'] : 0;
        $diff = abs($newQty - $oldQty);
        $type = $newQty > $oldQty ? 'in' : 'out';

        $this->db->update('inventory', ['qty' => $newQty], 'product_id=? AND branch_id=?', [$productId, $branchId]);
        $this->db->insert('stock_logs', [
            'product_id' => $productId,
            'branch_id' => $branchId,
            'type' => 'adjustment',
            'qty' => $diff,
            'notes' => $reason ?: "Adjusted from {$oldQty} to {$newQty}",
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);

        // Keep aggregate stock columns in sync across all branches
        (new Product())->syncTotals($productId);

        logActivity('stock_adjustment', 'inventory', $productId, ['qty' => $oldQty], ['qty' => $newQty, 'reason' => $reason]);
        return true;
    }

    public function recordDamage($productId, $qty, $branchId = null, $notes = '') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $inv = $this->getStock($productId, $branchId);
        if (!$inv || $inv['qty'] < $qty) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        $product = new Product();
        $product->updateStock($productId, $branchId, $qty, 'damage', null, null, $notes);
        return ['success' => true];
    }

    /**
     * Goods coming back INTO sellable stock.
     * $referenceType was previously hardcoded to 'sale'; the seed data and
     * reports use 'sale_return', so that is now the default.
     */
    public function recordReturn($productId, $qty, $branchId = null, $referenceId = null, $notes = '', $referenceType = 'sale_return') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $qty = (int)$qty;
        if ($qty <= 0) return ['success' => false, 'message' => 'Quantity must be positive'];

        $product = new Product();
        $product->updateStock($productId, $branchId, $qty, 'return', $referenceId, $referenceType, $notes);
        return ['success' => true];
    }

    /**
     * Goods being shipped BACK to a supplier (Phase 14). Outbound: the
     * 'purchase_return' type is not in updateStock()'s inbound list, so
     * the quantity subtracts. unit_cost stays per-PO-line (Phase 06), so
     * the remaining stock's cost basis is unaffected.
     */
    public function recordPurchaseReturn($productId, $qty, $branchId = null, $referenceId = null, $notes = '') {
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? 1);
        $qty = (int)$qty;
        if ($qty <= 0) return ['success' => false, 'message' => 'Quantity must be positive'];

        $product = new Product();
        $product->updateStock($productId, $branchId, $qty, 'purchase_return', $referenceId, 'purchase_return', $notes);
        return ['success' => true];
    }

    public function transferStock($productId, $fromBranch, $toBranch, $qty) {
        $result = $this->stockOut($productId, $qty, $fromBranch, null, null, "Transfer to branch #{$toBranch}");
        if (is_array($result) && !$result['success']) return $result;

        $this->stockIn($productId, $qty, $toBranch, null, null, "Transfer from branch #{$fromBranch}");
        $this->db->insert('stock_logs', [
            'product_id' => $productId, 'branch_id' => $fromBranch,
            'type' => 'transfer', 'qty' => $qty,
            'notes' => "Transferred to branch #{$toBranch}",
            'user_id' => $_SESSION['user_id'] ?? null,
        ]);
        logActivity('stock_transfer', 'inventory', $productId, null, ['from' => $fromBranch, 'to' => $toBranch, 'qty' => $qty]);
        return ['success' => true];
    }

    public function getStockLogs($productId = null, $branchId = null, $type = '', $limit = 100, $page = 1, $productSearch = '') {
        $where = "1=1";
        $params = [];
        if ($productId) { $where .= " AND sl.product_id = ?"; $params[] = $productId; }
        if ($branchId) { $where .= " AND sl.branch_id = ?"; $params[] = $branchId; }
        if ($type) { $where .= " AND sl.type = ?"; $params[] = $type; }
        if ($productSearch) {
            $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $s = "%{$productSearch}%";
            $params[] = $s;
            $params[] = $s;
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as t FROM stock_logs sl
             JOIN products p ON sl.product_id = p.id
             WHERE {$where}",
            $params
        )['t'];

        $pagination = paginate($total, $limit, $page);

        $logs = $this->db->fetchAll(
            "SELECT sl.*, p.name as product_name, p.sku, b.name as branch_name, u.name as user_name
             FROM stock_logs sl
             JOIN products p ON sl.product_id = p.id
             JOIN branches b ON sl.branch_id = b.id
             LEFT JOIN users u ON sl.user_id = u.id
             WHERE {$where}
             ORDER BY sl.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['logs' => $logs, 'pagination' => $pagination, 'total' => $total];
    }

    public function suggestTransfer($productId, $neededQty) {
        $availableBranches = $this->db->fetchAll(
            "SELECT i.branch_id, b.name as branch_name, i.qty
             FROM inventory i
             JOIN branches b ON i.branch_id = b.id
             WHERE i.product_id = ? AND i.qty > ? AND b.status = 'active'
             ORDER BY i.qty DESC",
            [$productId, $neededQty]
        );
        return $availableBranches;
    }
}
