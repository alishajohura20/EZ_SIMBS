<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$branch = new Branch();
$id = (int)($_GET['branch_id'] ?? 1);
$inventory = $branch->getBranchInventory($id, $_GET);
jsonSuccess($inventory);
