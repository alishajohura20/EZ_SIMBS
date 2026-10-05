<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$customer = new Customer();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $result = $customer->getProfile($id);
        if (!$result) jsonError('Customer not found', 404);
        jsonSuccess($result);
    }
    $result = $customer->getAll($_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['name'])) jsonError('Customer name is required');
    $id = $customer->create($data);
    $created = $customer->getById($id);
    jsonSuccess(['id' => $id, 'membership_id' => $created['membership_id'] ?? ''], 'Customer created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Customer id is required');
    $customer->update($id, getRequestBody());
    jsonSuccess(null, 'Customer updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Customer id is required');
    $result = $customer->delete($id);
    if ($result['success']) jsonSuccess(null, 'Customer deleted');
    else jsonError($result['message']);
} else {
    jsonError('Method not allowed', 405);
}
