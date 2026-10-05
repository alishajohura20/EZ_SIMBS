<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$purchaseId = (int)($_GET['purchase_id'] ?? 0);
if (!$purchaseId) jsonError('Purchase id is required');

$result = (new PurchaseReturn())->getReturnableForPurchase($purchaseId);
if (!$result) jsonError('Purchase not found', 404);
jsonSuccess($result);