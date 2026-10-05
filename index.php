<?php
require_once __DIR__ . '/app/core/Bootstrap.php';

$router = \App\Core\Bootstrap::init();

require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $role = $_SESSION['user_role'] ?? '';
    if (in_array($role, ['admin', 'manager', 'branch_manager', 'cashier'])) {
        redirect(APP_URL . '/pages/store/');
    }
    redirect(APP_URL . '/pages/customer/dashboard.php');
} else {
    redirect(APP_URL . '/landing.php');
}
