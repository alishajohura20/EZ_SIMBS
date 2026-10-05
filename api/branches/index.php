<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);

$branch = new Branch();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $result = $branch->getSummary((int)$_GET['id']);
        if (!$result) jsonError('Not found', 404);
        jsonSuccess($result);
    } else {
        jsonSuccess($branch->getAll(['search' => $_GET['search'] ?? '', 'page' => $_GET['page'] ?? 1]));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getRequestBody();
    if (empty($data['name'])) jsonError('Branch name is required');
    $id = $branch->create($data);
    jsonSuccess(['id' => $id], 'Branch created');
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = getRequestBody();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('Branch ID required');
    $branch->update($id, $data);
    jsonSuccess(null, 'Branch updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('Branch ID required');
    $result = $branch->delete($id);
    if ($result['success']) jsonSuccess(null, 'Branch deactivated');
    else jsonError($result['message']);
}
