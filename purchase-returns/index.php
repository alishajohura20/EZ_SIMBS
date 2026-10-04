<?php
$pageTitle = 'Supplier Returns';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-truck-loading me-2"></i>Supplier Returns</h2>
        <?php if (in_array($_SESSION['user_role'], ['admin', 'manager'], true)): ?>
        <a href="../../api/reports/purchase-returns.php?export=csv" class="btn btn-outline-secondary btn-sm" id="exportCsv">
            <i class="fas fa-file-csv me-1"></i>Export
        </a>
        <?php endif; ?>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><input id="searchInput" class="form-control" placeholder="Return no, PO, supplier..."></div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="requested">Requested</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="received">Received</option>
                        <option value="credited">Credited</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="reasonFilter" class="form-select">
                        <option value="">All Reasons</option>
                        <?php foreach (['defective','damaged','wrong_stock','overstock','expired','other'] as $rc): ?>
                        <option value="<?= $rc ?>"><?= ucwords(str_replace('_',' ',$rc)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><input type="date" id="dateFrom" class="form-control"></div>
                <div class="col-md-2"><input type="date" id="dateTo" class="form-control"></div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" id="filterBtn"><i class="fas fa-filter"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Return No</th><th>PO</th><th>Supplier</th><th>Reason</th>
                        <th class="text-end">Items</th><th class="text-end">Credit</th>
                        <th>Status</th><th>Branch</th><th>Date</th><th></th>
                    </tr>
                </thead>
                <tbody id="returnTable"></tbody>
            </table>
        </div>
    </div>

    <div id="paginationHost" class="mt-3"></div>
</div>

<script>
const statusColors = {
    requested: 'warning', approved: 'info', rejected: 'secondary',
    received: 'primary', credited: 'success', cancelled: 'dark'
};
let currentPage = 1;

async function loadReturns(page = 1) {
    currentPage = page;
    const p = new URLSearchParams({
        page,
        search: document.getElementById('searchInput').value,
        status: document.getElementById('statusFilter').value,
        reason_code: document.getElementById('reasonFilter').value,
        date_from: document.getElementById('dateFrom').value,
        date_to: document.getElementById('dateTo').value,
    });
    const res = await apiRequest('../../api/purchase-returns/index.php?' + p);
    if (!res.success) return;

    document.getElementById('returnTable').innerHTML = res.data.returns.map(r => `
        <tr>
            <td><code>${r.return_no}</code></td>
            <td><code>${r.po_number || '-'}</code></td>
            <td>${r.supplier_name || '-'}</td>
            <td class="text-capitalize">${(r.reason_code || '-').replace(/_/g,' ')}</td>
            <td class="text-end">${r.total_qty}</td>
            <td class="text-end">$${Number(r.credit_total).toFixed(2)}</td>
            <td><span class="badge bg-${statusColors[r.status] || 'secondary'}">${r.status}</span></td>
            <td class="small text-muted">${r.branch_name || '-'}</td>
            <td class="small text-muted">${new Date(r.created_at).toLocaleString()}</td>
            <td class="text-end">
                <a href="view.php?id=${r.id}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i></a>
            </td>
        </tr>`).join('');

    renderPagination(res.data.pagination, loadReturns);
}

['searchInput','statusFilter','reasonFilter','dateFrom','dateTo'].forEach(id =>
    document.getElementById(id).addEventListener('change', () => loadReturns(1)));
document.getElementById('filterBtn').addEventListener('click', () => loadReturns(1));

loadReturns();
</script>

<?php require_once '../../includes/footer.php'; ?>