<?php
/**
 * Catalog Module Routes
 * Maps to existing API endpoints and pages - zero code impact
 */

$apiRoutes = [
    // Categories
    'categories-index' => __DIR__ . '/../../api/categories/index.php',
    'categories-id'    => __DIR__ . '/../../api/categories/[id].php',
    // Brands
    'brands-index'     => __DIR__ . '/../../api/brands/index.php',
    'brands-id'        => __DIR__ . '/../../api/brands/[id].php',
    // Units
    'units-index'      => __DIR__ . '/../../api/units/index.php',
    'units-id'         => __DIR__ . '/../../api/units/[id].php',
    // Products
    'products-index'   => __DIR__ . '/../../api/products/index.php',
    'products-id'      => __DIR__ . '/../../api/products/[id].php',
    'products-barcode' => __DIR__ . '/../../api/products/barcode.php',
    // Inventory
    'inventory-index'     => __DIR__ . '/../../api/inventory/index.php',
    'inventory-summary'   => __DIR__ . '/../../api/inventory/summary.php',
    'inventory-logs'      => __DIR__ . '/../../api/inventory/logs.php',
    'inventory-branches'  => __DIR__ . '/../../api/inventory/branches.php',
];

$pageRoutes = [
    'categories'    => __DIR__ . '/../../pages/categories/index.php',
    'brands'        => __DIR__ . '/../../pages/brands/index.php',
    'units'         => __DIR__ . '/../../pages/units/index.php',
    'products'      => __DIR__ . '/../../pages/products/index.php',
    'inventory'     => __DIR__ . '/../../pages/inventory/index.php',
    'stock-logs'    => __DIR__ . '/../../pages/inventory/stock-logs.php',
];

return ['api' => $apiRoutes, 'page' => $pageRoutes];