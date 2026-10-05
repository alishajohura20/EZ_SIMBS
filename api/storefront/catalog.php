<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

$sf = new Storefront();
$userId = $_SESSION['user_id'] ?? null;

$result = $sf->searchProducts($_GET, $userId);
jsonSuccess($result);