<?php
/**
 * Reporting Module Routes (Team Lead owned)
 * Maps to existing API endpoints and pages - zero code impact
 */

$apiRoutes = [
    // Dashboard
    'dashboard-stats'  => __DIR__ . '/../../api/dashboard/stats.php',
    'dashboard-charts' => __DIR__ . '/../../api/dashboard/charts.php',
    // Reports
    'reports-sales'           => __DIR__ . '/../../api/reports/sales.php',
    'reports-inventory'       => __DIR__ . '/../../api/reports/inventory.php',
    'reports-profit-loss'     => __DIR__ . '/../../api/reports/profit-loss.php',
    'reports-purchases'       => __DIR__ . '/../../api/reports/purchases.php',
    'reports-customers'       => __DIR__ . '/../../api/reports/customers.php',
    'reports-returns'         => __DIR__ . '/../../api/reports/returns.php',
    'reports-purchase-returns'=> __DIR__ . '/../../api/reports/purchase-returns.php',
];

$pageRoutes = [
    'dashboard' => __DIR__ . '/../../pages/dashboard/index.php',
    'reports'   => __DIR__ . '/../../pages/reports/index.php',
];

return ['api' => $apiRoutes, 'page' => $pageRoutes];