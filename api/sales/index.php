<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sale = new Sale();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $result = $sale->getById($id);
        if (!$result) jsonError('Sale not found', 404);
        jsonSuccess($result);
    }
    $result = $sale->getAll($_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['items'])) jsonError('No items');
    try {
        $result = $sale->create($data);
        jsonSuccess($result, 'Sale completed');
    } catch (Exception $e) {
        jsonError($e->getMessage());
    }
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Sale id is required');
    $result = $sale->cancel($id);
    if ($result['success']) jsonSuccess(null, 'Sale cancelled');
    else jsonError($result['message']);
} else {
    jsonError('Method not allowed', 405);
}
