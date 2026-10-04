<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Returns';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
$isCashier = ($_SESSION['user_role'] ?? '') === 'cashier';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Sales Returns &amp; Refunds</h4>
        <?php if (!$isCashier): ?>
        <a href="../../api/reports/returns.php?export=csv" class="btn btn-outline-secondary">
            <i class="fas fa-download"></i> Export
        </a>
        <?php endif; ?>
    </div>

    <?php if ($isCashier): ?>
    <div class="alert alert-info py-2" style="border-radius:12px">
        <i class="fas fa-code-branch me-2"></i>Showing returns for your branch only.
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4" id="returnStatCards"></div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search return #, invoice #, order #, customer...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="requested">Requested</option>
                        <option value="approved">Approved</option>
                        <option value="received">Received</option>
                        <option value="refunded">Refunded</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="pos">In-store</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <?php if (!$isCashier): ?>
                <div class="col-md-2">
                    <select class="form-select" id="branchFilter">
                        <option value="">All Branches</option>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" onclick="loadReturns()"><i class="fas fa-filter"></i> Filter</button>
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
                            <th>#</th><th>Return #</th><th>Source</th><th>Customer</th><th>Branch</th>
                            <th>Reason</th><th>Status</th><th>Requested</th><th>Refund</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="returnTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>

    <?php require_once '../../includes/return-modal.php'; ?>
</div>

<script>
let currentPage = 1;

const statusColors = { requested: 'warning', approved: 'info', received: 'primary', refunded: 'success', rejected: 'danger', cancelled: 'secondary' };

async function loadBranches() {
    const res = await apiRequest('../../api/branches/index.php');
    if (!res.success) return;
    const sel = document.getElementById('branchFilter');
    (res.data.branches || []).forEach(b => {
        const o = document.createElement('option');
        o.value = b.id; o.textContent = b.name;
        sel.appendChild(o);
    });
}

async function loadStats() {
    const res = await apiRequest('../../api/reports/returns.php?summary=1');
    if (!res.success || !res.data) return;
    const c = res.data.counts || {};
    const m = res.data.monthly || {};
    document.getElementById('returnStatCards').innerHTML = [
        ['Waiting approval', c.requested || 0, 'warning', 'fas fa-hourglass-half'],
        ['Approved / to receive', c.approved || 0, 'info', 'fas fa-box-open'],
        ['Received / to refund', c.received || 0, 'primary', 'fas fa-undo-alt'],
        ['Refunded this month', m.refunded_this_month != null ? '$' + parseFloat(m.refunded_this_month).toFixed(2) : '—', 'success', 'fas fa-money-bill-wave'],
    ].map(s => `
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">${s[0]}</div>
                            <h5 class="mb-0 mt-1">${s[1]}</h5>
                        </div>
                        <i class="fas ${s[3]} fa-2x text-${s[2]} opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>`).join('');
}

async function loadReturns(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page,
        search: document.getElementById('searchInput').value,
        status: document.getElementById('statusFilter').value,
        type: document.getElementById('typeFilter').value,
        branch_id: document.getElementById('branchFilter') ? document.getElementById('branchFilter').value : '',
        date_from: document.getElementById('dateFrom') ? document.getElementById('dateFrom').value : '',
        date_to: document.getElementById('dateTo') ? document.getElementById('dateTo').value : '',
    });
    const res = await apiRequest(`../../api/returns/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('returnTable').innerHTML = res.data.returns.map((r, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><a href="view.php?id=${r.id}" class="fw-semibold text-decoration-none"><code>${r.return_no}</code></a></td>
            <td><span class="badge bg-${r.type === 'online' ? 'dark' : 'light border'}">${r.type === 'online' ? (r.order_no || 'Order') : (r.invoice_no || 'Sale')}</span></td>
            <td>${r.customer_name ?? '-'}</td>
            <td>${r.branch_name ?? '-'}</td>
            <td><span class="badge bg-secondary bg-opacity-10 text-dark" title="${(r.reason || '').replace(/"/g, '&quot;')}">${r.reason_code || '-'}</span></td>
            <td><span class="badge bg-${statusColors[r.status]}">${r.status}</span></td>
            <td>${new Date(r.created_at).toLocaleString()}</td>
            <td><strong>$${parseFloat(r.refund_total).toFixed(2)}</strong></td>
            <td>
                <a href="view.php?id=${r.id}" class="btn btn-sm btn-outline-primary" title="Open"><i class="fas fa-eye"></i></a>
            </td>
        </tr>`).join('');

    renderPagination(res.data.pagination, loadReturns);
}

loadBranches();
loadStats();
loadReturns();

const __saleId = new URLSearchParams(location.search).get('sale_id');
if (__saleId) openReturnModal(parseInt(__saleId, 10));
</script>

<?php require_once '../../includes/footer.php'; ?>