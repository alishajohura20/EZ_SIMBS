<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$data = getRequestBody();
$supplierId = (int)($data['supplier_id'] ?? $_GET['supplier_id'] ?? 0);
if (!$supplierId) jsonError('Supplier id is required');

$action = $data['action'] ?? '';

// READ: current balance (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    jsonSuccess(['balance' => (float)(new Supplier())->getBalance($supplierId)]);
}

// Manually record money the supplier paid back
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'refund') {
    $amount = (float)($data['amount'] ?? 0);
    if ($amount <= 0) jsonError('Amount must be positive');
    (new Supplier())->deductBalance($supplierId, $amount);
    logActivity('supplier_balance_refund', 'suppliers', $supplierId, null, ['amount' => $amount, 'reference' => $data['reference'] ?? '']);
    jsonSuccess(null, formatCurrency($amount) . ' deducted from supplier balance');
}

jsonError('Unknown action', 400);