<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$unit = new Unit();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $u = $unit->getById($id);
        if (!$u) jsonError('Unit not found', 404);
        jsonSuccess($u);
    }
    jsonSuccess($unit->getAll($_GET['search'] ?? '', $_GET['used'] ?? ''));
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['name']) || empty($data['symbol'])) jsonError('Unit name and symbol are required');
    $id = $unit->create($data);
    jsonSuccess(['id' => $id], 'Unit created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Unit id is required');
    $unit->update($id, getRequestBody());
    jsonSuccess(null, 'Unit updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Unit id is required');
    $unit->delete($id);
    jsonSuccess(null, 'Unit deleted');
} else {
    jsonError('Method not allowed', 405);
}
