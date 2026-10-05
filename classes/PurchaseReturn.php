<?php
/**
 * Phase 14: Purchase (Supplier) Returns & Credits.
 *
 * A supplier return references a RECEIVED purchase order and reverses
 * part of it. It is a document, never a purchase mutation: the PO keeps
 * its receipts and payment history, and only the settlement columns
 * (`due`, `payment_status`) are derived from the return.
 *
 * Lifecycle:
 *   requested -> approved -> received -> credited
 *   requested -> rejected
 *   approved / received / credited -> cancelled (void)
 *
 * 'received' is the single inventory moment: goods leave via a
 * 'purchase_return' stock-out. 'credited' settles purchases.due first
 * (when the credit-note policy is on) and overflows to suppliers.balance.
 */
class PurchaseReturn {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Policy value from the settings table, with a safe default. */
    private function setting($key, $default) {
        $row = $this->db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $row['setting_value'] ?? $default;
    }

    /** Branch managers only ever see their own branch. */
    private function scopeBranch($filters) {
        $role = $_SESSION['user_role'] ?? '';
        if (in_array($role, ['admin', 'manager'], true)) {
            return empty($filters['branch_id']) ? null : (int)$filters['branch_id'];
        }
        return (int)($_SESSION['user_branch'] ?? 1);
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (r.return_no LIKE ? OR p.po_number LIKE ? OR s.company_name LIKE ?)";
            $s = "%{$filters['search']}%";
            array_push($params, $s, $s, $s);
        }
        if (!empty($filters['status']))      { $where .= " AND r.status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['supplier_id'])) { $where .= " AND r.supplier_id = ?"; $params[] = (int)$filters['supplier_id']; }
        if (!empty($filters['reason_code'])) { $where .= " AND r.reason_code = ?"; $params[] = $filters['reason_code']; }
        if (!empty($filters['date_from']))   { $where .= " AND r.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to']))     { $where .= " AND r.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }

        $branch = $this->scopeBranch($filters);
        if ($branch) { $where .= " AND r.branch_id = ?"; $params[] = $branch; }

        $from = "FROM purchase_returns r
                 LEFT JOIN purchases p  ON r.purchase_id = p.id
                 LEFT JOIN suppliers s  ON r.supplier_id = s.id
                 LEFT JOIN branches b   ON r.branch_id = b.id
                 WHERE {$where}";

        $total = (int)$this->db->fetch("SELECT COUNT(*) as t {$from}", $params)['t'];
        $pagination = paginate($total, 20, (int)($filters['page'] ?? 1));

        $returns = $this->db->fetchAll(
            "SELECT r.*, p.po_number, s.company_name as supplier_name, b.name as branch_name,
                    (SELECT COALESCE(SUM(ri.qty), 0) FROM purchase_return_items ri
                     WHERE ri.return_id = r.id) as total_qty
             {$from}
             ORDER BY r.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['returns' => $returns, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        $r = $this->db->fetch(
            "SELECT r.*, p.po_number, p.total as purchase_total, p.due as purchase_due,
                    p.subtotal as purchase_subtotal, p.status as purchase_status,
                    p.payment_status as purchase_payment_status, p.paid as purchase_paid,
                    s.company_name as supplier_name, s.phone as supplier_phone, s.balance as supplier_balance,
                    b.name as branch_name,
                    req.name as requested_by_name, ap.name as approved_by_name,
                    rc.name as received_by_name, cr.name as credited_by_name
             FROM purchase_returns r
             LEFT JOIN purchases p  ON r.purchase_id = p.id
             LEFT JOIN suppliers s  ON r.supplier_id = s.id
             LEFT JOIN branches b   ON r.branch_id = b.id
             LEFT JOIN users req    ON r.requested_by = req.id
             LEFT JOIN users ap     ON r.approved_by = ap.id
             LEFT JOIN users rc     ON r.received_by = rc.id
             LEFT JOIN users cr     ON r.credited_by = cr.id
             WHERE r.id = ?", [$id]
        );
        if (!$r) return null;

        // Staff restricted to their own branch can only open their own returns.
        $role = $_SESSION['user_role'] ?? '';
        if (in_array($role, ['branch_manager'], true)) {
            $userBranch = (int)($_SESSION['user_branch'] ?? 1);
            if ((int)$r['branch_id'] !== $userBranch) return null;
        }

        $items = $this->db->fetchAll(
            "SELECT ri.*, p.name as product_name, p.sku, p.image, u.symbol as unit_symbol,
                    pi.unit_cost as source_cost, pi.received_qty, pi.returned_qty
             FROM purchase_return_items ri
             JOIN products p ON ri.product_id = p.id
             LEFT JOIN units u ON p.unit_id = u.id
             LEFT JOIN purchase_items pi ON ri.purchase_item_id = pi.id
             WHERE ri.return_id = ?
             ORDER BY ri.id", [$id]
        );

        $payments = $this->db->fetchAll(
            "SELECT pp.*, u.name as added_by_name
             FROM purchase_return_payments pp
             LEFT JOIN users u ON pp.added_by = u.id
             WHERE pp.return_id = ? ORDER BY pp.id", [$id]
        );

        $photos = (new ReturnPhoto())->getFor('purchase', $id);

        return ['return' => $r, 'items' => $items, 'payments' => $payments, 'photos' => $photos];
    }

    /**
     * Remaining returnable quantity and value for every line of a received PO.
     *
     * Only received purchases can be returned. The tally includes RMAs in
     * any state except 'cancelled', so an open return already blocks a
     * second return for the same units before the goods are even dispatched.
     */
    public function getReturnableForPurchase($purchaseId) {
        $purchase = $this->db->fetch("SELECT * FROM purchases WHERE id = ?", [$purchaseId]);
        if (!$purchase) return null;

        $tally = $this->db->fetchAll(
            "SELECT ri.purchase_item_id, COALESCE(SUM(ri.qty), 0) as returned_qty
             FROM purchase_return_items ri
             JOIN purchase_returns r ON r.id = ri.return_id
             WHERE r.purchase_id = ? AND r.status <> 'cancelled'
             GROUP BY ri.purchase_item_id", [$purchaseId]
        );
        $returnedMap = [];
        foreach ($tally as $row) {
            $returnedMap[(int)$row['purchase_item_id']] = (int)$row['returned_qty'];
        }

        $lines = $this->db->fetchAll(
            "SELECT pi.id, pi.product_id, pi.qty, pi.received_qty, pi.unit_cost, pi.total,
                    p.name as product_name, p.sku, p.image, u.symbol as unit_symbol
             FROM purchase_items pi
             JOIN products p ON pi.product_id = p.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE pi.purchase_id = ?
             ORDER BY pi.id", [$purchaseId]
        );

        $items = [];
        $returnableSubtotal = 0.0;
        foreach ($lines as $it) {
            $received = (int)$it['received_qty'];
            $already = $returnedMap[(int)$it['id']] ?? 0;
            $remaining = max(0, $received - $already);
            $it['returnable_qty'] = $remaining;
            $it['already_returned_qty'] = $already;
            $it['returnable_amount'] = round($remaining * (float)$it['unit_cost'], 2);
            $returnableSubtotal += $it['returnable_amount'];
            $items[] = $it;
        }
        $returnableSubtotal = round($returnableSubtotal, 2);

        $blockers = [];
        if (!in_array($purchase['status'], ['received'], true)) {
            $blockers[] = 'Only a received purchase order can be returned';
        }
        if ($returnableSubtotal <= 0) {
            $blockers[] = 'Nothing left to return';
        }

        return [
            'purchase' => $purchase,
            'items' => $items,
            'returnable_subtotal' => $returnableSubtotal,
            'returnable' => $purchase['status'] === 'received' && $returnableSubtotal > 0,
            'blockers' => $blockers,
        ];
    }

    /**
     * Credit on a supplier return = returned line value + the proportional
     * share of the purchase's tax. Clamped so cumulative credits (all live
     * RMAs) never exceed the purchase total.
     *
     *     credit = R + tax*(R/S)
     *
     * where R = returned line value and S = original purchase subtotal.
     */
    public function computeCredit($purchase, $returnedLineValue, $alreadyCredited = 0.0) {
        $purchaseSubtotal = (float)$purchase['subtotal'];
        $ratio = $purchaseSubtotal > 0 ? $returnedLineValue / $purchaseSubtotal : 0.0;
        $ratio = max(0.0, min(1.0, $ratio));

        $taxShare = round((float)$purchase['tax'] * $ratio, 2);
        $credit = round($returnedLineValue + $taxShare, 2);

        $ceiling = round((float)$purchase['total'] - (float)$alreadyCredited, 2);
        $credit = min(max(0.0, $credit), max(0.0, $ceiling));

        return [
            'returned_line_value' => round($returnedLineValue, 2),
            'tax' => $taxShare,
            'credit_total' => $credit,
            'ratio' => $ratio,
        ];
    }

    /** Credit already settled against a PO (all live returns on it). */
    private function alreadyCredited($purchaseId) {
        $row = $this->db->fetch(
            "SELECT COALESCE(SUM(credit_total), 0) as t
             FROM purchase_returns WHERE purchase_id = ? AND status IN ('credited','received')",
            [$purchaseId]
        );
        return (float)$row['t'];
    }

    /**
     * Create a return document. Client-supplied prices are never trusted:
     * only line ids and quantities arrive from the browser; unit_cost is
     * read from the original purchase line.
     */
    public function create($data) {
        $purchaseId = (int)($data['purchase_id'] ?? 0);
        $items      = $data['items'] ?? null;
        $reasonCode = $data['reason_code'] ?? '';

        if (!$purchaseId) return ['success' => false, 'message' => 'Purchase id is required'];
        if (!is_array($items) || !$items) return ['success' => false, 'message' => 'Select at least one item to return'];
        if (!$reasonCode) return ['success' => false, 'message' => 'A return reason is required'];

        $role = $_SESSION['user_role'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $purchase = $this->db->fetch("SELECT * FROM purchases WHERE id = ?", [$purchaseId]);
            if (!$purchase) throw new \Exception('Purchase order not found');
            if ($purchase['status'] !== 'received') {
                throw new \Exception('Only a received purchase order can be returned');
            }
            $branchId = (int)$purchase['branch_id'];

            // Authoritative returnable lines (received qty minus prior returns)
            $lines = $this->db->fetchAll(
                "SELECT * FROM purchase_items WHERE purchase_id = ?", [$purchaseId]
            );
            $lineMap = [];
            foreach ($lines as $l) $lineMap[(int)$l['id']] = $l;

            $returnedMap = $this->db->fetchAll(
                "SELECT ri.purchase_item_id as line_id, COALESCE(SUM(ri.qty),0) as qty
                 FROM purchase_return_items ri
                 JOIN purchase_returns r ON r.id = ri.return_id
                 WHERE r.status <> 'cancelled' AND r.purchase_id = ?
                 GROUP BY ri.purchase_item_id", [$purchaseId]
            );
            $already = [];
            foreach ($returnedMap as $row) $already[(int)$row['line_id']] = (int)$row['qty'];

            $accepted = [];
            $returnedValue = 0.0;
            foreach ($items as $item) {
                $lineId = (int)($item['purchase_item_id'] ?? $item['id'] ?? 0);
                if (!isset($lineMap[$lineId])) throw new \Exception("Line $lineId is not part of this PO");

                $line = $lineMap[$lineId];
                $qty  = (int)($item['qty'] ?? 0);
                if ($qty <= 0) continue;

                $received   = (int)$line['received_qty'];
                $remaining  = $received - ($already[$lineId] ?? 0);
                if ($qty > $remaining) {
                    throw new \Exception(sprintf(
                        'Line %s: only %d unit(s) returnable (requested %d)',
                        $line['product_id'], max(0, $remaining), $qty
                    ));
                }

                $lineTotal = round($qty * (float)$line['unit_cost'], 2);
                $already[$lineId] = ($already[$lineId] ?? 0) + $qty;

                $accepted[] = [
                    'line_id'   => $lineId,
                    'product_id'=> (int)$line['product_id'],
                    'qty'       => $qty,
                    'unit_cost' => (float)$line['unit_cost'],
                    'total'     => $lineTotal,
                ];
                $returnedValue += $lineTotal;
            }
            if (!$accepted) throw new \Exception('Select at least one item to return');

            $money = $this->computeCredit($purchase, $returnedValue, $this->alreadyCredited($purchaseId));

            $requireApproval = (int)$this->setting('purchase_return_require_approval', 1);
            $status = ($role === 'branch_manager' && $requireApproval === 1) ? 'requested' : 'approved';

            $returnNo = generateReturnNo('purchase');
            $returnId = $this->db->insert('purchase_returns', [
                'return_no'    => $returnNo,
                'purchase_id'  => $purchaseId,
                'supplier_id'  => (int)$purchase['supplier_id'],
                'branch_id'    => $branchId,
                'status'       => $status,
                'reason_code'  => $reasonCode,
                'reason'       => sanitize($data['reason'] ?? ''),
                'method'       => $data['method'] ?? 'credit_note',
                'subtotal'     => $money['returned_line_value'],
                'tax'          => $money['tax'],
                'credit_total' => $money['credit_total'],
                'requested_by' => $userId,
                'admin_note'   => sanitize($data['note'] ?? ''),
            ]);

            foreach ($accepted as $a) {
                $this->db->insert('purchase_return_items', [
                    'return_id'        => $returnId,
                    'purchase_item_id' => $a['line_id'],
                    'product_id'       => $a['product_id'],
                    'qty'              => $a['qty'],
                    'unit_cost'        => $a['unit_cost'],
                    'total'            => $a['total'],
                ]);
            }

            // Photo evidence (multipart upload from the same request)
            if (!empty($_FILES['photos'])) {
                (new ReturnPhoto())->addMany('purchase', $returnId, null, $_FILES['photos'], $userId);
            }

            if ($status === 'requested') {
                (new Notification())->createForRole(
                    'Supplier return awaiting approval',
                    "{$returnNo} is awaiting review",
                    ['admin', 'manager', 'branch_manager'],
                    'warning',
                    APP_URL . '/pages/purchase-returns/view.php?id=' . $returnId
                );
            }

            logActivity('purchase_return_created', 'purchase_returns', $returnId, null, [
                'return_no'    => $returnNo,
                'credit_total' => $money['credit_total'],
                'status'       => $status,
            ]);

            $conn->commit();
            return ['success' => true, 'id' => $returnId, 'return_no' => $returnNo, 'credit_total' => $money['credit_total']];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function approve($id, $note = '') {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if ($r['return']['status'] !== 'requested') {
            return ['success' => false, 'message' => 'Only a requested return can be approved'];
        }

        $this->db->update('purchase_returns', [
            'status'      => 'approved',
            'approved_by' => $_SESSION['user_id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_note'  => sanitize($note),
        ], 'id = ?', [$id]);

        logActivity('purchase_return_approved', 'purchase_returns', $id, ['status' => 'requested'], ['status' => 'approved']);
        return ['success' => true];
    }

    public function reject($id, $note = '') {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if ($r['return']['status'] !== 'requested') {
            return ['success' => false, 'message' => 'Only a requested return can be rejected'];
        }
        if (trim((string)$note) === '') {
            return ['success' => false, 'message' => 'A reason is required when rejecting a request'];
        }

        $this->db->update('purchase_returns', [
            'status'      => 'rejected',
            'approved_by' => $_SESSION['user_id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_note'  => sanitize($note),
        ], 'id = ?', [$id]);

        // Rejected requests hold no stock and no money, so nothing to undo.
        logActivity('purchase_return_rejected', 'purchase_returns', $id, ['status' => 'requested'], ['status' => 'rejected']);
        return ['success' => true];
    }

    /**
     * "Mark Shipped Back" — the physical moment. Every line ships to the
     * supplier: one 'purchase_return' stock-out per line, condition note
     * recorded, and the PO's cached returned_qty kept in step.
     */
    public function receive($id, $decisions = []) {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if (!in_array($r['return']['status'], ['approved'], true)) {
            return ['success' => false, 'message' => 'Only an approved return can be shipped back'];
        }

        $map = [];
        foreach ($decisions as $d) $map[(int)($d['return_item_id'] ?? 0)] = $d;

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $inventory = new Inventory();
            $shippedQty = 0;

            foreach ($r['items'] as $it) {
                $d = $map[(int)$it['id']] ?? [];
                $note = sanitize($d['condition_note'] ?? $it['condition_note'] ?? '');

                $this->db->update('purchase_return_items', [
                    'condition_note' => $note ?: null,
                ], 'id = ?', [$it['id']]);

                // Goods leave our stock — this is the single stock_logs row
                $inventory->recordPurchaseReturn(
                    (int)$it['product_id'],
                    (int)$it['qty'],
                    (int)$r['return']['branch_id'],
                    $id,
                    "Return {$r['return']['return_no']} to supplier"
                );
                $shippedQty += (int)$it['qty'];

                // Keep the cached PO counter in step with the ledger
                $this->db->query(
                    "UPDATE purchase_items SET returned_qty = returned_qty + ? WHERE id = ?",
                    [(int)$it['qty'], (int)$it['purchase_item_id']]
                );
            }

            $this->db->update('purchase_returns', [
                'status'      => 'received',
                'received_by' => $_SESSION['user_id'] ?? null,
                'received_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$id]);

            logActivity('purchase_return_received', 'purchase_returns', $id, ['status' => 'approved'], [
                'status' => 'received', 'shipped_qty' => $shippedQty
            ]);

            $conn->commit();
            return ['success' => true, 'shipped_qty' => $shippedQty];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Settle the supplier's credit. When the credit-note policy is on, the
     * credit applies against what we still owe on the PO first and only the
     * overflow parks on the supplier's balance; when it is off, the whole
     * credit goes to balance. The single ledger row records HOW it settled.
     */
    public function credit($id, $options = []) {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if (!in_array($r['return']['status'], ['received'], true)) {
            return ['success' => false, 'message' => 'Ship the goods back before crediting'];
        }

        $ret = $r['return'];
        $method = $options['method'] ?? $ret['method'];
        if (!in_array($method, ['credit_note', 'bank_transfer', 'cash'], true)) {
            return ['success' => false, 'message' => 'Invalid credit method'];
        }

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $creditTotal = (float)$ret['credit_total'];

            // 1) First applied against what we still owe on this PO
            $due = (float)$ret['purchase_due'];
            $creditDueFirst = (int)$this->setting('purchase_return_credit_note', 1) === 1;
            $appliedToDue = $creditDueFirst ? round(min($creditTotal, $due), 2) : 0.0;
            $toBalance = round($creditTotal - $appliedToDue, 2);

            if ($appliedToDue > 0) {
                $newDue = round($due - $appliedToDue, 2);
                $purchase = $this->db->fetch("SELECT paid, total FROM purchases WHERE id = ?", [$ret['purchase_id']]);
                $payStatus = $newDue <= 0 ? 'paid' : ((float)$purchase['paid'] > 0 ? 'partial' : 'pending');
                $this->db->update('purchases', [
                    'due' => max(0, $newDue),
                    'payment_status' => $payStatus,
                ], 'id = ?', [$ret['purchase_id']]);
            }

            // 2) Anything beyond the due balance becomes supplier credit
            if ($toBalance > 0) {
                (new Supplier())->addBalance((int)$ret['supplier_id'], $toBalance);
            }

            $this->db->insert('purchase_return_payments', [
                'return_id' => $id,
                'amount'    => $creditTotal,
                'method'    => $method,
                'reference' => sanitize($options['reference'] ?? ''),
                'notes'     => sanitize($options['notes'] ?? ''),
                'added_by'  => $_SESSION['user_id'] ?? null,
            ]);

            $this->db->update('purchase_returns', [
                'status'         => 'credited',
                'method'         => $method,
                'applied_to_due' => $appliedToDue,
                'to_balance'     => $toBalance,
                'credited_by'    => $_SESSION['user_id'] ?? null,
                'credited_at'    => date('Y-m-d H:i:s'),
            ], 'id = ?', [$id]);

            logActivity('purchase_return_credited', 'purchase_returns', $id, ['status' => 'received'], [
                'status' => 'credited', 'amount' => $creditTotal,
                'applied_to_due' => $appliedToDue, 'to_balance' => $toBalance, 'method' => $method
            ]);

            $conn->commit();
            return ['success' => true, 'amount' => $creditTotal, 'applied_to_due' => $appliedToDue, 'to_balance' => $toBalance];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Void is a reversal, never a delete. It puts the stock back in,
     * unwinds the due/balance settlement, frees the PO lines for a fresh
     * return, and leaves the cancelled RMA as history.
     */
    public function void($id, $reason = '') {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        $ret = $r['return'];
        if (!in_array($ret['status'], ['approved', 'received', 'credited'], true)) {
            return ['success' => false, 'message' => 'This return cannot be voided'];
        }

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            // Unwind stock that already left the building
            if (in_array($ret['status'], ['received', 'credited'], true)) {
                $inventory = new Inventory();
                foreach ($r['items'] as $it) {
                    $inventory->stockIn(
                        (int)$it['product_id'],
                        (int)$it['qty'],
                        (int)$ret['branch_id'],
                        $id,
                        'purchase_return_void',
                        "Voided return {$ret['return_no']}: " . $reason
                    );
                    $this->db->query(
                        "UPDATE purchase_items SET returned_qty = GREATEST(0, returned_qty - ?) WHERE id = ?",
                        [(int)$it['qty'], (int)$it['purchase_item_id']]
                    );
                }
            }

            // Unwind a settlement already applied
            if ($ret['status'] === 'credited') {
                if ((float)$ret['applied_to_due'] > 0) {
                    $purchase = $this->db->fetch("SELECT total, paid FROM purchases WHERE id = ?", [$ret['purchase_id']]);
                    $due = round(min((float)$purchase['total'] - (float)$purchase['paid'], (float)$ret['purchase_due'] + (float)$ret['applied_to_due']), 2);
                    $this->db->update('purchases', [
                        'due' => max(0, $due),
                        'payment_status' => $due <= 0 ? 'paid' : ((float)$purchase['paid'] > 0 ? 'partial' : 'pending'),
                    ], 'id = ?', [$ret['purchase_id']]);
                }
                if ((float)$ret['to_balance'] > 0) {
                    (new Supplier())->deductBalance((int)$ret['supplier_id'], (float)$ret['to_balance']);
                }
            }

            $this->db->update('purchase_returns', [
                'status'     => 'cancelled',
                'admin_note' => 'VOIDED: ' . sanitize($reason),
            ], 'id = ?', [$id]);

            logActivity('purchase_return_voided', 'purchase_returns', $id, ['status' => $ret['status']], [
                'status' => 'cancelled', 'reason' => $reason
            ]);

            $conn->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}