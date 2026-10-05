<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$invoice = new Invoice();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    jsonSuccess($invoice->getAll($_GET));
} else {
    jsonError('Method not allowed', 405);
}
