<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$notif = new Notification();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $unreadOnly = isset($_GET['unread']) && $_GET['unread'] === '1';
    $notifications = $notif->getForUser($userId, 50, $unreadOnly);
    $unreadCount = $notif->getUnreadCount($userId);
    jsonSuccess(['notifications' => $notifications, 'unread_count' => $unreadCount]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getRequestBody();
    $action = $data['action'] ?? '';

    if ($action === 'read') {
        $notif->markRead((int)$data['id'], $userId);
        jsonSuccess(null, 'Marked as read');
    } elseif ($action === 'read_all') {
        $notif->markAllRead($userId);
        jsonSuccess(null, 'All marked as read');
    } elseif ($action === 'delete') {
        $notif->delete((int)$data['id'], $userId);
        jsonSuccess(null, 'Deleted');
    } elseif ($action === 'delete_all') {
        $notif->deleteAll($userId);
        jsonSuccess(null, 'All deleted');
    } else {
        jsonError('Invalid action');
    }
}
