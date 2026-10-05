<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$purchase = new Purchase();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $result = $purchase->getById($id);
        if (!$result) jsonError('Purchase not found', 404);
        jsonSuccess($result);
    }
    $result = $purchase->getAll($_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['supplier_id']) || empty($data['items'])) {
        jsonError('Supplier and items are required');
    }
    $result = $purchase->create($data);
    jsonSuccess($result, 'Purchase order created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Purchase id is required');
    $result = $purchase->update($id, getRequestBody());
    if ($result['success']) jsonSuccess(null, 'Purchase updated');
    else jsonError($result['message']);
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Purchase id is required');
    $result = $purchase->cancel($id);
    if ($result['success']) jsonSuccess(null, 'Purchase cancelled');
    else jsonError($result['message']);
} else {
    jsonError('Method not allowed', 405);
}
