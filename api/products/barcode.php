<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$product = new Product();
$barcode = $_GET['code'] ?? '';
if (!$barcode) jsonError('Barcode required');

$p = $product->getByBarcode($barcode);
if (!$p) jsonError('Product not found', 404);
jsonSuccess($p);
