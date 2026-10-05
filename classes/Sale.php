<?php
class Sale {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (s.invoice_no LIKE ? OR c.name LIKE ?)";
            $s = "%{$filters['search']}%";
            $params[] = $s;
            $params[] = $s;
        }
        if (!empty($filters['status'])) {
            $where .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['customer_id'])) {
            $where .= " AND s.customer_id = ?";
            $params[] = $filters['customer_id'];
        }
        if (!empty($filters['date_from'])) {
            $where .= " AND s.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where .= " AND s.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        if (!empty($filters['branch_id'])) {
            $where .= " AND s.branch_id = ?";
            $params[] = $filters['branch_id'];
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as t FROM sales s LEFT JOIN customers c ON s.customer_id=c.id WHERE {$where}",
            $params
        )['t'];

        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $sales = $this->db->fetchAll(
            "SELECT s.*, c.name as customer_name, c.phone as customer_phone,
                    u.name as cashier_name, b.name as branch_name
             FROM sales s
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN users u ON s.created_by = u.id
             LEFT JOIN branches b ON s.branch_id = b.id
             WHERE {$where}
             ORDER BY s.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['sales' => $sales, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        $sale = $this->db->fetch(
            "SELECT s.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                    u.name as cashier_name, b.name as branch_name
             FROM sales s
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN users u ON s.created_by = u.id
             LEFT JOIN branches b ON s.branch_id = b.id
             WHERE s.id = ?", [$id]
        );
        if (!$sale) return null;

        // Ensure exchange_return_id is always present in the sale object
        if (!isset($sale['exchange_return_id'])) {
            $sale['exchange_return_id'] = null;
        }
        if (!$sale) return null;

        $items = $this->db->fetchAll(
            "SELECT si.*, pr.name as product_name, pr.sku, pr.image, u.symbol as unit_symbol
             FROM sale_items si
             JOIN products pr ON si.product_id = pr.id
             LEFT JOIN units u ON pr.unit_id = u.id
             WHERE si.sale_id = ?", [$id]
        );

        $payments = $this->db->fetchAll(
            "SELECT * FROM sale_payments WHERE sale_id = ? ORDER BY created_at", [$id]
        );

        $returnable = (new SaleReturn())->getReturnableForSale($id);

        return [
            'sale' => $sale,
            'items' => $items,
            'payments' => $payments,
            'returns' => $this->db->fetchAll(
                "SELECT id, return_no, status, refund_total, created_at
                 FROM sale_returns WHERE sale_id = ? ORDER BY id", [$id]
            ),
            'returnable_items' => $returnable['items'] ?? [],
            'returnable' => $returnable['returnable'] ?? false,
            'return_blockers' => $returnable['blockers'] ?? [],
            'return_window_days' => $returnable['window_days'] ?? 0,
            // Phase 15: exchange affordance drives the POS / receipt / Returns
            // buttons. "Active" just means the sale is not a zero-total book
            // entry; the modal itself re-checks on the live return document.
            'exchange' => [
                'allowed'  => $returnable['returnable'] ?? false,
                'active'   => (float)$sale['grand_total'] > 0,
                'blockers' => $returnable['blockers'] ?? [],
            ],
        ];
    }

    /**
     * Create a sale. `$exchangeReturnId` optionally links the new sale to the
     * exchange return that funded it (sales.exchange_return_id); the reverse
     * link lives on sale_returns.exchange_sale_id. `branch_id` may be passed
     * in the data (exchange replacement sales must be booked on the branch
     * the returned goods restocked to); it otherwise falls back to the
     * operator's branch.
     */
    public function create($data, $exchangeReturnId = null) {
        $invoiceNo = generateInvoiceNo();
        $branchId = !empty($data['branch_id']) ? (int)$data['branch_id'] : ($_SESSION['user_branch'] ?? 1);

        $this->db->beginTransaction();
        try {
        $saleId = $this->db->insert('sales', [
            'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : null,
            'invoice_no' => $invoiceNo,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'discount' => (float)($data['discount'] ?? 0),
            'shipping' => (float)($data['shipping'] ?? 0),
            'notes' => sanitize($data['notes'] ?? ''),
            'status' => 'completed',
            'created_by' => $_SESSION['user_id'] ?? null,
            'branch_id' => $branchId,
            'exchange_return_id' => $exchangeReturnId ? (int)$exchangeReturnId : null,
        ]);

        $subtotal = 0;
        $inventory = new Inventory();

        foreach ($data['items'] as $item) {
            $lineTotal = (int)$item['qty'] * (float)$item['unit_price'];
            $itemDiscount = (float)($item['discount'] ?? 0);
            $lineTotal -= $itemDiscount;

            $this->db->insert('sale_items', [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'qty' => (int)$item['qty'],
                'unit_price' => (float)$item['unit_price'],
                'discount' => $itemDiscount,
                'total' => $lineTotal,
            ]);

            $subtotal += $lineTotal;

            $result = $inventory->stockOut($item['product_id'], (int)$item['qty'], $branchId, $saleId, 'sale', "Sale #{$invoiceNo}");
            if (is_array($result) && !$result['success']) {
                throw new Exception($result['message']);
            }
        }

        $taxRate = (float)($this->db->fetch("SELECT setting_value FROM settings WHERE setting_key='tax_rate'")['setting_value'] ?? 0);
        $discount = (float)($data['discount'] ?? 0);
        $shipping = (float)($data['shipping'] ?? 0);
        $tax = ($subtotal - $discount) * ($taxRate / 100);
        $grandTotal = $subtotal - $discount + $tax + $shipping;

        $totalPaid = 0;
        if (!empty($data['payments'])) {
            foreach ($data['payments'] as $pay) {
                $this->db->insert('sale_payments', [
                    'sale_id' => $saleId,
                    'amount' => (float)$pay['amount'],
                    'payment_method' => $pay['method'],
                    'reference' => $pay['reference'] ?? '',
                    'received_by' => $_SESSION['user_id'] ?? null,
                ]);
                $totalPaid += (float)$pay['amount'];
            }
        } else {
            $totalPaid = $grandTotal;
            $this->db->insert('sale_payments', [
                'sale_id' => $saleId,
                'amount' => $grandTotal,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'received_by' => $_SESSION['user_id'] ?? null,
            ]);
        }

        $this->db->update('sales', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'grand_total' => $grandTotal,
        ], 'id = ?', [$saleId]);

        if (!empty($data['customer_id']) && $grandTotal > 0) {
            $customer = new Customer();
            $points = (int)floor($grandTotal / 10);
            $customer->addLoyaltyPoints($data['customer_id'], $points);
        }

        $invoice = new Invoice();
        $invoice->create($saleId);

        logActivity('sale_created', 'sales', $saleId, null, ['invoice_no' => $invoiceNo, 'total' => $grandTotal]);

        $this->db->commit();
        return ['id' => $saleId, 'invoice_no' => $invoiceNo, 'grand_total' => $grandTotal];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function hold($id) {
        $this->db->update('sales', ['status' => 'held'], 'id = ?', [$id]);
        logActivity('sale_held', 'sales', $id);
    }

    public function resume($id) {
        $this->db->update('sales', ['status' => 'completed'], 'id = ?', [$id]);
        logActivity('sale_resumed', 'sales', $id);
    }

    public function cancel($id) {
        $sale = $this->db->fetch("SELECT * FROM sales WHERE id = ?", [$id]);
        if (!$sale) return ['success' => false, 'message' => 'Not found'];

        $items = $this->db->fetchAll("SELECT * FROM sale_items WHERE sale_id = ?", [$id]);
        $inventory = new Inventory();
        foreach ($items as $item) {
            $inventory->stockIn($item['product_id'], $item['qty'], $sale['branch_id'], $id, 'sale_cancelled', "Cancelled sale #{$sale['invoice_no']}");
        }

        $this->db->update('sales', ['status' => 'cancelled'], 'id = ?', [$id]);

        // An exchange sale points back at the return that funded it. Free
        // that link when the sale is cancelled so the return itself can then
        // be voided (SaleReturn::void() refuses while exchange_sale_id is
        // set, to stop an exchanged return being orphaned mid-flight).
        if (!empty($sale['exchange_return_id'])) {
            $this->db->update('sale_returns', ['exchange_sale_id' => null], 'id = ?', [(int)$sale['exchange_return_id']]);
        }

        logActivity('sale_cancelled', 'sales', $id);
        return ['success' => true];
    }

    public function getDailySales($date = null, $branchId = null) {
        $date = $date ?? date('Y-m-d');
        $branchId = $branchId ?? ($_SESSION['user_branch'] ?? null);
        $where = "DATE(s.created_at) = ? AND s.status = 'completed'";
        $params = [$date];
        if ($branchId) { $where .= " AND s.branch_id = ?"; $params[] = $branchId; }

        return $this->db->fetch(
            "SELECT COUNT(*) as count, IFNULL(SUM(grand_total),0) as total,
                    IFNULL(SUM(discount),0) as discounts, IFNULL(SUM(tax),0) as taxes
             FROM sales s WHERE {$where}", $params
        );
    }
}
