<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$inv = new Inventory();
$summary = $inv->getStockSummary($_GET['branch_id'] ?? null);
jsonSuccess($summary);
