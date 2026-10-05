<?php
$pageTitle = 'Cashier Dashboard';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$db = Database::getInstance();
$branchId = (int)($_SESSION['user_branch'] ?? 0);
$date = date('Y-m-d');
$month = date('Y-m');
$branchCond = $branchId ? " AND s.branch_id = ?" : "";

$today = $db->fetch(
    "SELECT IFNULL(SUM(s.grand_total),0) AS total, COUNT(*) AS count, IFNULL(AVG(s.grand_total),0) AS avg_order
     FROM sales s
     WHERE s.status='completed' AND DATE(s.created_at) = ?{$branchCond}",
    $branchId ? [$date, $branchId] : [$date]
);

$itemsToday = $db->fetch(
    "SELECT IFNULL(SUM(si.qty),0) AS items
     FROM sale_items si JOIN sales s ON si.sale_id = s.id
     WHERE s.status='completed' AND DATE(s.created_at) = ?{$branchCond}",
    $branchId ? [$date, $branchId] : [$date]
);

$monthTotal = $db->fetch(
    "SELECT IFNULL(SUM(s.grand_total),0) AS total
     FROM sales s
     WHERE s.status='completed' AND DATE_FORMAT(s.created_at, '%Y-%m') = ?{$branchCond}",
    $branchId ? [$month, $branchId] : [$month]
);

$paymentsToday = $db->fetchAll(
    "SELECT s.payment_method, IFNULL(SUM(s.grand_total),0) AS total, COUNT(*) AS cnt
     FROM sales s
     WHERE s.status='completed' AND DATE(s.created_at) = ?{$branchCond}
     GROUP BY s.payment_method ORDER BY total DESC",
    $branchId ? [$date, $branchId] : [$date]
);

$cashToday = $cardToday = 0;
foreach ($paymentsToday as $pm) {
    if (strcasecmp($pm['payment_method'], 'cash') === 0) $cashToday = (float)$pm['total'];
    else $cardToday += (float)$pm['total'];
}

$lowStockCount = $branchId ? (int)$db->fetch(
    "SELECT COUNT(*) AS c FROM inventory i JOIN products p ON p.id = i.product_id
     WHERE i.branch_id = ? AND i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status='active'",
    [$branchId]
)['c'] : 0;

$recent = $db->fetchAll(
    "SELECT s.invoice_no, s.grand_total, s.payment_method, s.created_at, c.name AS customer_name
     FROM sales s LEFT JOIN customers c ON c.id = s.customer_id
     WHERE s.status='completed'{$branchCond}
     ORDER BY s.created_at DESC LIMIT 8",
    $branchId ? [$branchId] : []
);
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="mb-0" style="font-weight:700"><i class="fas fa-cash-register me-2 text-success"></i>Cashier Dashboard</h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if ($branchId): ?>
                <span class="badge" style="background:var(--primary-soft,#eef2ff);color:var(--primary,#4f46e5)"><i class="fas fa-code-branch me-1"></i><?= sanitize($db->fetch("SELECT name FROM branches WHERE id = ?", [$branchId])['name'] ?? 'Branch') ?></span>
            <?php endif; ?>
            <a href="../store/roles.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-shield me-1"></i> Role Dashboards</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$today['total'], 2) ?></h3><p>Today's Sales</p></div><div class="text-success"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)$today['count'] ?></h3><p>Today's Invoices</p></div><div class="text-info"><i class="fas fa-receipt fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)$itemsToday['items'] ?></h3><p>Items Sold Today</p></div><div class="text-primary"><i class="fas fa-shopping-basket fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$today['avg_order'], 2) ?></h3><p>Avg Order Value</p></div><div class="text-secondary"><i class="fas fa-calculator fa-3x opacity-25"></i></div></div></div></div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3 class="text-success">$<?= number_format($cashToday, 2) ?></h3><p>Cash Today</p></div><div class="text-success"><i class="fas fa-coins fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format($cardToday, 2) ?></h3><p>Card / Other Today</p></div><div class="text-primary"><i class="fas fa-credit-card fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$monthTotal['total'], 0) ?></h3><p>Month Sales</p></div><div class="text-warning"><i class="fas fa-chart-line fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3 class="text-danger"><?= $lowStockCount ?></h3><p>Low Stock Alerts</p></div><div class="text-danger"><i class="fas fa-exclamation-triangle fa-3x opacity-25"></i></div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card mb-3 border-0" style="border-radius:18px;background:linear-gradient(135deg,#059669,#10b981);color:#fff">
                <div class="card-body text-center d-flex flex-column align-items-center" style="gap:10px">
                    <div style="font-size:2.6rem"><i class="fas fa-cash-register"></i></div>
                    <h5 class="mb-0" style="font-weight:800">Start Billing At Your Counter</h5>
                    <p class="mb-0" style="opacity:.9"><?= (int)$today['count'] ?> order<?= $today['count'] == 1 ? '' : 's' ?> processed today</p>
                    <a href="../sales/pos.php" class="btn btn-light px-4" style="font-weight:800"><i class="fas fa-tachometer-alt me-2"></i> Open POS</a>
                </div>
            </div>
            <div class="card"><div class="card-header"><h5>Counter Shortcuts</h5></div>
                <div class="card-body d-grid gap-2">
                    <a href="../inventory/" class="btn btn-outline-primary"><i class="fas fa-boxes me-2"></i> Check Stock</a>
                    <a href="../sales/" class="btn btn-outline-primary"><i class="fas fa-receipt me-2"></i> Sales History</a>
                    <a href="../invoices/" class="btn btn-outline-primary"><i class="fas fa-file-invoice me-2"></i> Invoices</a>
                    <a href="../customers/" class="btn btn-outline-primary"><i class="fas fa-users me-2"></i> Customer Lookup</a>
                    <a href="../products/" class="btn btn-outline-primary"><i class="fas fa-box me-2"></i> Products &amp; Barcode</a>
                    <a href="../purchases/" class="btn btn-outline-primary"><i class="fas fa-shopping-cart me-2"></i> Purchases</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-3"><div class="card-header"><h5><i class="fas fa-credit-card me-2" style="color:var(--primary)"></i>Today's Payments</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead><tr><th>Method</th><th>Transactions</th><th>Total</th><th>Share</th></tr></thead>
                        <tbody>
                        <?php if (!$paymentsToday): ?>
                            <tr><td colspan="4" class="text-center text-muted">No payments recorded today yet.</td></tr>
                        <?php else: foreach ($paymentsToday as $pm): ?>
                            <tr>
                                <td><span class="badge bg-secondary text-capitalize"><?= sanitize($pm['payment_method']) ?></span></td>
                                <td><?= (int)$pm['cnt'] ?></td>
                                <td>$<?= number_format((float)$pm['total'], 2) ?></td>
                                <td>
                                    <?php $pct = (float)$today['total'] > 0 ? round((float)$pm['total'] / $today['total'] * 100) : 0; ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div>
                                        <small class="text-muted"><?= $pct ?>%</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card"><div class="card-header"><h5>Recent Sales</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Method</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if (!$recent): ?>
                            <tr><td colspan="5" class="text-center text-muted">No sales recorded yet.</td></tr>
                        <?php else: foreach ($recent as $s): ?>
                            <tr><td><code><?= $s['invoice_no'] ?></code></td><td><?= sanitize($s['customer_name'] ?? 'Walk-in') ?></td><td>$<?= number_format($s['grand_total'], 2) ?></td><td><span class="badge bg-secondary text-capitalize"><?= $s['payment_method'] ?></span></td><td><?= date('M j, Y H:i', strtotime($s['created_at'])) ?></td></tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>