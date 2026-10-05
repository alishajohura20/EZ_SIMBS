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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - <?= $s['invoice_no'] ?></title>
    <style>
        body { font-family: 'Courier New', monospace; max-width: 300px; margin: 0 auto; padding: 20px; font-size: 12px; }
        .center { text-align: center; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; }
        .row { display: flex; justify-content: space-between; }
        hr { border: none; border-top: 1px dashed #000; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <?php if (($data['returnable'] ?? false)): ?>
    <div class="no-print" style="background:#e7f1ff;border:1px solid #9dc3ff;border-radius:6px;padding:8px;margin-bottom:12px;text-align:center">
        <a href="../returns/?sale_id=<?= (int)$s['id'] ?>" style="color:#0d47a1;font-weight:bold">Return Items from this Sale</a>
    </div>
    <?php endif; ?>
    <?php $exchR = (new SaleReturn())->getExchangeableForSale((int)$s['id']); ?>
    <?php if ($exchR): ?>
    <div class="no-print" style="background:#fff3d6;border:1px solid #f5c76b;border-radius:6px;padding:8px;margin-bottom:12px;text-align:center">
        <a href="../returns/view.php?id=<?= (int)$exchR['id'] ?>" style="color:#7a5900;font-weight:bold">Exchange Items (<?= sanitize($exchR['return_no']) ?>)</a>
    </div>
    <?php endif; ?>
    <div class="center">
        <h3><?= APP_NAME ?></h3>
        <p>123 Business St, City</p>
        <p><?= $s['invoice_no'] ?></p>
        <p><?= date('M d, Y h:i A', strtotime($s['created_at'])) ?></p>
    </div>
    <hr>
    <p>Customer: <?= $s['customer_name'] ?? 'Walk-in' ?></p>
    <hr>
    <?php foreach ($items as $i): ?>
    <div class="row">
        <span><?= $i['product_name'] ?> x<?= $i['qty'] ?></span>
        <span>$<?= number_format($i['total'], 2) ?></span>
    </div>
    <?php endforeach; ?>
    <hr>
    <div class="row"><span>Subtotal:</span><span>$<?= number_format($s['subtotal'], 2) ?></span></div>
    <?php if ($s['discount'] > 0): ?>
    <div class="row"><span>Discount:</span><span>-$<?= number_format($s['discount'], 2) ?></span></div>
    <?php endif; ?>
    <div class="row"><span>Tax:</span><span>$<?= number_format($s['tax'], 2) ?></span></div>
    <?php if ($s['shipping'] > 0): ?>
    <div class="row"><span>Shipping:</span><span>$<?= number_format($s['shipping'], 2) ?></span></div>
    <?php endif; ?>
    <hr>
    <div class="row"><span><strong>TOTAL:</strong></span><span><strong>$<?= number_format($s['grand_total'], 2) ?></strong></span></div>
    <div class="row"><span>Payment:</span><span><?= ucfirst($s['payment_method']) ?></span></div>
    <hr>
    <p class="center">Thank you for your purchase!</p>
    <script>window.print();</script>
</body>
</html>
