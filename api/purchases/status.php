<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$data = getRequestBody();
$purchase = new Purchase();
$result = $purchase->updateStatus((int)$data['purchase_id'], $data['status']);
if ($result['success']) jsonSuccess(null, 'Status updated');
else jsonError($result['message']);
