<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$supplier = new Supplier();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $result = $supplier->getSummary($id);
        if (!$result) jsonError('Supplier not found', 404);
        jsonSuccess($result);
    }
    $result = $supplier->getAll($_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['company_name'])) jsonError('Company name is required');
    $id = $supplier->create($data);
    jsonSuccess(['id' => $id], 'Supplier created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Supplier id is required');
    $supplier->update($id, getRequestBody());
    jsonSuccess(null, 'Supplier updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Supplier id is required');
    $result = $supplier->delete($id);
    if ($result['success']) jsonSuccess(null, 'Supplier deleted');
    else jsonError($result['message']);
} else {
    jsonError('Method not allowed', 405);
}
