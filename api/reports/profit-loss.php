<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$report = new Report();
$result = $report->profitLossReport($_GET);
jsonSuccess($result);
