<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$purchase = new Purchase();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $purchase->getById($id);
    if (!$result) jsonError('Not found', 404);
    jsonSuccess($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $result = $purchase->update($id, getRequestBody());
    if ($result['success']) jsonSuccess(null, 'Purchase updated');
    else jsonError($result['message']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $purchase->updateStatus($id, 'cancelled');
    if ($result['success']) jsonSuccess(null, 'Purchase cancelled');
    else jsonError($result['message']);
}
