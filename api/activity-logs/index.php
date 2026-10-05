<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);

$logger = new ActivityLog();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $logger->getAll($_GET);
    jsonSuccess($result);
}
