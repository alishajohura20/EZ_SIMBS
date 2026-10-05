<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$returns = new PurchaseReturn();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $result = $returns->getById($id);
        if (!$result) jsonError('Return not found', 404);
        jsonSuccess($result);
    }
    jsonSuccess($returns->getAll($_GET));

} elseif ($method === 'POST') {
    $data = getRequestBody();
    try {
        $result = $returns->create($data);
        if (!$result['success']) jsonError($result['message']);
        jsonSuccess($result, 'Supplier return ' . $result['return_no'] . ' created');
    } catch (Exception $e) {
        jsonError($e->getMessage());
    }

} else {
    jsonError('Method not allowed', 405);
}