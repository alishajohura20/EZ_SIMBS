<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
if (!$id) jsonError('Return id is required');

try {
    $result = (new PurchaseReturn())->credit($id, [
        'method'    => $data['method'] ?? 'credit_note',
        'reference' => $data['reference'] ?? '',
        'notes'     => $data['notes'] ?? '',
    ]);
    if (!$result['success']) jsonError($result['message']);
    jsonSuccess($result, 'Credited ' . formatCurrency($result['amount']));
} catch (Exception $e) {
    jsonError($e->getMessage());
}