<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);

$reorder = new AutoReorder();
$suggestions = $reorder->getSuggestions($_GET['product_id'] ?? null);
jsonSuccess($suggestions);
