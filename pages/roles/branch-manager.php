<?php
$pageTitle = 'Branch Manager Dashboard';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$db = Database::getInstance();
$durationLimit = null;
$branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : ($_SESSION['user_branch'] ?? 0);
$branch = $branchId ? $db->fetch("SELECT * FROM branches WHERE id = ?", [$branchId]) : null;
if (!$branch) {
    $branch = $db->fetch("SELECT * FROM branches ORDER BY id LIMIT 1");
    $branchId = $branch ? (int)$branch['id'] : 0;
}
$branches = $db->fetchAll("SELECT id, name FROM branches WHERE status='active' ORDER BY name");

$dash = new Dashboard();
$stats = $branchId ? $dash->getStats($branchId) : $dash->getStats();

$lowStock = $branchId ? $db->fetchAll(
    "SELECT p.name, p.sku, i.qty, i.reorder_level FROM inventory i
     JOIN products p ON p.id = i.product_id
     WHERE i.branch_id = ? AND i.qty <= i.reorder_level AND i.reorder_level > 0 AND p.status='active'
     ORDER BY (i.qty / i.reorder_level) LIMIT 12", [$branchId]
) : [];

$recentSales = $branchId ? $db->fetchAll(
    "SELECT s.invoice_no, s.grand_total, s.payment_method, s.created_at, c.name AS customer_name
     FROM sales s LEFT JOIN customers c ON c.id = s.customer_id
     WHERE s.status='completed' AND s.branch_id = ?
     ORDER BY s.created_at DESC LIMIT 8", [$branchId]
) : [];

$stockInfo = $branchId ? $db->fetch(
    "SELECT COUNT(*) AS product_count, IFNULL(SUM(qty),0) AS total_qty, IFNULL(SUM(i.qty * p.cost),0) AS stock_value
     FROM inventory i JOIN products p ON i.product_id = p.id
     WHERE i.branch_id = ?", [$branchId]
) : ['product_count' => 0, 'total_qty' => 0, 'stock_value' => 0];

$topProduct = $branchId ? $db->fetch(
    "SELECT p.name, p.image, p.sku, SUM(si.qty) AS total_sold, SUM(si.total) AS revenue
     FROM sale_items si
     JOIN sales s ON si.sale_id = s.id
     JOIN products p ON si.product_id = p.id
     WHERE s.status = 'completed' AND s.branch_id = ?
     GROUP BY p.id ORDER BY total_sold DESC, revenue DESC LIMIT 1", [$branchId]
) : null;
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="mb-0" style="font-weight:700"><i class="fas fa-store-alt me-2" style="color:#0ea5e9"></i>Branch Manager Dashboard</h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <select id="branchSelect" class="form-select form-select-sm" style="width:auto;border-radius:50px" onchange="location.href='branch-manager.php?branch_id='+this.value">
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $b['id'] == $branchId ? 'selected' : '' ?>><?= sanitize($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <a href="../store/roles.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-shield me-1"></i> Role Dashboards</a>
        </div>
    </div>

    <div class="alert alert-info d-flex align-items-center" style="border-radius:14px">
        <i class="fas fa-code-branch me-2"></i>
        <div>Scoped to <strong><?= sanitize($branch['name'] ?? '—') ?></strong> — stock, sales and alerts below are branch-specific.</div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stats['today_sales'], 0) ?></h3><p>Today's Sales</p></div><div class="text-primary"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)$stats['today_count'] ?></h3><p>Today's Invoices</p></div><div class="text-info"><i class="fas fa-receipt fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stats['monthly_sales'], 0) ?></h3><p>Monthly Sales</p></div><div class="text-success"><i class="fas fa-chart-line fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3 class="<?= (float)$stats['monthly_profit'] < 0 ? 'text-danger' : 'text-success' ?>">$<?= number_format((float)$stats['monthly_profit'], 0) ?></h3><p>Monthly Profit</p></div><div class="text-success"><i class="fas fa-coins fa-3x opacity-25"></i></div></div></div></div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)$stockInfo['stock_value'], 0) ?></h3><p>Stock Value</p></div><div class="text-secondary"><i class="fas fa-boxes fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)$stockInfo['product_count'] ?></h3><p>Products in Stock</p></div><div class="text-primary"><i class="fas fa-box fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3 class="text-danger"><?= (int)$stats['low_stock'] ?></h3><p>Low Stock Items</p></div><div class="text-danger"><i class="fas fa-exclamation-triangle fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3 class="text-warning"><?= (int)$stats['out_of_stock'] ?></h3><p>Out of Stock</p></div><div class="text-warning"><i class="fas fa-box-open fa-3x opacity-25"></i></div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card mb-3"><div class="card-header"><h5><i class="fas fa-fire me-2" style="color:var(--primary)"></i>Top Selling Product</h5></div>
                <div class="card-body text-center">
                    <?php if ($topProduct): ?>
                        <?php if (!empty($topProduct['image'])): ?>
                            <img src="../../<?= sanitize($topProduct['image']) ?>" onerror="this.style.display='none'" style="width:64px;height:64px;object-fit:cover;border-radius:12px;margin-bottom:10px">
                        <?php endif; ?>
                        <h5 class="mb-1" style="font-weight:800"><?= sanitize($topProduct['name']) ?></h5>
                        <small class="text-muted d-block mb-2">SKU: <?= sanitize($topProduct['sku']) ?></small>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge" style="background:var(--primary-soft, #eef2ff);color:var(--primary, #4f46e5)"><strong><?= (int)$topProduct['total_sold'] ?></strong> sold</span>
                            <span class="badge bg-success"><i class="fas fa-dollar-sign me-1"></i><?= number_format((float)$topProduct['revenue'], 0) ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No completed sales at this branch yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card"><div class="card-header"><h5>Branch Actions</h5></div>
                <div class="card-body d-grid gap-2">
                    <a href="../inventory/" class="btn btn-outline-primary"><i class="fas fa-boxes me-2"></i> Inventory</a>
                    <a href="../inventory/stock-logs.php" class="btn btn-outline-primary"><i class="fas fa-history me-2"></i> Stock Logs</a>
                    <a href="../branches/" class="btn btn-outline-primary"><i class="fas fa-code-branch me-2"></i> Manage Branches</a>
                    <a href="../notifications/reorder-suggestions.php" class="btn btn-outline-primary"><i class="fas fa-sync-alt me-2"></i> Reorder Suggestions</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card"><div class="card-header"><h5>Low Stock Alerts</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Reorder</th></tr></thead>
                        <tbody>
                        <?php if (!$lowStock): ?>
                            <tr><td colspan="4" class="text-center text-muted">No low stock items. </td></tr>
                        <?php else: foreach ($lowStock as $p): ?>
                            <tr><td><?= sanitize($p['name']) ?></td><td><code><?= $p['sku'] ?></code></td><td><span class="badge bg-danger"><?= $p['qty'] ?></span></td><td><?= $p['reorder_level'] ?></td></tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card"><div class="card-header"><h5>Recent Sales — <?= sanitize($branch['name'] ?? '') ?></h5></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Method</th><th>Date</th></tr></thead>
                <tbody>
                <?php if (!$recentSales): ?>
                    <tr><td colspan="5" class="text-center text-muted">No sales recorded at this branch yet.</td></tr>
                <?php else: foreach ($recentSales as $s): ?>
                    <tr><td><code><?= $s['invoice_no'] ?></code></td><td><?= sanitize($s['customer_name'] ?? 'Walk-in') ?></td><td>$<?= number_format($s['grand_total'], 2) ?></td><td><span class="badge bg-secondary"><?= $s['payment_method'] ?></span></td><td><?= date('M j, Y H:i', strtotime($s['created_at'])) ?></td></tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>