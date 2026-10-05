<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);

$data = getRequestBody();
$id = (int)($data['return_id'] ?? 0);
$action = $data['action'] ?? '';
if (!$id) jsonError('Return id is required');

$returns = new PurchaseReturn();
$result = $action === 'approve' ? $returns->approve($id, $data['note'] ?? '')
                                : $returns->reject($id, $data['note'] ?? '');

if ($result['success']) jsonSuccess(null, 'Return ' . $action . 'd');
else jsonError($result['message']);