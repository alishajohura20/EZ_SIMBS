<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$brand = new Brand();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $b = $brand->getById($id);
        if (!$b) jsonError('Brand not found', 404);
        jsonSuccess($b);
    }
    $brands = $brand->getAll($_GET['search'] ?? '', $_GET['status'] ?? '');
    jsonSuccess($brands);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['name'])) jsonError('Brand name is required');
    $id = $brand->create($data);
    jsonSuccess(['id' => $id], 'Brand created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Brand id is required');
    $brand->update($id, getRequestBody());
    jsonSuccess(null, 'Brand updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Brand id is required');
    $brand->delete($id);
    jsonSuccess(null, 'Brand deleted');
} else {
    jsonError('Method not allowed', 405);
}
