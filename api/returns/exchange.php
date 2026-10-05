<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
if (!$id) jsonError('Return id is required');

// The cashier penalty for a negative difference (store owes money back) is
// enforced inside the class with an explicit error — the API gate stays open
// for cashiers and the class decides, matching the Phase 13 tender-lock.
try {
    $result = (new SaleReturn())->exchange(
        $id,
        $data['items'] ?? [],               // restock decisions: {return_item_id, restock, condition_note}
        $data['replacement_items'] ?? [],   // replacement catalog: {product_id, qty}
        $data['payments'] ?? []             // difference payments: {method, amount, reference}
    );
    if (!$result['success']) jsonError($result['message']);
    jsonSuccess($result, 'Exchange completed — check the new receipt');
} catch (Exception $e) {
    jsonError($e->getMessage());
}