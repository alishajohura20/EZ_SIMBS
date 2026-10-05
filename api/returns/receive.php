<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
if (!$id) jsonError('Return id is required');

try {
    $result = (new SaleReturn())->receive($id, $data['items'] ?? []);
    if (!$result['success']) jsonError($result['message']);
    jsonSuccess($result, 'Goods received');
} catch (Exception $e) {
    jsonError($e->getMessage());
}