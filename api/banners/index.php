<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager']);

$banner = new Banner();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $b = $banner->getById($id);
        if (!$b) jsonError('Banner not found', 404);
        jsonSuccess($b);
    }
    jsonSuccess($banner->getAll($_GET['position'] ?? '', $_GET['status'] ?? ''));
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['title']) && empty($_FILES['image']['name'])) {
        jsonError('A title or an image is required');
    }
    $id = $banner->create($data, $_FILES['image'] ?? null);
    jsonSuccess(['id' => $id], 'Banner created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Banner id is required');
    $banner->update($id, getRequestBody(), $_FILES['image'] ?? null);
    jsonSuccess(null, 'Banner updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Banner id is required');
    $banner->delete($id);
    jsonSuccess(null, 'Banner deleted');
} else {
    jsonError('Method not allowed', 405);
}
