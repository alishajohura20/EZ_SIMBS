<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$product = new Product();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $p = $product->getById($id);
    if (!$p) jsonError('Not found', 404);
    jsonSuccess($p);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $product->update($id, getRequestBody());
    jsonSuccess(null, 'Updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $product->delete($id);
    jsonSuccess(null, 'Deleted');
}
