<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$category = new Category();

if ($method === 'GET') {
    if ($id) {
        $cat = $category->getById($id);
        if (!$cat) jsonError('Category not found', 404);
        jsonSuccess($cat);
    }
    $categories = $category->getAll($_GET['search'] ?? '', $_GET['status'] ?? '');
    jsonSuccess($categories);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['name'])) jsonError('Category name is required');
    $id = $category->create($data);
    jsonSuccess(['id' => $id], 'Category created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Category id is required');
    $category->update($id, getRequestBody());
    jsonSuccess(null, 'Category updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Category id is required');
    $category->delete($id);
    jsonSuccess(null, 'Category deleted');
} else {
    jsonError('Method not allowed', 405);
}
