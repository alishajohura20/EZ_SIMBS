<?php
class Dashboard {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getStats($branchId = null) {
        $branchWhere = $branchId ? "AND branch_id = ?" : "";
        $date = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $yearStart = date('Y-01-01');

        $sales = $this->db->fetch("
            SELECT
                IFNULL(SUM(CASE WHEN DATE(created_at) = ? THEN grand_total ELSE 0 END), 0) as today_sales,
                IFNULL(SUM(CASE WHEN created_at >= ? THEN grand_total ELSE 0 END), 0) as monthly_sales,
                IFNULL(SUM(CASE WHEN created_at >= ? THEN grand_total ELSE 0 END), 0) as yearly_sales,
                IFNULL(SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END), 0) as today_count,
                IFNULL(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) as monthly_count
            FROM sales WHERE status = 'completed' {$branchWhere}
        ", array_merge([$date, $monthStart, $yearStart, $date, $monthStart], $branchId ? [(int)$branchId] : []));

        $products = $this->db->fetch("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
        $customers = $this->db->fetch("SELECT COUNT(*) as total FROM customers WHERE status = 'active'");
        $suppliers = $this->db->fetch("SELECT COUNT(*) as total FROM suppliers WHERE status = 'active'");

        $branchCond = $branchId ? "AND branch_id = ?" : "";
        $lowStock = $this->db->fetch(
            "SELECT COUNT(*) as total FROM inventory WHERE qty <= reorder_level AND reorder_level > 0 {$branchCond}",
            $branchId ? [$branchId] : []
        )['total'];

        $outOfStock = $this->db->fetch(
            "SELECT COUNT(*) as total FROM inventory WHERE qty = 0 {$branchCond}",
            $branchId ? [$branchId] : []
        )['total'];

        $pendingPayments = $this->db->fetch(
            "SELECT IFNULL(SUM(due), 0) as total FROM purchases WHERE due > 0 AND status != 'cancelled'"
        )['total'];

        $cogs = $this->db->fetch("
            SELECT IFNULL(SUM(si.qty * p.cost), 0) as total
            FROM sale_items si JOIN products p ON si.product_id = p.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.status = 'completed' AND s.created_at >= ? {$branchWhere}
        ", array_merge([$monthStart], $branchId ? [(int)$branchId] : []))['total'];

        $monthlyProfit = $sales['monthly_sales'] - $cogs;

        return [
            'today_sales' => $sales['today_sales'],
            'today_count' => $sales['today_count'],
            'monthly_sales' => $sales['monthly_sales'],
            'monthly_count' => $sales['monthly_count'],
            'yearly_sales' => $sales['yearly_sales'],
            'total_products' => $products['total'],
            'total_customers' => $customers['total'],
            'total_suppliers' => $suppliers['total'],
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'pending_payments' => $pendingPayments,
            'monthly_profit' => $monthlyProfit,
        ];
    }

    public function getSalesChart($period = 'monthly', $branchId = null) {
        $branchWhere = $branchId ? "AND branch_id = ?" : "";
        $params = $branchId ? [$branchId] : [];

        if ($period === 'daily') {
            return $this->db->fetchAll("
                SELECT DATE(created_at) as label, IFNULL(SUM(grand_total),0) as total, COUNT(*) as count
                FROM sales WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) {$branchWhere}
                GROUP BY DATE(created_at) ORDER BY label", $params
            );
        } elseif ($period === 'weekly') {
            return $this->db->fetchAll("
                SELECT YEARWEEK(created_at) as label, IFNULL(SUM(grand_total),0) as total, COUNT(*) as count
                FROM sales WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) {$branchWhere}
                GROUP BY YEARWEEK(created_at) ORDER BY label", $params
            );
        } else {
            return $this->db->fetchAll("
                SELECT DATE_FORMAT(created_at, '%Y-%m') as label, IFNULL(SUM(grand_total),0) as total, COUNT(*) as count
                FROM sales WHERE status='completed' AND YEAR(created_at) = YEAR(CURDATE()) {$branchWhere}
                GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY label", $params
            );
        }
    }

    public function getTopProducts($limit = 10) {
        return $this->db->fetchAll("
            SELECT p.name, p.sku, p.image, SUM(si.qty) as total_sold, SUM(si.total) as revenue
            FROM sale_items si JOIN products p ON si.product_id = p.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.status = 'completed' AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY p.id ORDER BY total_sold DESC LIMIT ?", [$limit]
        );
    }

    public function getRecentSales($limit = 10) {
        return $this->db->fetchAll("
            SELECT s.invoice_no, s.grand_total, s.payment_method, s.created_at,
                    c.name as customer_name, u.name as cashier_name
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.created_by = u.id
            WHERE s.status = 'completed'
            ORDER BY s.created_at DESC LIMIT ?", [$limit]
        );
    }

    public function getLowStockProducts() {
        return $this->db->fetchAll("
            SELECT p.name, p.sku, i.qty, i.reorder_level, b.name as branch_name
            FROM inventory i JOIN products p ON i.product_id = p.id JOIN branches b ON i.branch_id = b.id
            WHERE i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status = 'active'
            ORDER BY (i.qty / i.reorder_level) ASC LIMIT 20
        ");
    }
}
