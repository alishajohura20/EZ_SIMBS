<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Store.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $store = new Store();
        $result = $store->register(getRequestBody());
        if ($result['success']) {
            jsonSuccess([
                'store_id' => $result['store_id'],
                'user_id'  => $result['user_id'],
            ], 'Store created successfully');
        } else {
            jsonError($result['message'], 400);
        }
        break;
    default:
        jsonError('Method not allowed', 405);
}
