<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$saleId = (int)($_GET['sale_id'] ?? 0);
if (!$saleId) jsonError('Sale id is required');

$result = (new SaleReturn())->getReturnableForSale($saleId);
if (!$result) jsonError('Sale not found', 404);
jsonSuccess($result);