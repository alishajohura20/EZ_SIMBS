<?php
$pageTitle = 'Reorder Suggestions';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <h4 class="mb-4"><i class="fas fa-sync-alt"></i> Auto-Reorder Suggestions</h4>

    <div class="card mb-4">
        <div class="card-body">
            <button class="btn btn-primary" onclick="runAutoReorder()"><i class="fas fa-play"></i> Run Auto-Reorder Check</button>
            <span id="reorderResult" class="ms-3"></span>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Suggested Reorders</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Product</th><th>SKU</th><th>Current Stock</th><th>Reorder Level</th><th>Suggested Qty</th><th>Est. Cost</th><th>Branch</th><th>Action</th></tr></thead>
                    <tbody id="suggestionsTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
async function loadSuggestions() {
    const res = await apiRequest('../../api/notifications/reorder-suggestions.php');
    if (!res.success) return;

    document.getElementById('suggestionsTable').innerHTML = res.data.map(s => `
        <tr>
            <td>${s.product_name}</td>
            <td><code>${s.sku}</code></td>
            <td><span class="badge bg-danger">${s.qty}</span></td>
            <td>${s.reorder_level}</td>
            <td><strong>${s.suggested_qty}</strong></td>
            <td>$${parseFloat(s.estimated_cost).toFixed(2)}</td>
            <td>${s.branch_name}</td>
            <td><button class="btn btn-sm btn-success" onclick="createPO(${s.product_id},${s.suggested_qty},${s.branch_id})"><i class="fas fa-cart-plus"></i> Create PO</button></td>
        </tr>
    `).join('');
}

async function runAutoReorder() {
    document.getElementById('reorderResult').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Checking...';
    const res = await apiRequest('../../api/notifications/check.php');
    if (res.success) {
        const d = res.data;
        document.getElementById('reorderResult').innerHTML = `
            <span class="badge bg-warning">${d.low_stock_alerts} low stock</span>
            <span class="badge bg-danger">${d.out_of_stock_alerts} out of stock</span>
            <span class="badge bg-info">${d.due_payment_alerts} due payments</span>
            <span class="badge bg-success">${d.auto_reorders_created} auto POs created</span>
        `;
        loadSuggestions();
    }
}

loadSuggestions();
</script>

<?php require_once '../../includes/footer.php'; ?>
