<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$product = new Product();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        $rows = $product->exportCSV($_GET);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="products.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['SKU', 'Name', 'Category', 'Brand', 'Unit', 'Price', 'Cost', 'Min Stock', 'Reorder Level', 'Barcode', 'Status', 'Stock']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['sku'], $row['name'], $row['category_name'], $row['brand_name'],
                $row['unit_symbol'], $row['price'], $row['cost'], $row['min_stock'],
                $row['reorder_level'], $row['barcode'], $row['status'], $row['total_stock'],
            ]);
        }
        fclose($out);
        exit;
    }
    if ($id) {
        $p = $product->getById($id);
        if (!$p) jsonError('Product not found', 404);
        jsonSuccess($p);
    }
    $result = $product->getAll($_GET);
    jsonSuccess($result);
} elseif ($method === 'POST') {
    $data = getRequestBody();
    if (empty($data['name'])) jsonError('Product name is required');
    $id = $product->create($data);
    jsonSuccess(['id' => $id], 'Product created');
} elseif ($method === 'PUT') {
    if (!$id) jsonError('Product id is required');
    $product->update($id, getRequestBody());
    jsonSuccess(null, 'Product updated');
} elseif ($method === 'DELETE') {
    if (!$id) jsonError('Product id is required');
    $product->delete($id);
    jsonSuccess(null, 'Product deleted');
} else {
    jsonError('Method not allowed', 405);
}
