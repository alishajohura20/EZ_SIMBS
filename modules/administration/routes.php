<?php
/**
 * Administration Module Routes (Team Lead owned)
 * Maps to existing API endpoints and pages - zero code impact
 */

$apiRoutes = [
    // Branches
    'branches-index'      => __DIR__ . '/../../api/branches/index.php',
    'branches-inventory'  => __DIR__ . '/../../api/branches/inventory.php',
    'branches-suggestions'=> __DIR__ . '/../../api/branches/suggestions.php',
    // Settings
    'settings-index' => __DIR__ . '/../../api/settings/index.php',
    // Activity Logs
    'activity-logs-index' => __DIR__ . '/../../api/activity-logs/index.php',
    'activity-logs-stats' => __DIR__ . '/../../api/activity-logs/stats.php',
    // Notifications
    'notifications-index'       => __DIR__ . '/../../api/notifications/index.php',
    'notifications-check'       => __DIR__ . '/../../api/notifications/check.php',
    'notifications-reorder'     => __DIR__ . '/../../api/notifications/reorder-suggestions.php',
    // Banners
    'banners-index' => __DIR__ . '/../../api/banners/index.php',
];

$pageRoutes = [
    'branches'          => __DIR__ . '/../../pages/branches/index.php',
    'branches-view'     => __DIR__ . '/../../pages/branches/view.php',
    'settings'          => __DIR__ . '/../../pages/settings/index.php',
    'activity-log'      => __DIR__ . '/../../pages/activity-log/index.php',
    'notifications'     => __DIR__ . '/../../pages/notifications/index.php',
    'reorder-suggestions'=> __DIR__ . '/../../pages/notifications/reorder-suggestions.php',
    'roles'             => __DIR__ . '/../../pages/store/roles.php',
    'store-index'       => __DIR__ . '/../../pages/store/index.php',
    'store-confirmation'=> __DIR__ . '/../../pages/store/confirmation.php',
    // Role dashboards
    'role-admin'        => __DIR__ . '/../../pages/roles/admin.php',
    'role-manager'      => __DIR__ . '/../../pages/roles/manager.php',
    'role-branch-manager'=> __DIR__ . '/../../pages/roles/branch-manager.php',
    'role-cashier'      => __DIR__ . '/../../pages/roles/cashier.php',
];

return ['api' => $apiRoutes, 'page' => $pageRoutes];