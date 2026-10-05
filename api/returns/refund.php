<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
if (!$id) jsonError('Return id is required');

// Cashiers are limited to the original tender. This is enforced in the
// class, which returns an explicit error rather than a silent override.
try {
    $result = (new SaleReturn())->refund($id, [
        'method'    => $data['method'] ?? 'original',
        'reference' => $data['reference'] ?? '',
        'notes'     => $data['notes'] ?? '',
    ]);
    if (!$result['success']) jsonError($result['message']);
    jsonSuccess($result, 'Refunded ' . formatCurrency($result['amount']));
} catch (Exception $e) {
    jsonError($e->getMessage());
}