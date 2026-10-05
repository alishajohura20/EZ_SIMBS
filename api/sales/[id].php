<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sale = new Sale();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $sale->getById($id);
    if (!$result) jsonError('Not found', 404);
    jsonSuccess($result);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $sale->cancel($id);
    if ($result['success']) jsonSuccess(null, 'Sale cancelled');
    else jsonError($result['message']);
}
