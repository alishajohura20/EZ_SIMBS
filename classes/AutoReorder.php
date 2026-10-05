<?php
class AutoReorder {
    private $db;
    private $notification;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->notification = new Notification();
    }

    public function checkAndCreateReorderRequests() {
        $lowStockProducts = $this->db->fetchAll("
            SELECT i.product_id, i.branch_id, i.qty, i.reorder_level, i.min_stock,
                    p.name, p.sku, p.cost, b.name as branch_name
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            JOIN branches b ON i.branch_id = b.id
            WHERE i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status = 'active'
        ");

        $reorderRequests = [];
        foreach ($lowStockProducts as $product) {
            $existing = $this->db->fetch(
                "SELECT id FROM purchases WHERE status IN ('draft','pending')
                 AND id IN (SELECT purchase_id FROM purchase_items WHERE product_id = ?)",
                [$product['product_id']]
            );

            if (!$existing) {
                $supplier = $this->db->fetch(
                    "SELECT s.id, s.company_name
                     FROM purchase_items pi
                     JOIN purchases p ON pi.purchase_id = p.id
                     JOIN suppliers s ON p.supplier_id = s.id
                     WHERE pi.product_id = ? AND p.status = 'received'
                     GROUP BY s.id ORDER BY COUNT(*) DESC LIMIT 1",
                    [$product['product_id']]
                );

                $reorderQty = max(
                    ($product['reorder_level'] * 2) - $product['qty'],
                    $product['min_stock'] > 0 ? $product['min_stock'] - $product['qty'] : 10
                );

                $reorderRequests[] = [
                    'product_id' => $product['product_id'],
                    'product_name' => $product['name'],
                    'sku' => $product['sku'],
                    'current_qty' => $product['qty'],
                    'reorder_level' => $product['reorder_level'],
                    'suggested_qty' => $reorderQty,
                    'estimated_cost' => $reorderQty * $product['cost'],
                    'branch' => $product['branch_name'],
                    'supplier' => $supplier ? $supplier['company_name'] : 'No preferred supplier',
                    'supplier_id' => $supplier['id'] ?? null,
                ];

                if ($supplier) {
                    $this->createAutoPO($product, $reorderQty, $supplier['id']);
                }
            }
        }

        if (!empty($reorderRequests)) {
            $count = count($reorderRequests);
            $this->notification->createForRole(
                'Auto-Reorder Triggered',
                "{$count} product(s) fell below reorder level. Purchase orders have been auto-created in draft status.",
                ['admin', 'manager'],
                'warning',
                APP_URL . '/pages/purchases/?status=draft'
            );
        }

        return $reorderRequests;
    }

    private function createAutoPO($product, $qty, $supplierId) {
        $poNumber = generatePONumber();
        $totalCost = $qty * $product['cost'];

        $purchaseId = $this->db->insert('purchases', [
            'supplier_id' => $supplierId,
            'po_number' => $poNumber,
            'status' => 'draft',
            'total' => $totalCost,
            'due' => $totalCost,
            'notes' => "Auto-generated reorder for {$product['name']} ({$product['sku']}) - Stock: {$product['qty']}, Reorder Level: {$product['reorder_level']}",
            'created_by' => null,
            'branch_id' => $product['branch_id'],
        ]);

        $this->db->insert('purchase_items', [
            'purchase_id' => $purchaseId,
            'product_id' => $product['product_id'],
            'qty' => $qty,
            'received_qty' => 0,
            'unit_cost' => $product['cost'],
            'total' => $totalCost,
        ]);

        logActivity('auto_reorder_created', 'purchases', $purchaseId, null, [
            'product' => $product['name'],
            'qty' => $qty,
            'reason' => 'below_reorder_level',
        ]);

        return $purchaseId;
    }

    public function getPendingReorders() {
        return $this->db->fetchAll("
            SELECT p.id, p.po_number, p.total, p.status, p.created_at,
                    s.company_name as supplier_name,
                    pi.product_id, pr.name as product_name, pr.sku, pi.qty as order_qty
            FROM purchases p
            JOIN suppliers s ON p.supplier_id = s.id
            JOIN purchase_items pi ON pi.purchase_id = p.id
            JOIN products pr ON pi.product_id = pr.id
            WHERE p.notes LIKE '%Auto-generated reorder%'
            AND p.status IN ('draft', 'pending')
            ORDER BY p.created_at DESC
        ");
    }

    public function getSuggestions($productId = null) {
        $where = $productId ? "AND i.product_id = ?" : "";
        $params = $productId ? [$productId] : [];

        return $this->db->fetchAll("
            SELECT p.name, p.sku, p.cost, i.qty, i.reorder_level,
                    b.name as branch_name, b.id as branch_id,
                    ROUND((i.reorder_level * 2) - i.qty) as suggested_qty,
                    ROUND(((i.reorder_level * 2) - i.qty) * p.cost, 2) as estimated_cost
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            JOIN branches b ON i.branch_id = b.id
            WHERE i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status = 'active'
            {$where}
            ORDER BY (i.qty / i.reorder_level) ASC
        ", $params);
    }
}
