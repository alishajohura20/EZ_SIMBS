<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
$reason = trim((string)($data['reason'] ?? ''));
if (!$id) jsonError('Return id is required');
if ($reason === '') jsonError('A void reason is required');

try {
    $result = (new PurchaseReturn())->void($id, $reason);
    if (!$result['success']) jsonError($result['message']);
    jsonSuccess(null, 'Return voided');
} catch (Exception $e) {
    jsonError($e->getMessage());
}