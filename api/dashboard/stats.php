<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$dashboard = new Dashboard();
$stats = $dashboard->getStats($_GET['branch_id'] ?? null);
jsonSuccess($stats);
