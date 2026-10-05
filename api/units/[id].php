<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$unit = new Unit();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $u = $unit->getById($id);
    if (!$u) jsonError('Not found', 404);
    jsonSuccess($u);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $unit->update($id, getRequestBody());
    jsonSuccess(null, 'Updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $unit->delete($id);
    jsonSuccess(null, 'Deleted');
}
