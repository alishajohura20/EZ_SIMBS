<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$branch = new Branch();
$id = (int)($_GET['branch_id'] ?? 1);
$suggestions = $branch->getTransferSuggestions($id);
jsonSuccess($suggestions);
