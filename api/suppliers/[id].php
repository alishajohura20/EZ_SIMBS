<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$supplier = new Supplier();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $supplier->getSummary($id);
    if (!$result) jsonError('Not found', 404);
    jsonSuccess($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $supplier->update($id, getRequestBody());
    jsonSuccess(null, 'Supplier updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $supplier->delete($id);
    if ($result['success']) {
        jsonSuccess(null, 'Supplier deleted');
    } else {
        jsonError($result['message']);
    }
}
