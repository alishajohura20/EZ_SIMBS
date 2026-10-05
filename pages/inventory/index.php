<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
$pageTitle = 'Inventory';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Inventory Management</h4>
        <div>
            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#adjustModal">
                <i class="fas fa-sliders-h"></i> Adjust Stock
            </button>
            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#damageModal">
                <i class="fas fa-exclamation-triangle"></i> Record Damage
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
                <i class="fas fa-exchange-alt"></i> Transfer Stock
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summaryCards">
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumTotalProducts">0</h3><p class="text-muted mb-0">Total Products</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumTotalItems">0</h3><p class="text-muted mb-0">Total Items</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumTotalValue">$0</h3><p class="text-muted mb-0">Stock Value</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumOutOfStock" class="text-danger">0</h3><p class="text-muted mb-0">Out of Stock</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumLowStock" class="text-warning">0</h3><p class="text-muted mb-0">Low Stock</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat text-center p-3">
                <h3 id="sumOverstock" class="text-info">0</h3><p class="text-muted mb-0">Overstock</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search product...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="branchFilter">
                        <option value="">All Branches</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Stock Status</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                        <option value="overstock">Overstock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="loadStock()"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="stock-logs.php" class="btn btn-outline-secondary w-100"><i class="fas fa-history"></i> Stock Logs</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Product</th><th>SKU</th><th>Category</th>
                            <th>Branch</th><th>Qty</th><th>Min Stock</th><th>Reorder</th><th>Value</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stockTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Stock In</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="stockInForm">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Product *</label><select class="form-select" id="siProduct" required></select></div>
                <div class="mb-3"><label class="form-label">Branch *</label><select class="form-select" id="siBranch" required></select></div>
                <div class="mb-3"><label class="form-label">Quantity *</label><input type="number" class="form-control" id="siQty" min="1" required></div>
                <div class="mb-3"><label class="form-label">Notes</label><input type="text" class="form-control" id="siNotes"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Confirm Stock In</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Adjust Stock</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="adjustForm">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Product *</label><select class="form-select" id="adjProduct" required></select></div>
                <div class="mb-3"><label class="form-label">Branch *</label><select class="form-select" id="adjBranch" required></select></div>
                <div class="mb-3"><label class="form-label">New Quantity *</label><input type="number" class="form-control" id="adjQty" min="0" required></div>
                <div class="mb-3"><label class="form-label">Reason *</label><input type="text" class="form-control" id="adjReason" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Adjust</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Damage Modal -->
<div class="modal fade" id="damageModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Record Damaged Items</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="damageForm">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Product *</label><select class="form-select" id="dmgProduct" required></select></div>
                <div class="mb-3"><label class="form-label">Branch *</label><select class="form-select" id="dmgBranch" required></select></div>
                <div class="mb-3"><label class="form-label">Quantity *</label><input type="number" class="form-control" id="dmgQty" min="1" required></div>
                <div class="mb-3"><label class="form-label">Notes</label><input type="text" class="form-control" id="dmgNotes"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Record Damage</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Transfer Stock</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="transferForm">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Product *</label><select class="form-select" id="trProduct" required></select></div>
                <div class="mb-3"><label class="form-label">From Branch *</label><select class="form-select" id="trFrom" required></select></div>
                <div class="mb-3"><label class="form-label">To Branch *</label><select class="form-select" id="trTo" required></select></div>
                <div class="mb-3"><label class="form-label">Quantity *</label><input type="number" class="form-control" id="trQty" min="1" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Transfer</button>
            </div>
        </form>
    </div></div>
</div>

<script>
let currentPage = 1;

async function loadProductDropdowns() {
    const res = await apiRequest('../../api/products/index.php?per_page=500');
    if (!res.success) return;
    const opts = '<option value="">Select Product</option>' + res.data.products.map(p => `<option value="${p.id}">${p.name} (${p.sku}) - Stock: ${p.total_stock}</option>`).join('');
    ['siProduct','adjProduct','dmgProduct','trProduct'].forEach(id => document.getElementById(id).innerHTML = opts);
}

async function loadBranches() {
    const res = await apiRequest('../../api/inventory/branches.php');
    if (!res.success) return;
    const options = res.data.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    document.getElementById('branchFilter').innerHTML = '<option value="">All Branches</option>' + options;
    ['siBranch', 'adjBranch', 'dmgBranch'].forEach(id => document.getElementById(id).innerHTML = options);
    document.getElementById('trFrom').innerHTML = options;
    document.getElementById('trTo').innerHTML = options;
    syncModalBranches();
}

function syncModalBranches() {
    const branch = document.getElementById('branchFilter').value;
    if (!branch) return;
    ['siBranch', 'adjBranch', 'dmgBranch', 'trFrom'].forEach(id => document.getElementById(id).value = branch);
}

async function loadSummary() {
    const branch = document.getElementById('branchFilter').value;
    const res = await apiRequest(`../../api/inventory/summary.php?branch_id=${branch}`);
    if (!res.success) return;
    const s = res.data;
    document.getElementById('sumTotalProducts').textContent = s.total_products || 0;
    document.getElementById('sumTotalItems').textContent = s.total_items || 0;
    document.getElementById('sumTotalValue').textContent = '$' + parseFloat(s.total_value || 0).toFixed(2);
    document.getElementById('sumOutOfStock').textContent = s.out_of_stock || 0;
    document.getElementById('sumLowStock').textContent = s.low_stock || 0;
    document.getElementById('sumOverstock').textContent = s.overstock || 0;
}

async function loadStock(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({ page, search: document.getElementById('searchInput').value, status: document.getElementById('statusFilter').value, branch_id: document.getElementById('branchFilter').value });
    const res = await apiRequest(`../../api/inventory/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('stockTable').innerHTML = res.data.stock.map((s, i) => {
        const status = s.qty === 0 ? 'out' : s.qty <= s.reorder_level ? 'low' : 'ok';
        return `<tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td>${s.product_name}</td>
            <td><code>${s.sku}</code></td>
            <td>${s.category_name || '-'}</td>
            <td>${s.branch_name || '-'}</td>
            <td><strong>${s.qty} ${s.unit_symbol || ''}</strong></td>
            <td>${s.min_stock}</td>
            <td>${s.reorder_level}</td>
            <td>$${parseFloat(s.stock_value).toFixed(2)}</td>
            <td><span class="badge bg-${status==='out'?'danger':status==='low'?'warning':'success'}">${status==='out'?'Out':status==='low'?'Low':'OK'}</span></td>
            <td>
                <button class="btn btn-sm btn-success" onclick="quickStockIn(${s.product_id})" title="Stock In"><i class="fas fa-plus"></i></button>
                <button class="btn btn-sm btn-info" onclick="viewLogs(${s.product_id})" title="View Logs"><i class="fas fa-history"></i></button>
            </td>
        </tr>`;
    }).join('');

    renderPagination(res.data.pagination, loadStock);
}

function quickStockIn(productId) {
    document.getElementById('siProduct').value = productId;
    new bootstrap.Modal(document.getElementById('stockInModal')).show();
}

async function viewLogs(productId) {
    const res = await apiRequest(`../../api/inventory/logs.php?product_id=${productId}`);
    if (!res.success) return;
    const logs = res.data.logs.map(l => `${l.created_at} | ${l.type} | ${l.qty} | ${l.notes || ''}`).join('\n');
    alert(`Stock Logs:\n\n${logs || 'No logs found'}`);
}

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadStock();
});

document.getElementById('branchFilter').addEventListener('change', () => {
    syncModalBranches();
    loadStock(); loadSummary();
});

// Form submissions
document.getElementById('stockInForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await apiRequest('../../api/inventory/index.php', 'POST', { action: 'stock_in', product_id: +document.getElementById('siProduct').value, qty: +document.getElementById('siQty').value, branch_id: +document.getElementById('siBranch').value, notes: document.getElementById('siNotes').value });
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('stockInModal')).hide();
        loadStock(currentPage); loadSummary(); loadProductDropdowns();
    } else alert(res.message);
});

document.getElementById('adjustForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await apiRequest('../../api/inventory/index.php', 'POST', { action: 'adjust', product_id: +document.getElementById('adjProduct').value, new_qty: +document.getElementById('adjQty').value, branch_id: +document.getElementById('adjBranch').value, reason: document.getElementById('adjReason').value });
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('adjustModal')).hide();
        loadStock(currentPage); loadSummary();
    } else alert(res.message);
});

document.getElementById('damageForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await apiRequest('../../api/inventory/index.php', 'POST', { action: 'damage', product_id: +document.getElementById('dmgProduct').value, qty: +document.getElementById('dmgQty').value, branch_id: +document.getElementById('dmgBranch').value, notes: document.getElementById('dmgNotes').value });
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('damageModal')).hide();
        loadStock(currentPage); loadSummary();
    } else alert(res.message);
});

document.getElementById('transferForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await apiRequest('../../api/inventory/index.php', 'POST', { action: 'transfer', product_id: +document.getElementById('trProduct').value, from_branch: +document.getElementById('trFrom').value, to_branch: +document.getElementById('trTo').value, qty: +document.getElementById('trQty').value });
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
        loadStock(currentPage); loadSummary();
    } else alert(res.message);
});

loadProductDropdowns();
loadBranches();
loadSummary();
loadStock();
</script>

<?php require_once '../../includes/footer.php'; ?>
