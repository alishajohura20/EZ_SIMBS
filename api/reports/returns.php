<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$db = Database::getInstance();
$from = $_GET['date_from'] ?? date('Y-m-01');
$to   = $_GET['date_to']   ?? date('Y-m-d');

$where = "r.created_at BETWEEN ? AND ?";
$params = [$from, $to . ' 23:59:59'];

// Branch scoping: only admin and manager may see every branch
if (!in_array($_SESSION['user_role'], ['admin', 'manager'], true)) {
    $where .= " AND r.branch_id = ?";
    $params[] = $_SESSION['user_branch'] ?? 1;
}
if (!empty($_GET['branch_id'])) { $where .= " AND r.branch_id = ?"; $params[] = (int)$_GET['branch_id']; }

$fromClause = "FROM sale_returns r
               LEFT JOIN sales s ON r.sale_id = s.id
               LEFT JOIN orders o ON r.order_id = o.id
               LEFT JOIN branches b ON r.branch_id = b.id
               WHERE {$where}";

if (isset($_GET['summary'])) {
    $s = $db->fetch(
        "SELECT
            COUNT(*) as total_returns,
            COALESCE(SUM(CASE WHEN r.status IN ('refunded') THEN r.refund_total ELSE 0 END),0) as refunded_total,
            COALESCE(SUM(CASE WHEN r.status IN ('requested','approved','received') THEN 1 ELSE 0 END),0) as open_requests,
            COALESCE(SUM(CASE WHEN ri.restock = 0 THEN ri.qty ELSE 0 END),0) as unrestocked_qty
         {$fromClause}
         LEFT JOIN sale_return_items ri ON ri.return_id = r.id", $params
    );

    $month = $db->fetch(
        "SELECT COALESCE(SUM(refund_total),0) as t FROM sale_returns
         WHERE status = 'refunded' AND refunded_at >= ?", [date('Y-m-01')]
    )['t'];

    // Return rate = refunded value / sales value in the same window
    $sales = $db->fetch(
        "SELECT COALESCE(SUM(grand_total),0) as t FROM sales
         WHERE status IN ('completed','partially_returned') AND created_at BETWEEN ? AND ?",
        [$from, $to . ' 23:59:59']
    )['t'];

    $counts = $db->fetch(
        "SELECT
            COALESCE(SUM(CASE WHEN r.status = 'requested' THEN 1 ELSE 0 END),0) as requested,
            COALESCE(SUM(CASE WHEN r.status = 'approved'   THEN 1 ELSE 0 END),0) as approved,
            COALESCE(SUM(CASE WHEN r.status = 'received'   THEN 1 ELSE 0 END),0) as received,
            COALESCE(SUM(CASE WHEN r.status = 'refunded'   THEN 1 ELSE 0 END),0) as refunded
         {$fromClause}", $params
    );
    $counts['total'] = (int)$s['total_returns'];

    jsonSuccess([
        'total_returns'    => (int)$s['total_returns'],
        'refunded_total'   => (float)$s['refunded_total'],
        'refunded_month'   => (float)$month,
        'open_requests'    => (int)$s['open_requests'],
        'unrestocked_qty'  => (int)$s['unrestocked_qty'],
        'return_rate'      => $sales > 0 ? round((float)$s['refunded_total'] / (float)$sales * 100, 2) : 0,
        'counts'           => array_map('intval', $counts),
        'monthly'          => ['refunded_this_month' => (float)$month],
        'by_reason'        => $db->fetchAll(
            "SELECT reason_code, COUNT(*) as count, COALESCE(SUM(refund_total),0) as total
             {$fromClause} GROUP BY reason_code ORDER BY total DESC", $params),
        'by_product'       => $db->fetchAll(
            "SELECT ri.product_id, p.name, p.sku, SUM(ri.qty) as qty, SUM(ri.total) as total
             FROM sale_return_items ri
             JOIN sale_returns r ON r.id = ri.return_id
             JOIN products p ON p.id = ri.product_id
             WHERE r.created_at BETWEEN ? AND ?
             GROUP BY ri.product_id, p.name, p.sku ORDER BY total DESC LIMIT 20", [$from, $to . ' 23:59:59']),
        'by_branch'        => $db->fetchAll(
            "SELECT b.name, COUNT(*) as count, COALESCE(SUM(r.refund_total),0) as total
             {$fromClause} GROUP BY r.branch_id, b.name ORDER BY total DESC", $params),
    ]);
}

$rows = $db->fetchAll(
    "SELECT r.return_no, r.type, r.status, r.reason_code, r.refund_total, r.created_at,
            s.invoice_no, o.order_no, b.name as branch_name
     {$fromClause} ORDER BY r.created_at DESC", $params
);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=returns-' . $from . '-to-' . $to . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Return No','Type','Source','Reason','Status','Refund','Branch','Date']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['return_no'], $r['type'], $r['invoice_no'] ?: $r['order_no'],
            $r['reason_code'], $r['status'], $r['refund_total'], $r['branch_name'], $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

jsonSuccess(['returns' => $rows, 'date_from' => $from, 'date_to' => $to]);