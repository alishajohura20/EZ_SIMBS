<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];
$returns = new SaleReturn();
$role = $_SESSION['user_role'] ?? '';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $r = $returns->getById($id);
            if (!$r) jsonError('Return not found', 404);
            if ($role === 'customer' && (int)$r['return']['requested_for'] !== (int)$userId) {
                jsonError('Return not found', 404);
            }
            jsonSuccess(['return' => $r]);
        }
        jsonSuccess([
            'returns' => $returns->dbListForUser($userId, $_GET),
        ]);

    case 'POST':
        $data = getRequestBody();
        $action = $data['action'] ?? 'create';

        if ($action === 'create') {
            try {
                $result = $returns->create([
                    'type'        => 'online',
                    'order_id'    => $data['order_id'] ?? 0,
                    'items'       => $data['items'] ?? [],
                    'reason_code' => $data['reason_code'] ?? '',
                    'reason'      => $data['reason'] ?? '',
                    'note'        => $data['note'] ?? '',
                ]);
                if (!$result['success']) jsonError($result['message']);
                jsonSuccess($result, 'Return request submitted');
            } catch (Exception $e) {
                jsonError($e->getMessage());
            }
        }

        if ($action === 'cancel') {
            $id = (int)($data['return_id'] ?? 0);
            $r = $returns->getById($id);
            if (!$r || (int)$r['return']['requested_for'] !== (int)$userId) jsonError('Return not found', 404);
            if ($r['return']['status'] !== 'requested') jsonError('Only a pending request can be withdrawn');
            $db = Database::getInstance();
            $db->update('sale_returns', ['status' => 'cancelled', 'admin_note' => 'Withdrawn by customer'], 'id = ?', [$id]);
            logActivity('sale_return_withdrawn', 'sale_returns', $id, ['status' => 'requested'], ['status' => 'cancelled']);
            jsonSuccess(null, 'Request withdrawn');
        }

        jsonError('Unknown action', 400);
        break;

    default:
        jsonError('Method not allowed', 405);
}