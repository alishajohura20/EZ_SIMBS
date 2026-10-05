<?php
class SaleReturn {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Policy value from the settings table, with a safe default. */
    private function setting($key, $default) {
        $row = $this->db->fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $row['setting_value'] ?? $default;
    }

    /** Branch managers and cashiers only ever see their own branch. */
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
            $where .= " AND (r.return_no LIKE ? OR s.invoice_no LIKE ? OR o.order_no LIKE ? OR c.name LIKE ?)";
            $s = "%{$filters['search']}%";
            array_push($params, $s, $s, $s, $s);
        }
        if (!empty($filters['sale_id'])) { $where .= " AND r.sale_id = ?"; $params[] = (int)$filters['sale_id']; }
        if (!empty($filters['status']))   { $where .= " AND r.status = ?";     $params[] = $filters['status']; }
        if (!empty($filters['type']))     { $where .= " AND r.type = ?";       $params[] = $filters['type']; }
        if (!empty($filters['reason_code'])) { $where .= " AND r.reason_code = ?"; $params[] = $filters['reason_code']; }
        if (!empty($filters['date_from'])) { $where .= " AND r.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to']))   { $where .= " AND r.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }

        $branch = $this->scopeBranch($filters);
        if ($branch) { $where .= " AND r.branch_id = ?"; $params[] = $branch; }

        $from = "FROM sale_returns r
                 LEFT JOIN sales s     ON r.sale_id = s.id
                 LEFT JOIN orders o    ON r.order_id = o.id
                 LEFT JOIN customers c ON r.customer_id = c.id
                 LEFT JOIN branches b  ON r.branch_id = b.id
                 WHERE {$where}";

        $total = (int)$this->db->fetch("SELECT COUNT(*) as t {$from}", $params)['t'];
        $pagination = paginate($total, 20, (int)($filters['page'] ?? 1));

        $returns = $this->db->fetchAll(
            "SELECT r.*, s.invoice_no, o.order_no, c.name as customer_name, b.name as branch_name,
                    (SELECT COALESCE(SUM(ri.qty), 0) FROM sale_return_items ri WHERE ri.return_id = r.id) as total_qty
             {$from}
             ORDER BY r.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['returns' => $returns, 'pagination' => $pagination, 'total' => $total];
    }

    public function getById($id) {
        $r = $this->db->fetch(
            "SELECT r.*, s.invoice_no, s.grand_total as sale_grand_total, s.payment_method as sale_payment_method,
                    s.subtotal as sale_subtotal, o.order_no, o.status as order_status,
                    c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                    b.name as branch_name,
                    req.name as requested_by_name, reqf.name as requested_for_name,
                    ap.name as approved_by_name, rc.name as received_by_name, rf.name as refunded_by_name
             FROM sale_returns r
             LEFT JOIN sales s     ON r.sale_id = s.id
             LEFT JOIN orders o    ON r.order_id = o.id
             LEFT JOIN customers c ON r.customer_id = c.id
             LEFT JOIN branches b  ON r.branch_id = b.id
             LEFT JOIN users req   ON r.requested_by = req.id
             LEFT JOIN users reqf  ON r.requested_for = reqf.id
             LEFT JOIN users ap    ON r.approved_by = ap.id
             LEFT JOIN users rc    ON r.received_by = rc.id
             LEFT JOIN users rf    ON r.refunded_by = rf.id
             WHERE r.id = ?", [$id]
        );
        if (!$r) return null;

        // Staff restricted to their own branch can only open their own RMAs.
        $role = $_SESSION['user_role'] ?? '';
        if (in_array($role, ['branch_manager', 'cashier'], true)) {
            $userBranch = (int)($_SESSION['user_branch'] ?? 1);
            if ((int)$r['branch_id'] !== $userBranch) return null;
        }

        $items = $this->db->fetchAll(
            "SELECT ri.*, p.name as product_name, p.sku, p.image, u.symbol as unit_symbol,
                    si.id as orig_line_id
             FROM sale_return_items ri
             JOIN products p ON ri.product_id = p.id
             LEFT JOIN units u ON p.unit_id = u.id
             LEFT JOIN sale_items si ON ri.sale_item_id = si.id
             WHERE ri.return_id = ?
             ORDER BY ri.id", [$id]
        );

        $payments = $this->db->fetchAll(
            "SELECT rp.*, u.name as processed_by_name
             FROM sale_return_payments rp
             LEFT JOIN users u ON rp.processed_by = u.id
             WHERE rp.return_id = ? ORDER BY rp.id", [$id]
        );

        $photos = (new ReturnPhoto())->getFor('sale', $id);

        return ['return' => $r, 'items' => $items, 'payments' => $payments, 'photos' => $photos];
    }

    /**
     * Remaining returnable quantity and value for every line of a sale.
     *
     * The returned_qty tally deliberately includes RMAs in *any* state
     * except 'cancelled', so a request already lodged against a line
     * blocks a second request for the same units before goods are
     * even received.
     */
    public function getReturnableForSale($saleId) {
        $sale = $this->db->fetch("SELECT * FROM sales WHERE id = ?", [$saleId]);
        if (!$sale) return null;

        $tally = $this->db->fetchAll(
            "SELECT ri.sale_item_id, COALESCE(SUM(ri.qty), 0) as returned_qty
             FROM sale_return_items ri
             JOIN sale_returns r ON r.id = ri.return_id
             WHERE r.sale_id = ? AND r.status <> 'cancelled'
             GROUP BY ri.sale_item_id", [$saleId]
        );
        $returnedMap = [];
        foreach ($tally as $row) {
            $returnedMap[(int)$row['sale_item_id']] = (int)$row['returned_qty'];
        }

        // si.qty > 0 excludes any legacy negative row written by the
        // Phase 07 Sale::processReturn() implementation.
        $lines = $this->db->fetchAll(
            "SELECT si.id, si.product_id, si.qty, si.unit_price, si.discount, si.total,
                    p.name as product_name, p.sku, p.image, p.status as product_status,
                    u.symbol as unit_symbol
             FROM sale_items si
             JOIN products p ON si.product_id = p.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE si.sale_id = ? AND si.qty > 0
             ORDER BY si.id", [$saleId]
        );

        $windowDays = (int)$this->setting('return_window_days', 14);
        $ageDays = (int)floor((time() - strtotime($sale['created_at'])) / 86400);
        $withinWindow = $ageDays <= $windowDays;

        $items = [];
        $returnableSubtotal = 0.0;
        foreach ($lines as $it) {
            $already = $returnedMap[(int)$it['id']] ?? 0;
            $remaining = max(0, (int)$it['qty'] - $already);
            $it['already_returned_qty'] = $already;
            $it['returnable_qty'] = $remaining;
            $it['returnable_amount'] = round($remaining * (float)$it['unit_price'] - (float)$it['discount'], 2);
            $returnableSubtotal += $it['returnable_amount'];
            $items[] = $it;
        }
        $returnableSubtotal = round($returnableSubtotal, 2);

        // Human-readable reasons the Return button is unavailable.
        $blockers = [];
        if ($sale['status'] === 'cancelled') {
            $blockers[] = 'Sale was cancelled';
        } elseif ($sale['status'] === 'held') {
            $blockers[] = 'Sale is on hold — resume or cancel it first';
        } elseif ($sale['status'] === 'returned') {
            $blockers[] = 'Sale has already been fully returned';
        } elseif ($sale['status'] === 'partially_returned') {
            $blockers[] = 'Partially returned — remaining lines may still be returnable';
        }
        if (!$withinWindow) {
            $blockers[] = "Outside the {$windowDays}-day return window ({$ageDays} days old)";
        }
        if ($returnableSubtotal <= 0) {
            $blockers[] = 'Nothing left to return';
        }

        return [
            'sale' => $sale,
            'items' => $items,
            'window_days' => $windowDays,
            'age_days' => $ageDays,
            'within_window' => $withinWindow,
            'returnable_subtotal' => $returnableSubtotal,
            'source_label' => 'In-store Sale · ' . ($sale['invoice_no'] ?? '#'.$saleId),
            'auth_hint' => in_array($_SESSION['user_role'] ?? '', ['admin', 'manager'], true)
                ? 'You may accept returns outside the window or for partially held lines.'
                : 'Cashiers can return against completed sales only; managers can override the window.',
            'returnable' => $returnableSubtotal > 0 && $withinWindow && !in_array($sale['status'], ['cancelled', 'held'], true),
            'blockers' => $blockers,
            'can_override_window' => in_array($_SESSION['user_role'] ?? '', ['admin', 'manager'], true),
        ];
    }

    /**
     * Proportional reversal of a sale's money columns.
     *
     * The returned line value is the base; discount, tax and shipping are
     * each reversed in proportion to how much of the sale subtotal is being
     * returned. This keeps the same arithmetic convention the rest of the
     * project uses (see the verification checks in ez_simbs_seed_data.sql).
     *
     *     refund = R - discount*(R/S) + tax*(R/S) + shipping*(R/S)
     *
     * where R = returned line value and S = original sale subtotal.
     */
    public function computeRefund($sale, $returnedLineValue, $alreadyRefunded = 0.0) {
        $saleSubtotal = (float)$sale['subtotal'];
        $ratio = $saleSubtotal > 0 ? $returnedLineValue / $saleSubtotal : 0.0;
        $ratio = max(0.0, min(1.0, $ratio));

        $discountShare = round((float)$sale['discount'] * $ratio, 2);
        $taxShare      = round((float)$sale['tax'] * $ratio, 2);
        $shippingShare = round((float)$sale['shipping'] * $ratio, 2);

        $refund = round($returnedLineValue - $discountShare + $taxShare + $shippingShare, 2);

        // Never refund more than the customer is still owed on this sale.
        $ceiling = round((float)$sale['grand_total'] - (float)$alreadyRefunded, 2);
        $refund  = min(max(0.0, $refund), max(0.0, $ceiling));

        return [
            'returned_line_value' => round($returnedLineValue, 2),
            'discount'  => $discountShare,
            'tax'       => $taxShare,
            'shipping'  => $shippingShare,
            'refund_total' => $refund,
            'ratio'     => $ratio,
        ];
    }

    /** Sum of refunds already paid against a sale (all RMAs, minus cancelled). */
    private function alreadyRefunded($saleId) {
        $row = $this->db->fetch(
            "SELECT COALESCE(SUM(refund_total), 0) as t
             FROM sale_returns WHERE sale_id = ? AND status IN ('refunded','received')", [$saleId]
        );
        return (float)$row['t'];
    }

    /**
     * Create a return document. Client-supplied prices are never trusted:
     * only line ids and quantities arrive from the browser; unit_price and
     * discount are read from the original line.
     */
    public function create($data) {
        $type       = ($data['type'] ?? 'pos') === 'online' ? 'online' : 'pos';
        $sourceId   = $type === 'online' ? (int)($data['order_id'] ?? 0) : (int)($data['sale_id'] ?? 0);
        $items      = $data['items'] ?? null;
        $reasonCode = trim((string)($data['reason_code'] ?? ''));

        if (!$sourceId) return ['success' => false, 'message' => ($type === 'online' ? 'Order' : 'Sale') . ' id is required'];
        if (!is_array($items) || !$items) return ['success' => false, 'message' => 'Select at least one item to return'];
        if (!$reasonCode) return ['success' => false, 'message' => 'A return reason is required'];

        $role = $_SESSION['user_role'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;
        $canOverrideWindow = in_array($role, ['admin', 'manager'], true);
        $isCustomer = $role === 'customer';

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            if ($type === 'online') {
                $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$sourceId]);
                if (!$order) throw new \Exception('Order not found');
                if (!in_array($order['status'], ['delivered'], true)) {
                    throw new \Exception('Only delivered orders can be returned');
                }
                if ($isCustomer && (int)$order['user_id'] !== (int)$userId) {
                    throw new \Exception('Order not found');
                }
                $branchId = (int)($order['sale_id']
                    ? ($this->db->fetch("SELECT branch_id FROM sales WHERE id = ?", [$order['sale_id']])['branch_id'] ?? 1)
                    : 1);
                $status = 'requested';
            } else {
                $sale = $this->db->fetch("SELECT * FROM sales WHERE id = ?", [$sourceId]);
                if (!$sale) throw new \Exception('Sale not found');
                if ($sale['status'] === 'cancelled') throw new \Exception('Cancelled sales cannot be returned');
                if ($sale['status'] === 'held')     throw new \Exception('Resume or cancel the held sale first');
                $branchId = (int)$sale['branch_id'];
                $status = 'approved';
            }

            // Return window check, measured from delivery for online
            $windowDays = (int)$this->setting('return_window_days', 14);
            $anchor = $type === 'online' ? ($order['updated_at'] ?? $order['created_at']) : $sale['created_at'];
            $ageDays = (int)floor((time() - strtotime($anchor)) / 86400);
            $override = !empty($data['override_window']) && $canOverrideWindow;
            if ($ageDays > $windowDays && !$override) {
                throw new \Exception("Outside the {$windowDays}-day return window ({$ageDays} days)");
            }

            // Authoritative returnable lines
            $idField = $type === 'online' ? 'order_item_id' : 'sale_item_id';
            $tbl     = $type === 'online' ? 'order_items' : 'sale_items';
            $fkCol   = $type === 'online' ? 'order_id'  : 'sale_id';
            $docCol  = $type === 'online' ? 'order_id'  : 'sale_id';

            $lines = $this->db->fetchAll(
                "SELECT * FROM {$tbl} WHERE {$fkCol} = ? AND qty > 0", [$sourceId]
            );
            $lineMap = [];
            foreach ($lines as $l) $lineMap[(int)$l['id']] = $l;

            $returnedMap = $this->db->fetchAll(
                "SELECT ri.{$idField} as line_id, COALESCE(SUM(ri.qty),0) as qty
                 FROM sale_return_items ri
                 JOIN sale_returns r ON r.id = ri.return_id
                 WHERE r.status <> 'cancelled' AND r.{$docCol} = ?
                 GROUP BY ri.{$idField}", [$sourceId]
            );
            $already = [];
            foreach ($returnedMap as $row) $already[(int)$row['line_id']] = (int)$row['qty'];

            $accepted = [];
            $returnedValue = 0.0;
            foreach ($items as $item) {
                $lineId = (int)($item['sale_item_id'] ?? $item['order_item_id'] ?? $item['id'] ?? 0);
                if (!isset($lineMap[$lineId])) throw new \Exception("Line $lineId is not part of this document");

                $line  = $lineMap[$lineId];
                $qty   = (int)($item['qty'] ?? 0);
                if ($qty <= 0) continue;

                $remaining = (int)$line['qty'] - ($already[$lineId] ?? 0);
                if ($qty > $remaining) {
                    throw new \Exception(sprintf(
                        'Line %s: only %d unit(s) still returnable (requested %d)',
                        $line['product_id'], max(0, $remaining), $qty
                    ));
                }

                // Unit price and discount always come from the source line
                $lineTotal = round($qty * (float)$line['unit_price'], 2);
                $already[$lineId] = ($already[$lineId] ?? 0) + $qty;

                $accepted[] = [
                    'line_id'   => $lineId,
                    'product_id'=> (int)$line['product_id'],
                    'qty'       => $qty,
                    'unit_price'=> (float)$line['unit_price'],
                    'discount'  => round((float)($line['discount'] ?? 0), 2),
                    'total'     => $lineTotal,
                ];
                $returnedValue += $lineTotal;
            }
            if (!$accepted) throw new \Exception('Select at least one item to return');

            // Money split. Online requests have no sale row to split
            // against, so their order columns are used instead.
            $doc = $type === 'online' ? $order : $sale;
            $money = $this->computeRefund($doc, $returnedValue, $type === 'online' ? 0.0 : $this->alreadyRefunded($sourceId));

            $returnNo = generateReturnNo('sale');
            $returnId = $this->db->insert('sale_returns', [
                'return_no'    => $returnNo,
                'type'         => $type,
                'sale_id'      => $type === 'pos'    ? $sourceId : null,
                'order_id'     => $type === 'online' ? $sourceId : null,
                'customer_id'  => $doc['customer_id'] ?? null,
                'branch_id'    => $branchId,
                'status'       => $status,
                'reason_code'  => $reasonCode,
                'reason'       => sanitize($data['reason'] ?? ''),
                'method'       => $data['method'] ?? 'original',
                'subtotal'     => $money['returned_line_value'],
                'discount'     => $money['discount'],
                'tax'          => $money['tax'],
                'shipping'     => $money['shipping'],
                'refund_total' => $money['refund_total'],
                'requested_by' => $isCustomer ? null : $userId,
                'requested_for'=> $isCustomer ? $userId : ($doc['user_id'] ?? null),
                'admin_note'   => sanitize($data['note'] ?? ''),
            ]);

            foreach ($accepted as $a) {
                $this->db->insert('sale_return_items', [
                    'return_id'     => $returnId,
                    $idField        => $a['line_id'],
                    'product_id'    => $a['product_id'],
                    'qty'           => $a['qty'],
                    'unit_price'    => $a['unit_price'],
                    'discount'      => $a['discount'],
                    'total'         => $a['total'],
                    'restock'       => 0,   // decided at receipt, see receive()
                    'condition_note'=> null,
                ]);
            }

            // Photo evidence (multipart upload from the same request)
            if (!empty($_FILES['photos'])) {
                (new ReturnPhoto())->addMany('sale', $returnId, null, $_FILES['photos'], $userId);
            }

            if ($type === 'online' && $status === 'requested') {
                // Tell the store team there is a request waiting
                (new Notification())->createForRole(
                    'Return request received',
                    "{$returnNo} is awaiting review",
                    ['admin', 'manager', 'branch_manager'],
                    'warning',
                    APP_URL . '/pages/returns/view.php?id=' . $returnId
                );
            }

            logActivity('sale_return_created', 'sale_returns', $returnId, null, [
                'return_no'    => $returnNo,
                'type'         => $type,
                'refund_total' => $money['refund_total'],
                'override_window' => $override,
            ]);

            $conn->commit();
            return ['success' => true, 'id' => $returnId, 'return_no' => $returnNo, 'refund_total' => $money['refund_total']];

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

        $this->db->update('sale_returns', [
            'status'      => 'approved',
            'approved_by' => $_SESSION['user_id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_note'  => sanitize($note),
        ], 'id = ?', [$id]);

        logActivity('sale_return_approved', 'sale_returns', $id, ['status' => 'requested'], ['status' => 'approved']);
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

        $this->db->update('sale_returns', [
            'status'      => 'rejected',
            'approved_by' => $_SESSION['user_id'] ?? null,
            'approved_at' => date('Y-m-d H:i:s'),
            'admin_note'  => sanitize($note),
        ], 'id = ?', [$id]);

        // Rejected requests hold no stock and no money, so nothing to undo.
        logActivity('sale_return_rejected', 'sale_returns', $id, ['status' => 'requested'], ['status' => 'rejected']);
        return ['success' => true];
    }

    /**
     * Receipt with per-line restock. restock=0 deliberately performs no
     * inventory write: the goods came back but are not sellable, and the
     * RMA row plus condition_note is the audit record. Writing a stock_logs
     * row for a quantity that never entered sellable stock would corrupt
     * the inventory ledger.
     */
    public function receive($id, $decisions = []) {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if (!in_array($r['return']['status'], ['approved'], true)) {
            return ['success' => false, 'message' => 'Only an approved return can be received'];
        }

        $map = [];
        foreach ($decisions as $d) $map[(int)($d['return_item_id'] ?? 0)] = $d;

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $inventory = new Inventory();
            $restocked = 0;

            foreach ($r['items'] as $it) {
                $d = $map[(int)$it['id']] ?? [];
                $restock = array_key_exists('restock', $d) ? (int)(bool)$d['restock'] : (int)$it['restock'];
                $note    = sanitize($d['condition_note'] ?? $it['condition_note'] ?? '');

                $this->db->update('sale_return_items', [
                    'restock'        => $restock,
                    'condition_note' => $note ?: null,
                ], 'id = ?', [$it['id']]);

                if ($restock === 1) {
                    $inventory->recordReturn(
                        (int)$it['product_id'],
                        (int)$it['qty'],
                        (int)$r['return']['branch_id'],
                        $id,
                        $note,
                        'sale_return'
                    );
                    $restocked += (int)$it['qty'];
                }

                // Keep the cached counter in step with the ledger. Increment rather
                // than absolute-set: receive() may run more than once per
                // source line across separate RMAs.
                if ($it['orig_line_id']) {
                    $this->db->query(
                        'UPDATE sale_items SET returned_qty = returned_qty + ? WHERE id = ?',
                        [(int)$it['qty'], (int)$it['orig_line_id']]
                    );
                }
            }

            $this->db->update('sale_returns', [
                'status'      => 'received',
                'received_by' => $_SESSION['user_id'] ?? null,
                'received_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$id]);

            $this->refreshSaleReturnState($r['return']['sale_id']);

            logActivity('sale_return_received', 'sale_returns', $id, ['status' => 'approved'], [
                'status' => 'received', 'restocked_qty' => $restocked
            ]);

            $conn->commit();
            return ['success' => true, 'restocked_qty' => $restocked];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Derive sales.status from how much has come back. This is the only
     * column of `sales` this phase ever writes.
     */
    private function refreshSaleReturnState($saleId) {
        if (!$saleId) return;

        $row = $this->db->fetch(
            "SELECT
                COALESCE(SUM(CASE WHEN si.returned_qty > 0 THEN si.returned_qty ELSE 0 END), 0) as returned_units,
                COALESCE(SUM(si.qty), 0) as sold_units
             FROM sale_items si WHERE si.sale_id = ?", [$saleId]
        );
        if (!$row) return;

        $sold = (int)$row['sold_units'];
        $back = (int)$row['returned_units'];

        $status = 'completed';
        if ($sold > 0 && $back >= $sold)  $status = 'returned';
        elseif ($back > 0)                $status = 'partially_returned';

        $current = $this->db->fetch("SELECT status FROM sales WHERE id = ?", [$saleId])['status'] ?? 'completed';
        if ($current !== $status && in_array($current, ['completed', 'partially_returned', 'returned'], true)) {
            $this->db->update('sales', ['status' => $status], 'id = ?', [$saleId]);
        }
    }

    public function refund($id, $options = []) {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        if (!in_array($r['return']['status'], ['received'], true)) {
            return ['success' => false, 'message' => 'Receive the goods before refunding'];
        }

        $ret = $r['return'];
        $role = $_SESSION['user_role'] ?? '';
        $method = $options['method'] ?? $ret['method'];

        // A cashier may only refund to the tender the sale was paid with.
        if ($method !== 'original' && !in_array($role, ['admin', 'manager'], true)) {
            return ['success' => false, 'message' => 'Only a manager or admin can refund to a different tender'];
        }
        // Cashiers can be fully barred from refunding by policy.
        if ($role === 'cashier' && (int)$this->setting('return_allow_cashier_refund', 1) === 0) {
            return ['success' => false, 'message' => 'Cashiers cannot refund returns — please ask a manager'];
        }
        $resolved = $this->resolveTender($ret, $method);

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $this->db->insert('sale_return_payments', [
                'return_id'    => $id,
                'amount'       => (float)$ret['refund_total'],
                'method'       => $resolved,
                'reference'    => sanitize($options['reference'] ?? ''),
                'notes'        => sanitize($options['notes'] ?? ''),
                'processed_by' => $_SESSION['user_id'] ?? null,
            ]);

            // Customer-balance refunds credit the balance rather than a till.
            // Customer.php ships deductBalance() but no addBalance(), so use
            // the same inline UPDATE the class already uses internally.
            if ($resolved === 'customer_balance' && $ret['customer_id']) {
                $this->db->query(
                    'UPDATE customers SET balance = balance + ? WHERE id = ?',
                    [(float)$ret['refund_total'], (int)$ret['customer_id']]
                );
            }

            $pointsBack = $this->reverseLoyalty($ret);

            $this->db->update('sale_returns', [
                'status'           => 'refunded',
                'method'           => $method,
                'refunded_by'      => $_SESSION['user_id'] ?? null,
                'refunded_at'      => date('Y-m-d H:i:s'),
                'loyalty_reversed' => $pointsBack,
            ], 'id = ?', [$id]);

            logActivity('sale_return_refunded', 'sale_returns', $id, ['status' => 'received'], [
                'status' => 'refunded', 'amount' => $ret['refund_total'],
                'tender' => $resolved, 'loyalty_reversed' => $pointsBack
            ]);

            $conn->commit();
            return ['success' => true, 'amount' => (float)$ret['refund_total'], 'tender' => $resolved];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Exchange a received/approved return for a fresh POS sale, atomically.
     *
     * One transaction performs: per-line restock of the returned goods
     * (when the RMA was still 'approved'), the replacement sale at current
     * catalog prices (stock-out happens inside Sale::create, AFTER restock,
     * so a same-item swap satisfies the stock check), the price-difference
     * settlement (customer pays in for a positive difference; admin/manager
     * cash-out for a negative one), the loyalty clawback, and the symmetric
     * cross-links sales.exchange_return_id <-> sale_returns.exchange_sale_id.
     */
    public function exchange($returnId, $decisions = [], $replacementItems = [], $payments = []) {
        $r = $this->getById($returnId);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];

        $ret = $r['return'];
        $role = $_SESSION['user_role'] ?? '';

        if ($ret['type'] !== 'pos' || !$ret['sale_id']) {
            return ['success' => false, 'message' => 'Only POS returns can be exchanged'];
        }
        if (!empty($ret['exchange_sale_id'])) {
            return ['success' => false, 'message' => 'This return has already been exchanged'];
        }
        if (!in_array($ret['status'], ['approved', 'received'], true)) {
            return ['success' => false, 'message' => 'Receive the goods before exchanging'];
        }
        if (!is_array($replacementItems) || empty($replacementItems)) {
            return ['success' => false, 'message' => 'Choose at least one replacement item'];
        }

        $this->db->beginTransaction();
        try {
            $branchId = (int)$ret['branch_id'];
            $inventory = new Inventory();

            // ---- 1) Restock the returned goods if this RMA is still 'approved'
            if ($ret['status'] === 'approved') {
                $map = [];
                foreach ($decisions as $d) $map[(int)($d['return_item_id'] ?? 0)] = $d;

                foreach ($r['items'] as $it) {
                    $d = $map[(int)$it['id']] ?? [];
                    $restock = array_key_exists('restock', $d) ? (int)(bool)$d['restock'] : 1;
                    $note = sanitize($d['condition_note'] ?? '');

                    $this->db->update('sale_return_items', [
                        'restock'        => $restock,
                        'condition_note' => $note ?: null,
                    ], 'id = ?', [$it['id']]);

                    if ($restock === 1) {
                        $inventory->recordReturn((int)$it['product_id'], (int)$it['qty'], $branchId, $returnId, $note, 'sale_return');
                    }
                    // Cached counter increments ONLY for restocked lines; the
                    // authoritative over-return guard tallies every live RMA.
                    if ($it['orig_line_id']) {
                        $this->db->query(
                            'UPDATE sale_items SET returned_qty = returned_qty + ? WHERE id = ?',
                            [(int)$it['qty'], (int)$it['orig_line_id']]
                        );
                    }
                }
                $this->db->update('sale_returns', [
                    'status'      => 'received',
                    'received_by' => $_SESSION['user_id'] ?? null,
                    'received_at' => date('Y-m-d H:i:s'),
                ], 'id = ?', [$returnId]);
                $this->refreshSaleReturnState($ret['sale_id']);
            }

            // ---- 2) Build the replacement sale at current catalog prices.
            //      Prices come from the products table, never the browser.
            $exchangeCredit = (float)$ret['refund_total'];
            $lines = [];
            $subtotal = 0.0;
            foreach ($replacementItems as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $qty = (int)($item['qty'] ?? 0);
                if ($productId <= 0 || $qty <= 0) continue;

                $p = $this->db->fetch("SELECT price, cost FROM products WHERE id = ? AND status = 'active'", [$productId]);
                if (!$p) throw new \Exception("Product $productId not found or inactive");

                $lineTotal = round($qty * (float)$p['price'], 2);
                $subtotal += $lineTotal;
                $lines[] = [
                    'product_id' => $productId,
                    'qty'        => $qty,
                    'unit_price' => (float)$p['price'],
                    'total'      => $lineTotal,
                ];
            }
            if (!$lines) throw new \Exception('Choose at least one valid replacement item');

            // The restock above ran FIRST so the same transaction can satisfy
            // the stock check (e.g. swapping the exact item that came back).
            $customerId = (int)$ret['customer_id'] ?: null;

            $sale = new Sale();
            $newSale = $sale->create([
                'customer_id'    => $customerId,
                'branch_id'      => $branchId,
                'items'          => $lines,
                'discount'       => 0.0,
                'shipping'       => 0.0,
                'payment_method' => 'cash',
            ], $returnId);                 // exchangeReturnId forward link

            // Drop Sale::create's default 'full total as cash' payment row:
            // an exchange is funded by the difference, not the full price.
            $this->db->delete('sale_payments', 'sale_id = ?', [(int)$newSale['id']]);

            // ---- 3) Difference settlement
            $difference = round($newSale['grand_total'] - $exchangeCredit, 2);
            $effectiveMethod = null;
            $methodsUsed = [];

            if ($difference >= 0) {
                // Customer pays the difference (or zero) — any staff role
                $paid = 0.0;
                if ($difference > 0) {
                    if (empty($payments)) throw new \Exception('A payment for the price difference is required');
                    foreach ($payments as $pay) {
                        $amount = round((float)$pay['amount'], 2);
                        if ($amount <= 0) continue;
                        $method = in_array($pay['method'] ?? '', ['cash', 'card', 'mobile'], true) ? $pay['method'] : 'cash';
                        $this->db->insert('sale_payments', [
                            'sale_id'        => (int)$newSale['id'],
                            'amount'         => $amount,
                            'payment_method' => $method,
                            'reference'      => sanitize($pay['reference'] ?? ''),
                            'received_by'    => $_SESSION['user_id'] ?? null,
                        ]);
                        $methodsUsed[$method] = true;
                        $paid += $amount;
                    }
                    if (round($paid - $difference, 2) != 0.0) {
                        throw new \Exception(sprintf('Difference is %s but only %s was paid', formatCurrency($difference), formatCurrency($paid)));
                    }
                }
            } else {
                // Store owes the customer — escalation gate. The till money
                // genuinely leaves, so the ledger row stays 'original' (the
                // tender the source sale was paid with) rather than 'exchange'.
                if (!in_array($role, ['admin', 'manager'], true)) {
                    throw new \Exception('A cash-out exchange (negative difference) needs a manager or admin');
                }
                $cashOut = round(abs($difference), 2);
                $this->db->insert('sale_return_payments', [
                    'return_id'    => $returnId,
                    'amount'       => $cashOut,
                    'method'       => 'original',
                    'reference'    => 'Exchange cash-out on ' . $newSale['invoice_no'],
                    'processed_by' => $_SESSION['user_id'] ?? null,
                ]);
            }

            $effectiveMethod = count($methodsUsed) > 1 ? 'mixed' : (array_key_first($methodsUsed) ?: 'cash');
            $this->db->update('sales', ['payment_method' => $effectiveMethod], 'id = ?', [(int)$newSale['id']]);

            // ---- 4) Ledger the exchange credit (no money leaves the till)
            $this->db->insert('sale_return_payments', [
                'return_id'    => $returnId,
                'amount'       => $exchangeCredit,
                'method'       => 'exchange',
                'reference'    => 'Applied to ' . $newSale['invoice_no'],
                'notes'        => 'Exchanged at the counter',
                'processed_by' => $_SESSION['user_id'] ?? null,
            ]);

            $pointsBack = $this->reverseLoyalty($ret);

            // ---- 5) Link both documents and finish the return
            $this->db->update('sale_returns', [
                'status'           => 'refunded',
                'exchange_sale_id' => (int)$newSale['id'],
                'refunded_by'      => $_SESSION['user_id'] ?? null,
                'refunded_at'      => date('Y-m-d H:i:s'),
                'loyalty_reversed' => $pointsBack,
            ], 'id = ?', [$returnId]);

            $this->db->update('sales', ['exchange_return_id' => $returnId], 'id = ?', [(int)$newSale['id']]);
            $this->refreshSaleReturnState($ret['sale_id']);

            logActivity('sale_exchanged', 'sale_returns', $returnId, [
                'status' => $ret['status'], 'return_no' => $ret['return_no'],
            ], [
                'status' => 'refunded', 'new_sale_id' => (int)$newSale['id'],
                'exchange_credit' => $exchangeCredit, 'difference' => $difference,
                'points_back' => $pointsBack,
            ]);

            $this->db->commit();
            return ['success' => true, 'new_sale_id' => (int)$newSale['id'], 'difference' => $difference];

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

/** Map 'original' onto the tender the underlying document was paid with. */
    private function resolveTender($ret, $method) {
        if ($method !== 'original') return $method;
        $doc = $this->db->fetch("SELECT payment_method FROM sales WHERE id = ?", [$ret['sale_id']]);
        $pm = $doc['payment_method'] ?? 'cash';
        return in_array($pm, ['cash', 'card', 'mobile'], true) ? $pm : 'cash';
    }

    /**
     * Sale::create() awards floor(grand_total / 10) points. A refund claws
     * the matching share back, scaled by how much of the sale came back.
     * Never drives the balance below zero.
     */
    private function reverseLoyalty($ret) {
        if (!$ret['customer_id'] || (int)$ret['customer_id'] === 0) return 0;
        if ($ret['type'] === 'online') return 0;   // storefront orders award no points

        $cust = $this->db->fetch("SELECT loyalty_points FROM customers WHERE id = ?", [$ret['customer_id']]);
        if (!$cust) return 0;

        $earned = (int)floor((float)$ret['sale_grand_total'] / 10);
        if ($earned <= 0) return 0;

        $ratio = (float)$ret['sale_grand_total'] > 0
            ? (float)$ret['refund_total'] / (float)$ret['sale_grand_total'] : 0.0;
        $clawback = (int)floor($earned * max(0.0, min(1.0, $ratio)));
        $clawback = min($clawback, (int)$cust['loyalty_points']);

        if ($clawback > 0) $this->db->query('UPDATE customers SET loyalty_points = loyalty_points - ? WHERE id = ?', [$clawback, (int)$ret['customer_id']]);
        return $clawback;
    }

    /**
     * Void is a reversal, never a delete. It puts stock back out, debits any
     * customer-balance credit, and leaves the cancelled RMA in place.
     */
    public function void($id, $reason = '') {
        $r = $this->getById($id);
        if (!$r) return ['success' => false, 'message' => 'Return not found'];
        $ret = $r['return'];
        if (!in_array($ret['status'], ['approved', 'received', 'refunded'], true)) {
            return ['success' => false, 'message' => 'This return cannot be voided'];
        }
        if (!empty($ret['exchange_sale_id'])) {
            return ['success' => false, 'message' => 'This return was settled by an exchange — cancel the linked exchange sale (#' . (int)$ret['exchange_sale_id'] . ') first, then void this return'];
        }

        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        try {
            $inventory = new Inventory();

            // Unwind any restock this return already performed
            if (in_array($ret['status'], ['received', 'refunded'], true)) {
                foreach ($r['items'] as $it) {
                    if ((int)$it['restock'] !== 1) continue;
                    $inventory->recordReturn(
                        (int)$it['product_id'],
                        (int)$it['qty'],
                        (int)$ret['branch_id'],
                        $id,
                        "Voided return {$ret['return_no']}: " . $reason,
                        'sale_return_void'
                    );
                }
            }

            // Debit a customer-balance credit, and give back loyalty
            foreach ($r['payments'] as $p) {
                if ($p['method'] === 'customer_balance' && $ret['customer_id']) {
                    (new Customer())->deductBalance((int)$ret['customer_id'], (float)$p['amount']);
                }
            }
            if ((int)$ret['loyalty_reversed'] > 0 && $ret['customer_id']) {
                (new Customer())->addLoyaltyPoints((int)$ret['customer_id'], (int)$ret['loyalty_reversed']);
            }

            // Free the source lines back up for a fresh return: decrement by
            // this return's share rather than zeroing, so a line shared with
            // another live RMA keeps the rest of its tally.
            foreach ($r['items'] as $it) {
                if (!$it['sale_item_id']) continue;
                if (in_array($ret['status'], ['received', 'refunded'], true)) {
                    $this->db->query(
                        'UPDATE sale_items SET returned_qty = GREATEST(returned_qty - ?, 0) WHERE id = ?',
                        [(int)$it['qty'], (int)$it['sale_item_id']]
                    );
                }
            }
            $this->refreshSaleReturnState($ret['sale_id']);

            $this->db->update('sale_returns', [
                'status'     => 'cancelled',
                'admin_note' => 'VOIDED: ' . sanitize($reason),
            ], 'id = ?', [$id]);

            logActivity('sale_return_voided', 'sale_returns', $id, ['status' => $ret['status']], [
                'status' => 'cancelled', 'reason' => $reason
            ]);

            $conn->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /** The most recent RMA on a sale that can still be exchanged. */
    public function getExchangeableForSale($saleId) {
        return $this->db->fetch(
            "SELECT id, return_no, status, refund_total FROM sale_returns
             WHERE sale_id = ? AND type = 'pos' AND status IN ('approved','received')
               AND exchange_sale_id IS NULL
             ORDER BY id DESC LIMIT 1", [(int)$saleId]
        );
    }

    /** Customer's own list (used by api/storefront/returns.php). */
    public function dbListForUser($userId, $filters = []) {
        $where = "r.requested_for = ? AND r.status <> 'cancelled'";
        $params = [$userId];
        if (!empty($filters['status'])) { $where .= " AND r.status = ?"; $params[] = $filters['status']; }

        $total = (int)$this->db->fetch("SELECT COUNT(*) as t FROM sale_returns r WHERE {$where}", $params)['t'];
        $pagination = paginate($total, 20, (int)($filters['page'] ?? 1));

        $rows = $this->db->fetchAll(
            "SELECT r.*, o.order_no, o.status as order_status,
                    (SELECT COALESCE(SUM(ri.qty),0) FROM sale_return_items ri WHERE ri.return_id = r.id) as total_qty
             FROM sale_returns r LEFT JOIN orders o ON r.order_id = o.id
             WHERE {$where} ORDER BY r.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['returns' => $rows, 'pagination' => $pagination, 'total' => $total];
    }
}