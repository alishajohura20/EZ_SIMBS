<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin']);

$logger = new ActivityLog();
jsonSuccess($logger->getStats());
