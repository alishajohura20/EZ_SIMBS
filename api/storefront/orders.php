<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sf = new Storefront();
$userId = $_SESSION['user_id'];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $data = getRequestBody();
        $action = $data['action'] ?? 'create';
        if ($action === 'validate_coupon') {
            $cart = $sf->getCart($userId, false);
            $subtotal = 0;
            $count = $sf->getCart($userId, true);
            foreach ($count['items'] as $i) $subtotal += $i['qty'] * $i['price'];
            $result = $sf->validateCoupon($data['code'] ?? '', $subtotal);
            if ($result['success']) {
                jsonSuccess(['discount' => $result['discount'], 'coupon' => $result['coupon']]);
            } else {
                jsonError($result['message']);
            }
            break;
        }
        if ($action === 'create') {
            $cartData = $sf->getCart($userId, true);
            if (empty($cartData['items'])) {
                jsonError('Your cart is empty');
                break;
            }
            $result = $sf->createOrder($userId, $data, $cartData['items']);
            if ($result['success']) {
                jsonSuccess(['order_id' => $result['order_id'], 'order_no' => $result['order_no'], 'grand_total' => $result['grand_total']], 'Order placed successfully');
            } else {
                jsonError($result['message']);
            }
            break;
        }
        jsonError('Unknown action', 400);
        break;

    case 'GET':
        $orderId = (int)($_GET['id'] ?? 0);
        if ($orderId > 0) {
            $order = $sf->getOrder($orderId, $userId);
            if (!$order) jsonError('Order not found', 404);
            $payload = ['order' => $order];
            if (isset($_GET['returnable']) && (int)$_GET['returnable'] === 1) {
                $payload['returnable'] = $sf->getOrderReturnable($orderId, $userId);
            }
            jsonSuccess($payload);
        } else {
            jsonSuccess($sf->getOrders($userId, $_GET));
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}