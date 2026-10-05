<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$category = new Category();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cat = $category->getById($id);
    if (!$cat) jsonError('Not found', 404);
    jsonSuccess($cat);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = getRequestBody();
    $category->update($id, $data);
    jsonSuccess(null, 'Updated');
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $category->delete($id);
    jsonSuccess(null, 'Deleted');
}
