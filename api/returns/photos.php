<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$returnId = (int)($_GET['return_id'] ?? 0);
$itemId   = isset($_GET['return_item_id']) ? (int)$_GET['return_item_id'] : null;
$kind     = ($_GET['kind'] ?? 'sale') === 'purchase' ? 'purchase' : 'sale';
if (!$returnId) jsonError('Return id is required');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photos = new ReturnPhoto();
    $saved = $photos->addMany($kind, $returnId, $itemId, $_FILES['photos'] ?? [], $_SESSION['user_id'] ?? null);
    if (!$saved) jsonError('No valid images uploaded (jpg, png, webp; max 4 MB each; max 5 per request)');
    jsonSuccess(['photos' => $saved], count($saved) . ' photo(s) uploaded');

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $photoId = (int)($_GET['id'] ?? 0);
    if (!$photoId) jsonError('Photo id is required');
    $ok = (new ReturnPhoto())->delete($photoId);
    jsonSuccess(null, $ok ? 'Photo deleted' : 'Photo not found');

} else {
    jsonSuccess(['photos' => (new ReturnPhoto())->getFor($kind, $returnId)]);
}