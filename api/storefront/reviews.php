<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$sf = new Storefront();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        requireLogin();
        $data = getRequestBody();
        $productId = (int)($data['product_id'] ?? 0);
        $rating = (int)($data['rating'] ?? 5);
        $title = trim($data['title'] ?? '');
        $comment = trim($data['comment'] ?? '');
        if ($productId <= 0) jsonError('product_id is required', 400);
        if ($rating < 1 || $rating > 5) jsonError('Rating must be between 1 and 5', 400);
        $product = $sf->getProduct($productId);
        if (!$product) jsonError('Product not found', 404);
        $reviewId = $sf->addReview($productId, $_SESSION['user_id'], $rating, $title, $comment);
        if (!$reviewId) {
            jsonError('You have already reviewed this product', 400);
        }
        logActivity('review_added', 'products', $productId, null, ['rating' => $rating]);
        jsonSuccess(['review_id' => $reviewId], 'Review submitted');
        break;

    default:
        jsonError('Method not allowed', 405);
}