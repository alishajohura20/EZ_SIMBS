<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$customer = new Customer();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $customer->getProfile($id);
    if (!$result) jsonError('Not found', 404);
    jsonSuccess($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $customer->update($id, getRequestBody());
    jsonSuccess(null, 'Customer updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $customer->delete($id);
    if ($result['success']) jsonSuccess(null, 'Customer deleted');
    else jsonError($result['message']);
}
