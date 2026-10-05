<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Sales';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Sales Management</h4>
        <button class="btn btn-success" onclick="window.location='pos.php'">
            <i class="fas fa-cash-register"></i> Open POS
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search invoice #, customer...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="partially_returned">Partially Returned</option>
                        <option value="returned">Returned</option>
                        <option value="held">Held</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="dateTo">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary" onclick="loadSales()">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Invoice #</th><th>Customer</th><th>Cashier</th><th>Total</th>
                            <th>Status</th><th>Date</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="saleTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>

    <?php require_once '../../includes/return-modal.php'; ?>
    <?php require_once '../../includes/exchange-modal.php'; ?>
</div>

<script>
let currentPage = 1;

async function loadSales(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page, search: document.getElementById('searchInput').value,
        status: document.getElementById('statusFilter').value,
        date_from: document.getElementById('dateFrom').value,
        date_to: document.getElementById('dateTo').value,
    });
    const res = await apiRequest(`../../api/sales/index.php?${params}`);
    if (!res.success) return;

    const statusColors = { completed: 'success', held: 'warning', cancelled: 'danger', returned: 'info', partially_returned: 'warning' };

    document.getElementById('saleTable').innerHTML = res.data.sales.map((s, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><code>${s.invoice_no}</code></td>
            <td>${s.customer_name ?? 'Walk-in'}</td>
            <td>${s.cashier_name ?? '-'}</td>
            <td>$${parseFloat(s.grand_total).toFixed(2)}</td>
            <td><span class="badge bg-${statusColors[s.status]}">${s.status}</span></td>
            <td>${new Date(s.created_at).toLocaleString()}</td>
            <td>
                <a href="receipt.php?id=${s.id}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Receipt"><i class="fas fa-receipt"></i></a>
                ${['completed','partially_returned'].includes(s.status) ? `<button class="btn btn-sm btn-outline-info" onclick="openReturnModal(${s.id})" title="Return items"><i class="fas fa-undo-alt"></i></button>` : ''}
                ${['completed','partially_returned'].includes(s.status) ? `<button class="btn btn-sm btn-outline-warning" onclick="openExchangeForSale(${s.id})" title="Exchange items (return + resale)"><i class="fas fa-exchange-alt"></i></button>` : ''}
                ${s.status === 'completed' ? `<button class="btn btn-sm btn-warning" onclick="cancelSale(${s.id})" title="Cancel"><i class="fas fa-times"></i></button>` : ''}
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadSales);
}

async function cancelSale(id) {
    if (!confirm('Cancel this sale? Stock will be restored.')) return;
    const res = await apiRequest(`../../api/sales/index.php?id=${id}`, 'DELETE');
    if (res.success) loadSales(currentPage);
    else alert(res.message);
}

loadSales();
</script>

<?php require_once '../../includes/footer.php'; ?>
