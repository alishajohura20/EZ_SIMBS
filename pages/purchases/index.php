<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Purchases';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Purchase Management</h4>
        <button class="btn btn-primary" onclick="window.location='create.php'">
            <i class="fas fa-plus"></i> New Purchase Order
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search PO #, supplier...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="received">Received</option>
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
                    <button class="btn btn-outline-primary" onclick="loadPurchases()">Filter</button>
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
                            <th>#</th><th>PO Number</th><th>Supplier</th><th>Total</th>
                            <th>Paid</th><th>Due</th><th>Status</th><th>Date</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<?php require_once '../../includes/purchase-return-modal.php'; ?>

<script>
let currentPage = 1;
const USER_CAN_RETURN = <?= in_array($_SESSION['user_role'] ?? '', ['admin','manager','branch_manager']) ? 'true' : 'false' ?>;

async function loadPurchases(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page, search: document.getElementById('searchInput').value,
        status: document.getElementById('statusFilter').value,
        date_from: document.getElementById('dateFrom').value,
        date_to: document.getElementById('dateTo').value,
    });
    const res = await apiRequest(`../../api/purchases/index.php?${params}`);
    if (!res.success) return;

    const statusColors = { draft: 'secondary', pending: 'warning', approved: 'info', received: 'success', cancelled: 'danger' };

    document.getElementById('purchaseTable').innerHTML = res.data.purchases.map((p, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><code>${p.po_number}</code></td>
            <td>${p.supplier_name}</td>
            <td>$${parseFloat(p.total).toFixed(2)}</td>
            <td>$${parseFloat(p.paid).toFixed(2)}</td>
            <td class="${p.due > 0 ? 'text-danger fw-bold' : ''}">$${parseFloat(p.due).toFixed(2)}</td>
            <td><span class="badge bg-${statusColors[p.status]}">${p.status}</span></td>
            <td>${new Date(p.created_at).toLocaleDateString()}</td>
            <td>
                <a href="view.php?id=${p.id}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                ${['draft','pending'].includes(p.status) ? `<button class="btn btn-sm btn-success" onclick="updateStatus(${p.id},'approved')"><i class="fas fa-check"></i></button>` : ''}
                ${p.status === 'approved' ? `<button class="btn btn-sm btn-primary" onclick="updateStatus(${p.id},'received')"><i class="fas fa-truck"></i></button>` : ''}
                ${p.status === 'received' && USER_CAN_RETURN ? `<button class="btn btn-sm btn-warning" onclick="openPurchaseReturnModal(${p.id})" title="Return to supplier"><i class="fas fa-truck-loading"></i></button>` : ''}
                ${p.status !== 'cancelled' && p.status !== 'received' ? `<button class="btn btn-sm btn-danger" onclick="updateStatus(${p.id},'cancelled')"><i class="fas fa-times"></i></button>` : ''}
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadPurchases);
}

async function updateStatus(id, status) {
    const msg = { approved: 'Approve', received: 'Mark as Received', cancelled: 'Cancel' };
    if (!confirm(`${msg[status]} this purchase order?`)) return;
    const res = await apiRequest('../../api/purchases/status.php', 'POST', { purchase_id: id, status });
    if (res.success) loadPurchases(currentPage);
    else alert(res.message);
}

loadPurchases();
</script>

<?php require_once '../../includes/footer.php'; ?>
