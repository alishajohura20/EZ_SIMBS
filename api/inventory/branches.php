<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$db = Database::getInstance();
$branches = $db->fetchAll(
    "SELECT b.id, b.name FROM branches b
     WHERE b.status = 'active' AND b.store_id = (SELECT store_id FROM users WHERE id = ?)
     ORDER BY b.name",
    [$_SESSION['user_id'] ?? 0]
);
if (!$branches) {
    $branches = $db->fetchAll("SELECT id, name FROM branches WHERE status = 'active' ORDER BY name");
}
jsonSuccess($branches);
