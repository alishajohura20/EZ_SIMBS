<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$data = getRequestBody();
$sale = new Sale();
$sale->hold((int)$data['sale_id']);
jsonSuccess(null, 'Sale held');
