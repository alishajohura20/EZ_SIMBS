<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$report = new Report();
$result = $report->inventoryReport($_GET);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $report->exportCSV($result['details'] ?? [], 'inventory_report_' . date('Y-m-d') . '.csv');
}
jsonSuccess($result);
