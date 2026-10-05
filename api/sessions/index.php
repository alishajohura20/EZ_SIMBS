<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sm = new SessionManager();
$method = $_SERVER['REQUEST_METHOD'];
$isAdmin = in_array($_SESSION['user_role'] ?? '', ['admin', 'manager']);

if ($method === 'GET') {
    // Admin can request a system-wide overview with ?all=1
    if ($isAdmin && !empty($_GET['all'])) {
        $sessions = $sm->getAllSessions([
            'search' => $_GET['search'] ?? '',
            'role' => $_GET['role'] ?? '',
        ]);
        $myToken = $_SESSION['session_token'] ?? '';
        $usersWithSessions = [];
        foreach ($sessions as $key => $s) {
            $sessions[$key]['is_current'] = ($s['token'] === $myToken);
            $usersWithSessions[$s['user_id']] = true;
        }
        jsonSuccess([
            'sessions' => $sessions,
            'total' => count($sessions),
            'active_users' => count($usersWithSessions),
            'is_admin_view' => true,
        ]);
    }

    $sessions = $sm->getActiveSessions($_SESSION['user_id']);
    $myToken = $_SESSION['session_token'] ?? '';
    foreach ($sessions as $key => $s) {
        $sessions[$key]['is_current'] = ($s['token'] === $myToken);
    }
    jsonSuccess([
        'sessions' => $sessions,
        'total' => count($sessions),
        'is_admin_view' => false,
    ]);
}

if ($method === 'POST') {
    $data = getRequestBody();
    $action = $data['action'] ?? '';
    $currentToken = $_SESSION['session_token'] ?? '';

    switch ($action) {
        case 'terminate':
            $id = (int)($data['id'] ?? 0);
            if ($id <= 0) jsonError('Session id required', 400);

            $session = $sm->getById($id);
            if (!$session) jsonError('Session not found', 404);

            // Non-admins may only revoke their own sessions
            if (!$isAdmin && (int)$session['user_id'] !== (int)$_SESSION['user_id']) {
                jsonError('Unauthorized', 403);
            }

            $sm->terminate($id);
            logActivity('session_terminated', 'user_sessions', $id, ['user_id' => $session['user_id']]);

            // If the admin fired their own current session, end the login
            if ($session['token'] === $currentToken) {
                session_unset();
                session_destroy();
                jsonSuccess(['logged_out' => true], 'Current session terminated');
            }
            jsonSuccess(null, 'Session terminated');
            break;

        case 'terminate_others':
            $sm->terminateOthers($currentToken, $_SESSION['user_id']);
            logActivity('session_terminate_others', 'user_sessions', $_SESSION['user_id']);
            jsonSuccess(['revoked' => true], 'All other sessions ended');
            break;

        case 'terminate_all':
            if (!$isAdmin) jsonError('Unauthorized', 403);
            $userId = (int)($data['user_id'] ?? 0);
            if ($userId <= 0) jsonError('User id required', 400);

            $sm->terminateAllForUser($userId);
            logActivity('session_terminate_all', 'users', $userId);

            // If terminating ourselves, end the login
            if ($userId === (int)$_SESSION['user_id']) {
                session_unset();
                session_destroy();
                jsonSuccess(['logged_out' => true], 'All sessions ended');
            }
            jsonSuccess(null, 'All sessions for user ended');
            break;

        default:
            jsonError('Unknown action', 400);
    }
}

jsonError('Method not allowed', 405);