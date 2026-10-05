<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$customer = new Customer();
$phone = $_GET['phone'] ?? '';

if (!$phone) jsonError('Phone required');

$c = $customer->getByPhone($phone);
if ($c) {
    jsonSuccess($c);
} else {
    jsonSuccess(null, 'No customer found');
}
