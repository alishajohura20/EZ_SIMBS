<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);

$notif = new Notification();
$reorder = new AutoReorder();

$lowStock = $notif->checkLowStock();
$outOfStock = $notif->checkOutOfStock();
$duePayments = $notif->checkDuePayments();
$reorderRequests = $reorder->checkAndCreateReorderRequests();

jsonSuccess([
    'low_stock_alerts' => $lowStock,
    'out_of_stock_alerts' => $outOfStock,
    'due_payment_alerts' => $duePayments,
    'auto_reorders_created' => count($reorderRequests),
]);
