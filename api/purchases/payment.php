<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$data = getRequestBody();
$purchase = new Purchase();
$result = $purchase->addPayment(
    (int)$data['purchase_id'],
    (float)$data['amount'],
    $data['payment_method'] ?? 'cash',
    $data['reference'] ?? '',
    $data['notes'] ?? ''
);
if ($result['success']) jsonSuccess(null, 'Payment added');
else jsonError($result['message']);
