<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$settings = new Settings();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['group'])) {
        jsonSuccess($settings->getAll($_GET['group']));
    } else {
        jsonSuccess($settings->getGrouped());
    }
} elseif ($method === 'POST') {
    if (($_SESSION['user_role'] ?? '') !== 'admin') jsonError('Unauthorized', 403);
    $data = getRequestBody();
    $action = $data['action'] ?? '';

    switch ($action) {
        case 'save':
            $settings->saveBulk($data['settings'] ?? [], $data['group'] ?? 'general');
            jsonSuccess(null, 'Settings saved');
            break;
        case 'set_theme':
            $_SESSION['theme'] = $data['theme'] ?? 'light';
            $settings->set('theme', $data['theme'] ?? 'light');
            jsonSuccess(null, 'Theme updated');
            break;
        case 'backup':
            $result = $settings->backup();
            if ($result['success']) jsonSuccess(['file' => $result['file']], 'Backup created');
            else jsonError($result['message']);
            break;
        case 'restore':
            $filepath = BACKUP_PATH . '/' . basename($data['file']);
            $result = $settings->restore($filepath);
            if ($result['success']) jsonSuccess(null, 'Database restored');
            else jsonError($result['message']);
            break;
        case 'get_backups':
            jsonSuccess($settings->getBackups());
            break;
        default:
            jsonError('Invalid action');
    }
}
