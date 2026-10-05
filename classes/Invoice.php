<?php
class Invoice {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($saleId) {
        $sale = (new Sale())->getById($saleId);
        if (!$sale) return null;

        $qrData = json_encode([
            'invoice' => $sale['sale']['invoice_no'],
            'total' => $sale['sale']['grand_total'],
            'date' => $sale['sale']['created_at'],
        ]);

        $invoiceId = $this->db->insert('invoices', [
            'sale_id' => $saleId,
            'qr_code' => $qrData,
        ]);

        return $invoiceId;
    }

    public function getBySaleId($saleId) {
        return $this->db->fetch("SELECT * FROM invoices WHERE sale_id = ?", [$saleId]);
    }

    public function getAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (s.invoice_no LIKE ? OR c.name LIKE ?)";
            $s = "%{$filters['search']}%";
            $params[] = $s;
            $params[] = $s;
        }
        if (!empty($filters['date_from'])) {
            $where .= " AND i.created_at >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where .= " AND i.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        if (!empty($filters['branch_id'])) {
            $where .= " AND s.branch_id = ?";
            $params[] = $filters['branch_id'];
        }

        $total = $this->db->fetch(
            "SELECT COUNT(*) as t FROM invoices i
             JOIN sales s ON i.sale_id = s.id
             LEFT JOIN customers c ON s.customer_id = c.id
             WHERE {$where}",
            $params
        )['t'];

        $page = (int)($filters['page'] ?? 1);
        $pagination = paginate($total, 20, $page);

        $invoices = $this->db->fetchAll(
            "SELECT i.*, s.invoice_no, s.grand_total, s.status, s.created_at as sale_date,
                    c.name as customer_name, b.name as branch_name
             FROM invoices i
             JOIN sales s ON i.sale_id = s.id
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN branches b ON s.branch_id = b.id
             WHERE {$where}
             ORDER BY i.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['invoices' => $invoices, 'pagination' => $pagination, 'total' => $total];
    }

    public function generatePDF($saleId) {
        $sale = (new Sale())->getById($saleId);
        if (!$sale) return null;

        $pdfPath = EXPORT_PATH . "/pdf/invoice_{$sale['sale']['invoice_no']}.pdf";

        if (!is_dir(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0777, true);
        }

        $html = $this->renderInvoiceHTML($sale);

        $this->db->update('invoices', ['pdf_path' => $pdfPath], 'sale_id = ?', [$saleId]);
        return $pdfPath;
    }

    private function renderInvoiceHTML($data) {
        $s = $data['sale'];
        $items = $data['items'];

        $html = '<!DOCTYPE html><html><head><style>
            body{font-family:Arial,sans-serif;margin:20px}
            .header{display:flex;justify-content:space-between;border-bottom:2px solid #333;padding-bottom:10px}
            table{width:100%;border-collapse:collapse;margin:20px 0}
            th,td{border:1px solid #ddd;padding:8px;text-align:left}
            th{background:#f5f5f5}
            .totals{text-align:right;margin-top:20px}
            .totals table{width:auto;margin-left:auto}
        </style></head><body>
        <div class="header">
            <div><h2>' . APP_NAME . '</h2><p>123 Business St, City</p></div>
            <div><h3>INVOICE</h3><p><strong>' . $s['invoice_no'] . '</strong></p><p>Date: ' . date('M d, Y', strtotime($s['created_at'])) . '</p></div>
        </div>
        <p><strong>Customer:</strong> ' . ($s['customer_name'] ?? 'Walk-in') . '</p>
        <table><thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';

        foreach ($items as $i) {
            $html .= '<tr><td>' . $i['product_name'] . '</td><td>' . $i['sku'] . '</td><td>' . $i['qty'] . '</td><td>$' . number_format($i['unit_price'], 2) . '</td><td>$' . number_format($i['total'], 2) . '</td></tr>';
        }

        $html .= '</tbody></table>
        <div class="totals"><table>
            <tr><td>Subtotal:</td><td>$' . number_format($s['subtotal'], 2) . '</td></tr>
            <tr><td>Discount:</td><td>-$' . number_format($s['discount'], 2) . '</td></tr>
            <tr><td>Tax:</td><td>$' . number_format($s['tax'], 2) . '</td></tr>
            <tr><td>Shipping:</td><td>$' . number_format($s['shipping'], 2) . '</td></tr>
            <tr><td><strong>Grand Total:</strong></td><td><strong>$' . number_format($s['grand_total'], 2) . '</strong></td></tr>
        </table></div>
        <p style="text-align:center;margin-top:30px">Thank you for your purchase!</p>
        </body></html>';

        return $html;
    }
}
