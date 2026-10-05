<?php
class Storefront {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private function paginate($totalItems, $perPage, $currentPage) {
        $totalPages = ceil($totalItems / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;
        return [
            'total' => $totalItems,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'offset' => $offset,
        ];
    }

    // ---------- Banners ----------
    public function getBanners($position = 'hero', $limit = 5) {
        return $this->db->fetchAll(
            "SELECT * FROM banners WHERE status = 'active' AND position = ?
             ORDER BY sort_order ASC, id ASC LIMIT {$limit}",
            [$position]
        );
    }

    public function getProductBanners() {
        return $this->db->fetchAll(
            "SELECT * FROM banners WHERE status = 'active' AND position != 'hero'
             ORDER BY sort_order ASC, id ASC LIMIT 4"
        );
    }

    // ---------- Catalog browsing ----------
    public function getCategories() {
        return $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'active') as product_count
             FROM categories c WHERE c.status = 'active' ORDER BY c.name ASC"
        );
    }

    public function getFeaturedProducts($limit = 12, $userId = null) {
        $wishlistSql = $userId 
            ? "(SELECT COUNT(*) FROM wishlist WHERE user_id = {$userId} AND product_id = p.id) > 0 as in_wishlist"
            : "0 as in_wishlist";
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, b.name as brand_name,
                    (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id = p.id) as total_stock,
                    (SELECT ROUND(AVG(rating),1) FROM product_reviews WHERE product_id = p.id AND status='approved') as avg_rating,
                    (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id AND status='approved') as review_count,
                    {$wishlistSql}
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.status = 'active'
             ORDER BY p.created_at DESC
             LIMIT {$limit}"
        );
    }

    public function searchProducts($filters = [], $userId = null) {
        $where = "p.status = 'active'";
        $params = [];

        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $words = preg_split('/\s+/', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
            
            $conditions = [];
            $wordParams = [];
            
            foreach ($words as $word) {
                $s = "%{$word}%";
                $conditions[] = "(p.name LIKE ? OR p.barcode LIKE ? OR p.sku LIKE ? 
                    OR SOUNDEX(c.name) = SOUNDEX(?) 
                    OR SOUNDEX(b.name) = SOUNDEX(?)
                    OR c.name LIKE CONCAT('% ', ?, ' %') 
                    OR c.name LIKE CONCAT(?, ' %')
                    OR c.name LIKE CONCAT('% ', ?)
                    OR b.name LIKE CONCAT('% ', ?, ' %')
                    OR b.name LIKE CONCAT(?, ' %')
                    OR b.name LIKE CONCAT('% ', ?))";
                $wordParams = array_merge($wordParams, [$s, $s, $s, $word, $word, $word, $word, $word, $word, $word, $word]);
            }
            
            if ($conditions) {
                $where .= " AND (" . implode(" OR ", $conditions) . ")";
                $params = array_merge($params, $wordParams);
            }
        }
        if (!empty($filters['category_id'])) {
            $where .= " AND p.category_id = ?";
            $params[] = (int)$filters['category_id'];
        }
        if (!empty($filters['min_price'])) {
            $where .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $perPage = (int)($filters['per_page'] ?? 24);
        $pagination = $this->paginate($total, $perPage, $page);
        $orderBy = $filters['sort'] ?? 'newest';

        $sortSql = [
            'newest' => 'p.created_at DESC',
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name' => 'p.name ASC',
        ][$orderBy] ?? 'p.created_at DESC';

        $wishlistSql = $userId 
            ? "(SELECT COUNT(*) FROM wishlist WHERE user_id = {$userId} AND product_id = p.id) > 0 as in_wishlist"
            : "0 as in_wishlist";

        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, b.name as brand_name,
                    (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id = p.id) as total_stock,
                    (SELECT ROUND(AVG(rating),1) FROM product_reviews WHERE product_id = p.id AND status='approved') as avg_rating,
                    (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id AND status='approved') as review_count,
                    {$wishlistSql}
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE {$where}
             ORDER BY {$sortSql}
             LIMIT {$perPage} OFFSET {$pagination['offset']}",
            $params
        );

        return ['products' => $products, 'pagination' => $pagination];
    }

    public function getProduct($id) {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name, b.name as brand_name, u.name as unit_name,
                    (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id = p.id) as total_stock,
                    (SELECT ROUND(AVG(rating),1) FROM product_reviews WHERE product_id = p.id AND status='approved') as avg_rating,
                    (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id AND status='approved') as review_count
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN units u ON p.unit_id = u.id
             WHERE p.id = ? AND p.status = 'active'",
            [$id]
        );
    }

    public function getProductGallery($productId) {
        return $this->db->fetchAll(
            "SELECT image FROM product_gallery WHERE product_id = ? ORDER BY sort_order ASC, id ASC",
            [$productId]
        );
    }

    public function getProductReviews($productId, $approvedOnly = true) {
        $where = 'r.product_id = ?';
        $params = [$productId];
        if ($approvedOnly) {
            $where .= " AND r.status = 'approved'";
        }
        return $this->db->fetchAll(
            "SELECT r.*, u.name as user_name FROM product_reviews r
             JOIN users u ON r.user_id = u.id
             WHERE {$where} ORDER BY r.created_at DESC",
            $params
        );
    }

    public function getRatingSummary($productId) {
        $row = $this->db->fetch(
            "SELECT COUNT(*) as total, ROUND(AVG(rating),2) as average
             FROM product_reviews WHERE product_id = ? AND status='approved'",
            [$productId]
        );
        $dist = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
        foreach ($this->db->fetchAll(
            "SELECT rating, COUNT(*) as cnt FROM product_reviews WHERE product_id = ? AND status='approved' GROUP BY rating",
            [$productId]
        ) as $r) {
            $dist[(string)$r['rating']] = (int)$r['cnt'];
        }
        return [
            'total' => (int)($row['total'] ?? 0),
            'average' => (float)($row['average'] ?? 0),
            'distribution' => $dist,
        ];
    }

    public function addReview($productId, $userId, $rating, $title, $comment) {
        try {
            return $this->db->insert('product_reviews', [
                'product_id' => (int)$productId,
                'user_id' => (int)$userId,
                'rating' => max(1, min(5, (int)$rating)),
                'title' => sanitize($title ?? ''),
                'comment' => sanitize($comment ?? ''),
                'status' => 'approved',
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    // ---------- Wishlist ----------
    public function getWishlist($userId, $includeProducts = false) {
        if (!$includeProducts) {
            return $this->db->fetchAll("SELECT product_id FROM wishlist WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
        }
        return $this->db->fetchAll(
            "SELECT w.id as wishlist_id, p.*, 1 as in_wishlist,
                    (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id = p.id) as total_stock,
                    (SELECT ROUND(AVG(rating),1) FROM product_reviews WHERE product_id = p.id AND status='approved') as avg_rating
             FROM wishlist w
             JOIN products p ON w.product_id = p.id
             WHERE w.user_id = ? AND p.status = 'active'
             ORDER BY w.created_at DESC",
            [$userId]
        );
    }

    public function isInWishlist($userId, $productId) {
        return (bool)$this->db->fetch(
            "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
    }

    public function toggleWishlist($userId, $productId) {
        if ($this->isInWishlist($userId, $productId)) {
            $this->db->delete('wishlist', 'user_id = ? AND product_id = ?', [$userId, $productId]);
            return ['added' => false];
        }
        $this->db->insert('wishlist', ['user_id' => (int)$userId, 'product_id' => (int)$productId]);
        return ['added' => true];
    }

    // ---------- Cart ----------
    private function getOrCreateCart($userId) {
        $cart = $this->db->fetch("SELECT id FROM cart WHERE user_id = ?", [$userId]);
        if ($cart) return (int)$cart['id'];
        return (int)$this->db->insert('cart', ['user_id' => (int)$userId]);
    }

    public function getCart($userId, $includeProducts = false) {
        $cartId = $this->db->fetch("SELECT id FROM cart WHERE user_id = ?", [$userId]);
        if (!$cartId) return ['items' => [], 'count' => 0, 'subtotal' => 0];
        $cartId = (int)$cartId['id'];

        if (!$includeProducts) {
            return $this->db->fetchAll("SELECT product_id, qty FROM cart_items WHERE cart_id = ?", [$cartId]);
        }
        $items = $this->db->fetchAll(
            "SELECT ci.id as cart_item_id, ci.qty, p.id as product_id, p.*,
                    (SELECT IFNULL(SUM(qty),0) FROM inventory WHERE product_id = p.id) as total_stock
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = ? AND p.status = 'active'
             ORDER BY ci.added_at DESC",
            [$cartId]
        );
        $subtotal = 0;
        foreach ($items as $i) $subtotal += $i['qty'] * $i['price'];
        $count = (int)$this->db->fetch(
            "SELECT IFNULL(SUM(qty),0) as c FROM cart_items WHERE cart_id = ?", [$cartId]
        )['c'];

        return ['items' => $items, 'count' => $count, 'subtotal' => $subtotal, 'cart_id' => $cartId];
    }

    public function addToCart($userId, $productId, $qty = 1) {
        $cartId = $this->getOrCreateCart($userId);
        $product = $this->db->fetch("SELECT * FROM products WHERE id = ? AND status='active'", [$productId]);
        if (!$product) return ['success' => false, 'message' => 'Product not available'];
        $qty = max(1, (int)$qty);

        $existing = $this->db->fetch(
            "SELECT id, qty FROM cart_items WHERE cart_id = ? AND product_id = ?",
            [$cartId, $productId]
        );
        if ($existing) {
            $this->db->update('cart_items', ['qty' => $existing['qty'] + $qty], 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('cart_items', ['cart_id' => $cartId, 'product_id' => (int)$productId, 'qty' => $qty]);
        }
        $this->db->update('cart', ['updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$cartId]);
        return ['success' => true, 'cart' => $this->getCart($userId, true)];
    }

    public function updateCartItem($userId, $cartItemId, $qty) {
        $cartId = $this->db->fetch("SELECT id FROM cart WHERE user_id = ?", [$userId]);
        if (!$cartId) return ['success' => false, 'message' => 'Cart not found'];
        $cartId = (int)$cartId['id'];
        $qty = max(0, (int)$qty);
        if ($qty === 0) {
            $this->db->delete('cart_items', 'id = ? AND cart_id = ?', [$cartItemId, $cartId]);
        } else {
            $this->db->update('cart_items', ['qty' => $qty], 'id = ? AND cart_id = ?', [$cartItemId, $cartId]);
        }
        return ['success' => true, 'cart' => $this->getCart($userId, true)];
    }

    public function removeFromCart($userId, $cartItemId) {
        $cartId = $this->db->fetch("SELECT id FROM cart WHERE user_id = ?", [$userId]);
        if ($cartId) {
            $this->db->delete('cart_items', 'id = ? AND cart_id = ?', [$cartItemId, (int)$cartId['id']]);
        }
        return ['success' => true, 'cart' => $this->getCart($userId, true)];
    }

    public function clearCart($userId) {
        $cartId = $this->db->fetch("SELECT id FROM cart WHERE user_id = ?", [$userId]);
        if ($cartId) {
            $this->db->delete('cart_items', 'cart_id = ?', [(int)$cartId['id']]);
        }
    }

    // ---------- Coupons ----------
    public function getValidCoupons() {
        return $this->db->fetchAll(
            "SELECT * FROM coupons
             WHERE status = 'active'
               AND (max_uses = 0 OR used_count < max_uses)
               AND (expires_at IS NULL OR expires_at > NOW())
               AND (starts_at IS NULL OR starts_at <= NOW())
             ORDER BY value DESC"
        );
    }

    public function validateCoupon($code, $subtotal) {
        $coupon = $this->db->fetch(
            "SELECT * FROM coupons WHERE code = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())",
            [trim($code)]
        );
        if (!$coupon) return ['success' => false, 'message' => 'Invalid or expired coupon code'];
        if ($coupon['max_uses'] > 0 && $coupon['used_count'] >= $coupon['max_uses']) {
            return ['success' => false, 'message' => 'This coupon has reached its usage limit'];
        }
        if ($subtotal < $coupon['min_order']) {
            return ['success' => false, 'message' => "Minimum order amount is " . formatCurrency($coupon['min_order'])];
        }
        $discount = $coupon['type'] === 'percent'
            ? round($subtotal * $coupon['value'] / 100, 2)
            : min($coupon['value'], $subtotal);
        return ['success' => true, 'coupon' => $coupon, 'discount' => $discount];
    }

    // ---------- Orders ----------
    public function createOrder($userId, $data, $cartItems) {
        $this->db->getConnection()->beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }

            $discount = 0;
            $couponCode = null;
            if (!empty($data['coupon_code'])) {
                $res = $this->validateCoupon($data['coupon_code'], $subtotal);
                if ($res['success']) {
                    $discount = $res['discount'];
                    $couponCode = $data['coupon_code'];
                    $this->db->update('coupons', ['used_count' => $res['coupon']['used_count'] + 1], 'id = ?', [$res['coupon']['id']]);
                }
            }

            $taxRate = (float)($this->db->fetch("SELECT setting_value FROM settings WHERE setting_key='tax_rate'")['setting_value'] ?? 0);
            $tax = round(($subtotal - $discount) * $taxRate / 100, 2);
            $shipping = (float)($data['shipping'] ?? 0);
            $grandTotal = round($subtotal - $discount + $tax + $shipping, 2);

            $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);

            $customerId = null;
            $customer = $this->db->fetch(
                "SELECT id FROM customers WHERE email = ? OR phone = ? ORDER BY id ASC LIMIT 1",
                [$user['email'], $user['phone'] ?? '']
            );
            if ($customer) {
                $customerId = (int)$customer['id'];
            }

            $orderNo = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            while ($this->db->fetch("SELECT id FROM orders WHERE order_no = ?", [$orderNo])) {
                $orderNo = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }

            $orderId = $this->db->insert('orders', [
                'order_no' => $orderNo,
                'user_id' => (int)$userId,
                'customer_id' => $customerId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $couponCode,
                'tax' => $tax,
                'shipping' => $shipping,
                'grand_total' => $grandTotal,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'status' => 'pending',
                'shipping_address' => sanitize($data['shipping_address'] ?? ''),
                'contact_phone' => sanitize($data['contact_phone'] ?? ''),
                'notes' => sanitize($data['notes'] ?? ''),
                'is_pos' => 0,
            ]);

            foreach ($cartItems as $item) {
                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => (int)$item['product_id'],
                    'qty' => (int)$item['qty'],
                    'unit_price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);
            }

            $this->clearCart($userId);
            $this->db->getConnection()->commit();
            logActivity('order_created', 'orders', $orderId, null, ['order_no' => $orderNo, 'total' => $grandTotal]);
            return ['success' => true, 'order_id' => $orderId, 'order_no' => $orderNo, 'grand_total' => $grandTotal];
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            return ['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()];
        }
    }

    public function getOrders($userId, $filters = []) {
        $where = 'o.user_id = ?';
        $params = [$userId];
        if (!empty($filters['status'])) {
            $where .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        $total = $this->db->fetch("SELECT COUNT(*) as t FROM orders o WHERE {$where}", $params)['t'];
        $page = (int)($filters['page'] ?? 1);
        $perPage = (int)($filters['per_page'] ?? 20);
        $pagination = $this->paginate($total, $perPage, $page);

        $orders = $this->db->fetchAll(
            "SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
             FROM orders o WHERE {$where} ORDER BY o.created_at DESC
             LIMIT {$perPage} OFFSET {$pagination['offset']}",
            $params
        );

        return ['orders' => $orders, 'pagination' => $pagination];
    }

    public function getOrder($orderId, $userId) {
        $order = $this->db->fetch(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $userId]
        );
        if (!$order) return null;
        $order['items'] = $this->db->fetchAll(
            "SELECT oi.*, p.name, p.image, p.sku FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
        return $order;
    }

    /** Remaining returnable quantity per line of a delivered order. */
    public function getOrderReturnable($orderId, $userId) {
        $order = $this->getOrder($orderId, $userId);   // enforces ownership
        if (!$order) return null;

        $tally = Database::getInstance()->fetchAll(
            "SELECT ri.order_item_id, COALESCE(SUM(ri.qty),0) as qty
             FROM sale_return_items ri
             JOIN sale_returns r ON r.id = ri.return_id
             WHERE r.order_id = ? AND r.status <> 'cancelled'
             GROUP BY ri.order_item_id", [$orderId]
        );
        $already = [];
        foreach ($tally as $t) $already[(int)$t['order_item_id']] = (int)$t['qty'];

        $items = [];
        foreach ($order['items'] as $i) {
            $remaining = max(0, (int)$i['qty'] - ($already[(int)$i['id']] ?? 0));
            $i['returnable_qty'] = $remaining;
            $items[] = $i;
        }

        return [
            'items'    => $items,
            'returnable' => $order['status'] === 'delivered' && array_sum(array_column($items, 'returnable_qty')) > 0,
        ];
    }
}