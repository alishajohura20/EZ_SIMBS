<?php
class Report {
    private $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function salesReport($filters = []) {
        $where = "s.status = 'completed'"; $params = [];
        if (!empty($filters['date_from'])) { $where .= " AND s.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where .= " AND s.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }
        $summary = $this->db->fetch("SELECT COUNT(*) as total_orders, IFNULL(SUM(subtotal),0) as subtotal, IFNULL(SUM(discount),0) as discounts, IFNULL(SUM(tax),0) as taxes, IFNULL(SUM(shipping),0) as shipping_total, IFNULL(SUM(grand_total),0) as grand_total, IFNULL(AVG(grand_total),0) as avg_order FROM sales s WHERE {$where}", $params);
        $byPayment = $this->db->fetchAll("SELECT payment_method, COUNT(*) as count, SUM(grand_total) as total FROM sales s WHERE {$where} GROUP BY payment_method", $params);
        $daily = $this->db->fetchAll("SELECT DATE(s.created_at) as date, COUNT(*) as orders, SUM(grand_total) as total FROM sales s WHERE {$where} GROUP BY DATE(s.created_at) ORDER BY date", $params);
        return ['summary' => $summary, 'by_payment' => $byPayment, 'daily' => $daily];
    }

    public function inventoryReport($filters = []) {
        $where = "1=1"; $params = [];
        if (!empty($filters['branch_id'])) { $where .= " AND i.branch_id = ?"; $params[] = $filters['branch_id']; }
        $summary = $this->db->fetch("SELECT COUNT(*) as total_products, IFNULL(SUM(i.qty),0) as total_qty, IFNULL(SUM(i.qty * p.cost),0) as total_value, SUM(CASE WHEN i.qty = 0 THEN 1 ELSE 0 END) as out_of_stock, SUM(CASE WHEN i.qty <= i.reorder_level AND i.reorder_level > 0 THEN 1 ELSE 0 END) as low_stock FROM inventory i JOIN products p ON i.product_id = p.id WHERE {$where}", $params);
        $details = $this->db->fetchAll("SELECT p.name, p.sku, c.name as category, i.qty, p.cost, (i.qty * p.cost) as value, b.name as branch, i.reorder_level FROM inventory i JOIN products p ON i.product_id = p.id LEFT JOIN categories c ON p.category_id = c.id JOIN branches b ON i.branch_id = b.id WHERE {$where} ORDER BY value DESC", $params);
        return ['summary' => $summary, 'details' => $details];
    }

    public function profitLossReport($filters = []) {
        $where = "s.status = 'completed'"; $params = [];
        if (!empty($filters['date_from'])) { $where .= " AND s.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where .= " AND s.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }
        $revenue = $this->db->fetch("SELECT IFNULL(SUM(grand_total),0) as total FROM sales s WHERE {$where}", $params)['total'];
        $cogs = $this->db->fetch("SELECT IFNULL(SUM(si.qty * p.cost),0) as total FROM sale_items si JOIN products p ON si.product_id = p.id JOIN sales s ON si.sale_id = s.id WHERE {$where}", $params)['total'];
        $grossProfit = $revenue - $cogs;
        return ['revenue' => $revenue, 'cost_of_goods' => $cogs, 'gross_profit' => $grossProfit, 'profit_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0];
    }

    public function purchaseReport($filters = []) {
        $where = "1=1"; $params = [];
        if (!empty($filters['date_from'])) { $where .= " AND p.created_at >= ?"; $params[] = $filters['date_from']; }
        if (!empty($filters['date_to'])) { $where .= " AND p.created_at <= ?"; $params[] = $filters['date_to'] . ' 23:59:59'; }
        $summary = $this->db->fetch("SELECT COUNT(*) as total_orders, IFNULL(SUM(total),0) as total_amount, IFNULL(SUM(paid),0) as total_paid, IFNULL(SUM(due),0) as total_due FROM purchases p WHERE {$where}", $params);
        $bySupplier = $this->db->fetchAll("SELECT s.company_name, COUNT(*) as orders, SUM(p.total) as total FROM purchases p JOIN suppliers s ON p.supplier_id = s.id WHERE {$where} GROUP BY s.id ORDER BY total DESC", $params);
        return ['summary' => $summary, 'by_supplier' => $bySupplier];
    }

    public function customerReport() {
        return $this->db->fetchAll("SELECT c.name, c.phone, c.membership_id, c.loyalty_points, c.is_vip, COUNT(s.id) as orders, IFNULL(SUM(s.grand_total),0) as total_spent FROM customers c LEFT JOIN sales s ON c.id = s.customer_id AND s.status = 'completed' WHERE c.status = 'active' GROUP BY c.id ORDER BY total_spent DESC");
    }

    public function exportCSV($data, $filename) {
        if (empty($data)) return;
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $output = fopen('php://output', 'w');
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) fputcsv($output, $row);
        fclose($output);
        exit;
    }
}
