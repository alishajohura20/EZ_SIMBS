<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';
requireLogin();

$db = Database::getInstance();
$id = (int)($_GET['id'] ?? basename($_SERVER['SCRIPT_NAME'], '.php'));
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $user = $db->fetch(
            "SELECT u.id, u.name, u.email, u.phone, u.status, u.last_login, u.created_at,
                    r.name as role_name, b.name as branch_name
             FROM users u JOIN roles r ON u.role_id=r.id LEFT JOIN branches b ON u.branch_id=b.id
             WHERE u.id = ?", [$id]
        );
        if (!$user) jsonError('User not found', 404);
        jsonSuccess($user);
        break;

    case 'PUT':
        if (($_SESSION['user_role'] ?? '') !== 'admin') jsonError('Unauthorized', 403);
        $data = getRequestBody();
        $updates = [];
        if (isset($data['name'])) $updates['name'] = sanitize($data['name']);
        if (isset($data['email'])) $updates['email'] = sanitize($data['email']);
        if (isset($data['phone'])) $updates['phone'] = sanitize($data['phone']);
        if (isset($data['role_id'])) $updates['role_id'] = (int)$data['role_id'];
        if (isset($data['branch_id'])) $updates['branch_id'] = $data['branch_id'] ?: null;
        if (isset($data['status'])) $updates['status'] = $data['status'];
        if (isset($data['password']) && $data['password']) {
            $updates['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $db->update('users', $updates, 'id = ?', [$id]);
        logActivity('user_updated', 'users', $id, null, $updates);
        jsonSuccess(null, 'User updated');
        break;

    case 'DELETE':
        if (($_SESSION['user_role'] ?? '') !== 'admin') jsonError('Unauthorized', 403);
        if ($id == $_SESSION['user_id']) jsonError('Cannot delete yourself');
        $db->delete('users', 'id = ?', [$id]);
        logActivity('user_deleted', 'users', $id);
        jsonSuccess(null, 'User deleted');
        break;
}
