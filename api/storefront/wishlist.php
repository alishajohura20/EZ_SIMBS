<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sf = new Storefront();
$userId = $_SESSION['user_id'];
$data = getRequestBody();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        jsonSuccess(['wishlist' => $sf->getWishlist($userId, true)]);
        break;

    case 'POST':
        $productId = (int)($data['product_id'] ?? $_GET['product_id'] ?? 0);
        if ($productId <= 0) jsonError('product_id is required', 400);
        $result = $sf->toggleWishlist($userId, $productId);
        jsonSuccess(['added' => $result['added']]);
        break;

    case 'DELETE':
        $productId = (int)($data['product_id'] ?? $_GET['product_id'] ?? 0);
        if ($productId <= 0) jsonError('product_id is required', 400);
        Database::getInstance()->delete('wishlist', 'user_id = ? AND product_id = ?', [$userId, $productId]);
        jsonSuccess(null, 'Removed from wishlist');
        break;

    default:
        jsonError('Method not allowed', 405);
}