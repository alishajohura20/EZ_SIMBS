<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$brand = new Brand();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $b = $brand->getById($id);
    if (!$b) jsonError('Not found', 404);
    jsonSuccess($b);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $brand->update($id, getRequestBody());
    jsonSuccess(null, 'Updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $brand->delete($id);
    jsonSuccess(null, 'Deleted');
}
