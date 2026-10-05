<?php
/**
 * Sales Module Routes
 * Maps to existing API endpoints and pages - zero code impact
 */

$apiRoutes = [
    // Sales / POS
    'sales-index'  => __DIR__ . '/../../api/sales/index.php',
    'sales-id'     => __DIR__ . '/../../api/sales/[id].php',
    'sales-hold'   => __DIR__ . '/../../api/sales/hold.php',
    // Invoices
    'invoices-index' => __DIR__ . '/../../api/invoices/index.php',
    // Returns (Customer)
    'returns-index'      => __DIR__ . '/../../api/returns/index.php',
    'returns-returnable' => __DIR__ . '/../../api/returns/returnable.php',
    'returns-approve'    => __DIR__ . '/../../api/returns/approve.php',
    'returns-receive'    => __DIR__ . '/../../api/returns/receive.php',
    'returns-refund'     => __DIR__ . '/../../api/returns/refund.php',
    'returns-void'       => __DIR__ . '/../../api/returns/void.php',
    'returns-photos'     => __DIR__ . '/../../api/returns/photos.php',
    'returns-exchange'   => __DIR__ . '/../../api/returns/exchange.php',
    // Storefront (Customer-facing)
    'storefront-index'  => __DIR__ . '/../../api/storefront/index.php',
    'storefront-catalog'=> __DIR__ . '/../../api/storefront/catalog.php',
    'storefront-product'=> __DIR__ . '/../../api/storefront/product.php',
    'storefront-cart'   => __DIR__ . '/../../api/storefront/cart.php',
    'storefront-wishlist'=> __DIR__ . '/../../api/storefront/wishlist.php',
    'storefront-orders' => __DIR__ . '/../../api/storefront/orders.php',
    'storefront-reviews'=> __DIR__ . '/../../api/storefront/reviews.php',
    'storefront-returns'=> __DIR__ . '/../../api/storefront/returns.php',
];

$pageRoutes = [
    'sales'           => __DIR__ . '/../../pages/sales/index.php',
    'pos'             => __DIR__ . '/../../pages/sales/pos.php',
    'receipt'         => __DIR__ . '/../../pages/sales/receipt.php',
    'invoices'        => __DIR__ . '/../../pages/invoices/index.php',
    'invoices-print'  => __DIR__ . '/../../pages/invoices/print.php',
    'returns'         => __DIR__ . '/../../pages/returns/index.php',
    'returns-view'    => __DIR__ . '/../../pages/returns/view.php',
    // Customer pages (storefront)
    'customer-dashboard' => __DIR__ . '/../../pages/customer/dashboard.php',
    'customer-cart'      => __DIR__ . '/../../pages/customer/cart.php',
    'customer-wishlist'  => __DIR__ . '/../../pages/customer/wishlist.php',
    'customer-orders'    => __DIR__ . '/../../pages/customer/orders.php',
    'customer-profile'   => __DIR__ . '/../../pages/customer/profile.php',
    'customer-returns'   => __DIR__ . '/../../pages/customer/returns.php',
];

return ['api' => $apiRoutes, 'page' => $pageRoutes];