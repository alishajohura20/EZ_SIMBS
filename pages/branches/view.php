<?php
$pageTitle = 'Branch Details';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <div id="branchContent">Loading...</div>
</div>

<script>
const branchId = <?= $id ?>;
async function loadBranch() {
    const res = await apiRequest(`../../api/branches/index.php?id=${branchId}`);
    if (!res.success) { document.getElementById('branchContent').innerHTML = '<p>Not found</p>'; return; }
    const b = res.data;

    document.getElementById('branchContent').innerHTML = `
        <div class="d-flex justify-content-between mb-4">
            <h4>${b.branch.name}</h4>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
        <div class="row mb-4">
            <div class="col-md-3"><div class="card card-stat text-center"><h3>${b.inventory.products}</h3><p>Products</p></div></div>
            <div class="col-md-3"><div class="card card-stat text-center"><h3>${b.inventory.total_qty}</h3><p>Total Stock</p></div></div>
            <div class="col-md-3"><div class="card card-stat text-center"><h3>$${parseFloat(b.inventory.stock_value).toFixed(0)}</h3><p>Stock Value</p></div></div>
            <div class="col-md-3"><div class="card card-stat text-center"><h3 class="text-${b.low_stock > 0 ? 'danger' : 'success'}">${b.low_stock}</h3><p>Low Stock</p></div></div>
        </div>
        <div class="row mb-4">
            <div class="col-md-4"><div class="card card-stat"><h5>Completed Sales</h5><h3>${b.sales.total_sales}</h3><p>$${parseFloat(b.sales.total_revenue).toFixed(2)}</p></div></div>
            <div class="col-md-4"><div class="card card-stat"><h5>Active Staff</h5><h3>${b.staff_count}</h3></div></div>
            <div class="col-md-4"><div class="card card-stat"><h5>Manager</h5><p>${b.branch.manager_name || 'Unassigned'}</p><p>${b.branch.phone || ''}</p></div></div>
        </div>
        <h5>Transfer Suggestions (stock available from other branches)</h5>
        <div id="suggestions"></div>
    `;

    const sugRes = await apiRequest(`../../api/branches/suggestions.php?branch_id=${branchId}`);
    if (sugRes.success && sugRes.data.length > 0) {
        document.getElementById('suggestions').innerHTML = '<table class="table table-sm"><thead><tr><th>Product</th><th>Need</th><th>From Branch</th><th>Available</th><th>Transfer Qty</th><th>Action</th></tr></thead><tbody>' +
            sugRes.data.map(s => `<tr><td>${s.name} (${s.sku})</td><td>${s.needed_qty}</td><td>${s.from_branch}</td><td>${s.available_qty}</td><td>${s.transfer_qty}</td><td><button class="btn btn-sm btn-primary" onclick="transferStock(${s.product_id},${s.from_branch_id},${branchId},${s.transfer_qty})">Transfer</button></td></tr>`).join('') +
            '</tbody></table>';
    } else {
        document.getElementById('suggestions').innerHTML = '<p class="text-muted">No transfer suggestions</p>';
    }
}

async function transferStock(productId, from, to, qty) {
    if (!confirm(`Transfer ${qty} units?`)) return;
    const res = await apiRequest('../../api/inventory/index.php', 'POST', { action: 'transfer', product_id: productId, from_branch: from, to_branch: to, qty });
    if (res.success) { alert('Transfer complete'); loadBranch(); }
    else alert(res.message);
}

loadBranch();
</script>

<?php require_once '../../includes/footer.php'; ?>
