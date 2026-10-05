<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$dashboard = new Dashboard();
$period = $_GET['period'] ?? 'monthly';

jsonSuccess([
    'sales_chart' => $dashboard->getSalesChart($period),
    'top_products' => $dashboard->getTopProducts(),
    'recent_sales' => $dashboard->getRecentSales(),
    'low_stock' => $dashboard->getLowStockProducts(),
]);
