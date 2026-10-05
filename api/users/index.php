<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';
requireLogin();

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? '';
        $statusFilter = $_GET['status'] ?? '';

        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($roleFilter) {
            $where .= " AND r.name = ?";
            $params[] = $roleFilter;
        }
        if ($statusFilter) {
            $where .= " AND u.status = ?";
            $params[] = $statusFilter;
        }

        $total = $db->fetch("SELECT COUNT(*) as t FROM users u JOIN roles r ON u.role_id=r.id WHERE {$where}", $params)['t'];
        $pagination = paginate($total, 20, $page);

        $users = $db->fetchAll(
            "SELECT u.id, u.name, u.email, u.phone, u.status, u.last_login, u.created_at,
                    r.name as role_name, b.name as branch_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE {$where}
             ORDER BY u.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        jsonSuccess(['users' => $users, 'pagination' => $pagination]);
        break;

    case 'POST':
        if (($_SESSION['user_role'] ?? '') !== 'admin') jsonError('Unauthorized', 403);
        $data = getRequestBody();
        $auth = new Auth();
        $result = $auth->register($data);
        if ($result['success']) {
            jsonSuccess(['user_id' => $result['user_id']], 'User created');
        } else {
            jsonError($result['message']);
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}
