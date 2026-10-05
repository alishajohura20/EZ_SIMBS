<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$report = new Report();
$result = $report->purchaseReport($_GET);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $report->exportCSV($result['by_supplier'] ?? [], 'purchase_report_' . date('Y-m-d') . '.csv');
}
jsonSuccess($result);
