<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$sf = new Storefront();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getRequestBody();
    $productId = (int)($data['id'] ?? 0);
    if ($productId <= 0) {
        jsonError('Product id is required', 400);
    }
    $product = $sf->getProduct($productId);
    if (!$product) {
        jsonError('Product not found', 404);
    }
    $product['gallery'] = $sf->getProductGallery($productId);
    $product['rating_summary'] = $sf->getRatingSummary($productId);
    $product['reviews'] = $sf->getProductReviews($productId);
    $inWishlist = false;
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer') {
        $inWishlist = $sf->isInWishlist($_SESSION['user_id'], $productId);
    }
    jsonSuccess(['product' => $product, 'in_wishlist' => $inWishlist]);
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    jsonError('Product id is required', 400);
}
$product = $sf->getProduct($id);
if (!$product) {
    jsonError('Product not found', 404);
}
$product['gallery'] = $sf->getProductGallery($id);
$product['rating_summary'] = $sf->getRatingSummary($id);
$product['reviews'] = $sf->getProductReviews($id);
$inWishlist = false;
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer') {
    $inWishlist = $sf->isInWishlist($_SESSION['user_id'], $id);
}
jsonSuccess(['product' => $product, 'in_wishlist' => $inWishlist]);