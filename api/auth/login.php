<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = getRequestBody();
        $auth = new Auth();
        $result = $auth->login(
            $data['email'] ?? '',
            $data['password'] ?? '',
            !empty($data['remember']),
            !empty($data['role']) ? strtolower(trim($data['role'])) : null
        );
        if ($result['success']) {
            jsonSuccess(['role' => $result['role']], 'Login successful');
        } else {
            jsonError($result['message'], 401);
        }
        break;
    default:
        jsonError('Method not allowed', 405);
}