<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$sf = new Storefront();
$userId = $_SESSION['user_id'];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $cart = $sf->getCart($userId, true);
        $count = $sf->getCart($userId, false);
        $totalCount = 0;
        foreach ($count as $ci) $totalCount += $ci['qty'];
        jsonSuccess(['items' => $cart['items'], 'count' => $totalCount, 'subtotal' => $cart['subtotal'], 'cart_id' => $cart['cart_id'] ?? null]);
        break;

    case 'POST':
        $data = getRequestBody();
        $action = $data['action'] ?? 'add';
        switch ($action) {
            case 'add':
                $result = $sf->addToCart($userId, $data['product_id'] ?? 0, $data['qty'] ?? 1);
                if ($result['success']) jsonSuccess(['cart' => $result['cart']]);
                else jsonError($result['message']);
                break;
            case 'update':
                $result = $sf->updateCartItem($userId, $data['cart_item_id'] ?? 0, $data['qty'] ?? 1);
                if ($result['success']) jsonSuccess(['cart' => $result['cart']]);
                else jsonError($result['message']);
                break;
            case 'remove':
                $result = $sf->removeFromCart($userId, $data['cart_item_id'] ?? 0);
                if ($result['success']) jsonSuccess(['cart' => $result['cart']]);
                else jsonError($result['message']);
                break;
            case 'clear':
                $sf->clearCart($userId);
                jsonSuccess(null, 'Cart cleared');
                break;
            default:
                jsonError('Unknown action', 400);
        }
        break;

    default:
        jsonError('Method not allowed', 405);
}