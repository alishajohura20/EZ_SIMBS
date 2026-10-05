<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$inv = new Inventory();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $inv->getAllStock($_GET['branch_id'] ?? null, $_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'stock_in':
            $inv->stockIn($data['product_id'], (int)$data['qty'], $data['branch_id'] ?? null, $data['reference_id'] ?? null, $data['reference_type'] ?? null, $data['notes'] ?? '');
            break;
        case 'stock_out':
            $result = $inv->stockOut($data['product_id'], (int)$data['qty'], $data['branch_id'] ?? null, $data['reference_id'] ?? null, $data['reference_type'] ?? null, $data['notes'] ?? '');
            if (is_array($result) && !$result['success']) jsonError($result['message']);
            break;
        case 'adjust':
            $inv->adjustStock($data['product_id'], (int)$data['new_qty'], $data['branch_id'] ?? null, $data['reason'] ?? '');
            break;
        case 'damage':
            $result = $inv->recordDamage($data['product_id'], (int)$data['qty'], $data['branch_id'] ?? null, $data['notes'] ?? '');
            if (is_array($result) && !$result['success']) jsonError($result['message']);
            break;
        case 'return':
            $inv->recordReturn($data['product_id'], (int)$data['qty'], $data['branch_id'] ?? null, $data['reference_id'] ?? null, $data['notes'] ?? '');
            break;
        case 'transfer':
            $result = $inv->transferStock($data['product_id'], (int)$data['from_branch'], (int)$data['to_branch'], (int)$data['qty']);
            if (is_array($result) && !$result['success']) jsonError($result['message']);
            break;
        default:
            jsonError('Invalid action');
    }
    jsonSuccess(null, 'Stock updated');
}
