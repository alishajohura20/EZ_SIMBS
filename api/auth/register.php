<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = getRequestBody();
        $auth = new Auth();
        $result = $auth->registerPublic($data);
        if ($result['success']) {
            jsonSuccess(['user_id' => $result['user_id'], 'role' => $result['role'] ?? 'customer'], 'Registration successful');
        } else {
            jsonError($result['message'], 400);
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}