<?php
/**
 * Purchasing Module Routes
 * Maps to existing API endpoints and pages - zero code impact
 */

$apiRoutes = [
    // Suppliers
    'suppliers-index'  => __DIR__ . '/../../api/suppliers/index.php',
    'suppliers-id'     => __DIR__ . '/../../api/suppliers/[id].php',
    'suppliers-balance'=> __DIR__ . '/../../api/suppliers/balance.php',
    // Purchases
    'purchases-index'  => __DIR__ . '/../../api/purchases/index.php',
    'purchases-id'     => __DIR__ . '/../../api/purchases/[id].php',
    'purchases-status' => __DIR__ . '/../../api/purchases/status.php',
    'purchases-payment'=> __DIR__ . '/../../api/purchases/payment.php',
    // Purchase Returns
    'purchase-returns-index'    => __DIR__ . '/../../api/purchase-returns/index.php',
    'purchase-returns-returnable'=> __DIR__ . '/../../api/purchase-returns/returnable.php',
    'purchase-returns-approve'  => __DIR__ . '/../../api/purchase-returns/approve.php',
    'purchase-returns-receive'  => __DIR__ . '/../../api/purchase-returns/receive.php',
    'purchase-returns-credit'   => __DIR__ . '/../../api/purchase-returns/credit.php',
    'purchase-returns-void'     => __DIR__ . '/../../api/purchase-returns/void.php',
];

$pageRoutes = [
    'suppliers'        => __DIR__ . '/../../pages/suppliers/index.php',
    'purchases'        => __DIR__ . '/../../pages/purchases/index.php',
    'purchases-create' => __DIR__ . '/../../pages/purchases/create.php',
    'purchases-view'   => __DIR__ . '/../../pages/purchases/view.php',
    'purchase-returns' => __DIR__ . '/../../pages/purchase-returns/index.php',
    'purchase-returns-view' => __DIR__ . '/../../pages/purchase-returns/view.php',
];

return ['api' => $apiRoutes, 'page' => $pageRoutes];