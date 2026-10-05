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

if (!in_array($_SESSION['user_role'], ['admin', 'manager'], true)) {
    $where .= " AND r.branch_id = ?";
    $params[] = $_SESSION['user_branch'] ?? 1;
}
if (!empty($_GET['supplier_id'])) { $where .= " AND r.supplier_id = ?"; $params[] = (int)$_GET['supplier_id']; }

$fromClause = "FROM purchase_returns r
               LEFT JOIN purchases p ON r.purchase_id = p.id
               LEFT JOIN suppliers s ON r.supplier_id = s.id
               LEFT JOIN branches b  ON r.branch_id = b.id
               WHERE {$where}";

$s = $db->fetch(
    "SELECT COUNT(*) as total_returns,
            COALESCE(SUM(r.credit_total), 0) as credited_total,
            COALESCE(SUM(CASE WHEN r.status IN ('approved','received','requested') THEN 1 ELSE 0 END), 0) as open_returns,
            COALESCE(SUM(r.applied_to_due), 0) as applied_to_due,
            COALESCE(SUM(r.to_balance), 0) as to_balance
     {$fromClause}", $params
);

$rows = $db->fetchAll(
    "SELECT r.return_no, r.status, r.reason_code, r.credit_total, r.created_at,
            p.po_number, s.company_name, b.name as branch_name
     {$fromClause} ORDER BY r.created_at DESC", $params
);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=purchase-returns-' . $from . '-to-' . $to . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Return No','PO','Supplier','Reason','Status','Credit','Branch','Date']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['return_no'], $r['po_number'], $r['company_name'],
            $r['reason_code'], $r['status'], $r['credit_total'], $r['branch_name'], $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

jsonSuccess([
    'total_returns'  => (int)$s['total_returns'],
    'credited_total' => (float)$s['credited_total'],
    'open_returns'   => (int)$s['open_returns'],
    'applied_to_due' => (float)$s['applied_to_due'],
    'to_balance'     => (float)$s['to_balance'],
    'by_supplier'    => $db->fetchAll(
        "SELECT s.company_name, COUNT(*) as count, COALESCE(SUM(r.credit_total),0) as total
         {$fromClause} GROUP BY s.id, s.company_name ORDER BY total DESC", $params),
    'by_reason'      => $db->fetchAll(
        "SELECT r.reason_code, COUNT(*) as count, COALESCE(SUM(r.credit_total),0) as total
         {$fromClause} AND r.reason_code IS NOT NULL GROUP BY r.reason_code ORDER BY total DESC", $params),
    'by_product'     => $db->fetchAll(
        "SELECT ri.product_id, p.name, p.sku, SUM(ri.qty) as qty, SUM(ri.total) as total
         FROM purchase_return_items ri
         JOIN purchase_returns r ON r.id = ri.return_id
         JOIN products p ON p.id = ri.product_id
         WHERE r.created_at BETWEEN ? AND ?
         GROUP BY ri.product_id, p.name, p.sku ORDER BY total DESC LIMIT 20", [$from, $to . ' 23:59:59']),
    'returns'        => $rows,
]);