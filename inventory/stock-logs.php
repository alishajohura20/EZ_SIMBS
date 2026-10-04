<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager']);
$pageTitle = 'Stock Logs';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Stock Activity Log</h4>
        <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Inventory</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="damage">Damage</option>
                        <option value="return">Return</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="productFilter" placeholder="Filter by product...">
                </div>
                <div class="col-md-2"><button class="btn btn-primary" onclick="loadLogs()">Filter</button></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Date</th><th>Product</th><th>SKU</th><th>Branch</th><th>Type</th><th>Qty</th><th>User</th><th>Notes</th></tr>
                    </thead>
                    <tbody id="logsTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

async function loadLogs(page = 1) {
    currentPage = page;
    const type = document.getElementById('typeFilter').value;
    const product = document.getElementById('productFilter').value;
    const res = await apiRequest(`../../api/inventory/logs.php?type=${encodeURIComponent(type)}&product=${encodeURIComponent(product)}&page=${page}&limit=20`);
    if (!res.success) return;

    const typeColors = { in: 'success', out: 'danger', adjustment: 'warning', damage: 'dark', return: 'info', transfer: 'primary' };

    document.getElementById('logsTable').innerHTML = res.data.logs.map(l => `
        <tr>
            <td>${new Date(l.created_at).toLocaleString()}</td>
            <td>${l.product_name}</td>
            <td><code>${l.sku}</code></td>
            <td>${l.branch_name}</td>
            <td><span class="badge bg-${typeColors[l.type]||'secondary'}">${l.type}</span></td>
            <td>${l.qty}</td>
            <td>${l.user_name || '-'}</td>
            <td>${l.notes || '-'}</td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadLogs);
}

document.getElementById('productFilter').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadLogs();
});

loadLogs();
</script>

<?php require_once '../../includes/footer.php'; ?>
