<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$inv = new Inventory();
$logs = $inv->getStockLogs(
    $_GET['product_id'] ?? null,
    $_GET['branch_id'] ?? null,
    $_GET['type'] ?? '',
    (int)($_GET['limit'] ?? 20),
    (int)($_GET['page'] ?? 1),
    $_GET['product'] ?? ''
);
jsonSuccess($logs);
