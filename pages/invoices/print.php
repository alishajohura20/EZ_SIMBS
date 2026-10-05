<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();

$id = (int)$_GET['id'];
$sale = new Sale();
$data = $sale->getById($id);
if (!$data) die('Not found');

$s = $data['sale'];
$items = $data['items'];
$invoice = (new Invoice())->getBySaleId($id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoice - <?= $s['invoice_no'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #222; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .company { color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .totals { text-align: right; margin-top: 20px; }
        .totals table { width: auto; margin-left: auto; }
        .totals td { border: none; padding: 4px 8px; }
        .thanks { text-align: center; margin-top: 30px; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="margin-bottom:20px;padding:8px 16px;">Print</button>
    <div class="header">
        <div class="company">
            <h2><?= APP_NAME ?></h2>
            <p>123 Business St, City<br>Phone: +1-800-000-0000</p>
        </div>
        <div style="text-align:right">
            <h3>INVOICE</h3>
            <p><strong><?= $s['invoice_no'] ?></strong></p>
            <p>Date: <?= date('M d, Y', strtotime($s['created_at'])) ?></p>
            <?php if ($invoice && $invoice['qr_code']): ?>
            <p style="font-size:11px;color:#888">QR: <?= htmlspecialchars($invoice['qr_code']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <p><strong>Customer:</strong> <?= $s['customer_name'] ?? 'Walk-in' ?></p>
    <?php if (!empty($s['customer_phone'])): ?><p><?= $s['customer_phone'] ?></p><?php endif; ?>

    <table>
        <thead>
            <tr><th>Product</th><th>SKU</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i): ?>
            <tr>
                <td><?= $i['product_name'] ?></td>
                <td><?= $i['sku'] ?></td>
                <td><?= $i['qty'] ?></td>
                <td><?= formatCurrency($i['unit_price']) ?></td>
                <td><?= formatCurrency($i['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td>Subtotal:</td><td><?= formatCurrency($s['subtotal']) ?></td></tr>
            <?php if ($s['discount'] > 0): ?>
            <tr><td>Discount:</td><td>-<?= formatCurrency($s['discount']) ?></td></tr>
            <?php endif; ?>
            <tr><td>Tax:</td><td><?= formatCurrency($s['tax']) ?></td></tr>
            <?php if ($s['shipping'] > 0): ?>
            <tr><td>Shipping:</td><td><?= formatCurrency($s['shipping']) ?></td></tr>
            <?php endif; ?>
            <tr style="font-weight:bold;border-top:1px solid #333"><td>Grand Total:</td><td><?= formatCurrency($s['grand_total']) ?></td></tr>
        </table>
    </div>

    <p class="thanks">Thank you for your purchase!</p>
</body>
</html>
