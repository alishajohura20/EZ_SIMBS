<?php
class Purchase {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (p.po_number LIKE ? OR s.company_name LIKE ?)";
            $s = "%{$filters['search']}%";
            $params[] = $s;
            $params[] = $s;
        }
        if (!empty($filters['status'])) {
            $where .= " AND p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['supplier_id'])) {
            $where .= " AND p.supplier_id = ?";
            $params[] = $filters['supplier_id'];
        }
        if (!empty($filters['date_from'])) {
            $where .= " AND p.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where .= " AND p.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as t FROM purchases p JOIN suppliers s ON p.supplier_id=s.id WHERE {$where}",
            $params
        )['t'];

        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $purchases = $this->db->fetchAll(
            "SELECT p.*, s.company_name as supplier_name, u.name as created_by_name
             FROM purchases p
             JOIN suppliers s ON p.supplier_id = s.id
             LEFT JOIN users u ON p.created_by = u.id
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['purchases' => $purchases, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        $purchase = $this->db->fetch(
            "SELECT p.*, s.company_name as supplier_name, s.contact_person, s.phone as supplier_phone,
                    u.name as created_by_name
             FROM purchases p
             JOIN suppliers s ON p.supplier_id = s.id
             LEFT JOIN users u ON p.created_by = u.id
             WHERE p.id = ?", [$id]
        );
        if (!$purchase) return null;

        $items = $this->db->fetchAll(
            "SELECT pi.*, pr.name as product_name, pr.sku, u.symbol as unit_symbol
             FROM purchase_items pi
             JOIN products pr ON pi.product_id = pr.id
             LEFT JOIN units u ON pr.unit_id = u.id
             WHERE pi.purchase_id = ?", [$id]
        );

        $payments = $this->db->fetchAll(
            "SELECT pp.*, u.name as paid_by_name
             FROM purchase_payments pp
             LEFT JOIN users u ON pp.paid_by = u.id
             WHERE pp.purchase_id = ? ORDER BY pp.created_at DESC", [$id]
        );

        return ['purchase' => $purchase, 'items' => $items, 'payments' => $payments];
    }

    public function create($data) {
        $poNumber = generatePONumber();

        $purchaseId = $this->db->insert('purchases', [
            'supplier_id' => $data['supplier_id'],
            'po_number' => $poNumber,
            'status' => 'draft',
            'notes' => sanitize($data['notes'] ?? ''),
            'created_by' => $_SESSION['user_id'] ?? null,
            'branch_id' => $_SESSION['user_branch'] ?? 1,
        ]);

        $subtotal = 0;
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $lineTotal = (int)$item['qty'] * (float)$item['unit_cost'];
                $subtotal += $lineTotal;
                $this->db->insert('purchase_items', [
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product_id'],
                    'qty' => (int)$item['qty'],
                    'received_qty' => 0,
                    'unit_cost' => (float)$item['unit_cost'],
                    'total' => $lineTotal,
                ]);
            }
        }

        $taxRate = (float)($this->db->fetch("SELECT setting_value FROM settings WHERE setting_key='tax_rate'")['setting_value'] ?? 0);
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        $this->db->update('purchases', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'due' => $total,
        ], 'id = ?', [$purchaseId]);

        // Optional: apply the supplier's running balance to the new PO
        if (!empty($data['apply_supplier_balance'])) {
            $this->applySupplierBalance($purchaseId);
        }

        logActivity('purchase_created', 'purchases', $purchaseId, null, ['po_number' => $poNumber]);
        return ['id' => $purchaseId, 'po_number' => $poNumber];
    }

    public function update($id, $data) {
        $purchase = $this->db->fetch("SELECT status FROM purchases WHERE id=?", [$id]);
        if (!$purchase) return ['success' => false, 'message' => 'Not found'];
        if (!in_array($purchase['status'], ['draft', 'pending'])) {
            return ['success' => false, 'message' => 'Cannot edit approved/received orders'];
        }

        if (isset($data['supplier_id'])) {
            $this->db->update('purchases', ['supplier_id' => $data['supplier_id']], 'id=?', [$id]);
        }
        if (isset($data['notes'])) {
            $this->db->update('purchases', ['notes' => sanitize($data['notes'])], 'id=?', [$id]);
        }

        if (!empty($data['items'])) {
            $this->db->delete('purchase_items', 'purchase_id = ?', [$id]);
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $lineTotal = (int)$item['qty'] * (float)$item['unit_cost'];
                $subtotal += $lineTotal;
                $this->db->insert('purchase_items', [
                    'purchase_id' => $id,
                    'product_id' => $item['product_id'],
                    'qty' => (int)$item['qty'],
                    'received_qty' => 0,
                    'unit_cost' => (float)$item['unit_cost'],
                    'total' => $lineTotal,
                ]);
            }
            $taxRate = (float)($this->db->fetch("SELECT setting_value FROM settings WHERE setting_key='tax_rate'")['setting_value'] ?? 0);
            $tax = $subtotal * ($taxRate / 100);
            $total = $subtotal + $tax;
            $this->db->update('purchases', ['subtotal' => $subtotal, 'tax' => $tax, 'total' => $total, 'due' => $total - ($data['paid'] ?? 0)], 'id=?', [$id]);
        }

        logActivity('purchase_updated', 'purchases', $id);
        return ['success' => true];
    }

    public function updateStatus($id, $status) {
        $purchase = $this->db->fetch("SELECT * FROM purchases WHERE id=?", [$id]);
        if (!$purchase) return ['success' => false, 'message' => 'Not found'];

        $validTransitions = [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['approved', 'cancelled'],
            'approved' => ['received', 'cancelled'],
        ];

        if (!isset($validTransitions[$purchase['status']]) || !in_array($status, $validTransitions[$purchase['status']])) {
            return ['success' => false, 'message' => 'Invalid status transition'];
        }

        $this->db->update('purchases', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], 'id=?', [$id]);
        logActivity("purchase_{$status}", 'purchases', $id);

        if ($status === 'received') {
            $this->receiveStock($id);
        }

        return ['success' => true];
    }

    public function receiveStock($purchaseId) {
        $items = $this->db->fetchAll("SELECT * FROM purchase_items WHERE purchase_id = ?", [$purchaseId]);
        $branchId = $this->db->fetch("SELECT branch_id FROM purchases WHERE id = ?", [$purchaseId])['branch_id'] ?? 1;
        $inventory = new Inventory();

        foreach ($items as $item) {
            $qty = (int)$item['qty'];
            $inventory->stockIn($item['product_id'], $qty, $branchId, $purchaseId, 'purchase', "PO received");
            $this->db->update('purchase_items', ['received_qty' => $qty], 'id = ?', [$item['id']]);
        }

        logActivity('purchase_stock_received', 'purchases', $purchaseId);
    }

    public function addPayment($purchaseId, $amount, $method = 'cash', $reference = '', $notes = '') {
        $purchase = $this->db->fetch("SELECT * FROM purchases WHERE id=?", [$purchaseId]);
        if (!$purchase) return ['success' => false, 'message' => 'Not found'];

        $this->db->insert('purchase_payments', [
            'purchase_id' => $purchaseId,
            'amount' => (float)$amount,
            'payment_method' => $method,
            'reference' => $reference,
            'notes' => sanitize($notes),
            'paid_by' => $_SESSION['user_id'] ?? null,
        ]);

        $newPaid = (float)$purchase['paid'] + (float)$amount;
        $newDue = (float)$purchase['total'] - $newPaid;
        $payStatus = $newDue <= 0 ? 'paid' : ($newPaid > 0 ? 'partial' : 'pending');

        $this->db->update('purchases', [
            'paid' => $newPaid,
            'due' => max(0, $newDue),
            'payment_status' => $payStatus,
        ], 'id = ?', [$purchaseId]);

        logActivity('purchase_payment_added', 'purchases', $purchaseId, null, ['amount' => $amount]);
        return ['success' => true];
    }

    public function cancel($id) {
        return $this->updateStatus($id, 'cancelled');
    }

    public function getMonthlyReport($year = null) {
        $year = $year ?? date('Y');
        return $this->db->fetchAll(
            "SELECT MONTH(created_at) as month, COUNT(*) as count, SUM(total) as total, SUM(paid) as paid, SUM(due) as due
             FROM purchases WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at) ORDER BY month",
            [$year]
        );
    }

    /**
     * Reduce a PO's due by the supplier's balance (which THEN reduces the
     * balance), so existing credit keeps flowing back into purchases.
     */
    public function applySupplierBalance($purchaseId) {
        $purchase = $this->db->fetch("SELECT * FROM purchases WHERE id = ?", [$purchaseId]);
        if (!$purchase || (float)$purchase['due'] <= 0) return;

        $supplier = new Supplier();
        $balance = $supplier->getBalance((int)$purchase['supplier_id']);
        if ($balance <= 0) return;

        $applied = round(min($balance, (float)$purchase['due']), 2);
        $newDue = round((float)$purchase['due'] - $applied, 2);

        $this->db->update('purchases', [
            'due' => max(0, $newDue),
            'payment_status' => $newDue <= 0 ? 'paid' : 'pending',
        ], 'id = ?', [$purchaseId]);

        $supplier->deductBalance((int)$purchase['supplier_id'], $applied);
        logActivity('purchase_supplier_balance_applied', 'purchases', $purchaseId, null, ['amount' => $applied]);
    }
}
