<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$sf = new Storefront();
$userId = $_SESSION['user_id'] ?? null;

$banners = $sf->getBanners('hero');
$categories = $sf->getCategories();
$featured = $sf->getFeaturedProducts(12, $userId);

jsonSuccess([
    'banners' => $banners,
    'categories' => $categories,
    'featured' => $featured,
]);