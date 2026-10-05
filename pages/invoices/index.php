<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Invoices';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Invoice Management</h4>
        <button class="btn btn-success" onclick="window.location='../sales/pos.php'">
            <i class="fas fa-cash-register"></i> Open POS
        </button>
    </div>
    <?php if (($_SESSION['user_role'] ?? '') === 'cashier'): ?>
    <div class="alert alert-info py-2" style="border-radius:12px">
        <i class="fas fa-code-branch me-2"></i>Showing invoices for your branch only.
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search invoice #, customer...">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="dateTo">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-outline-primary" onclick="loadInvoices()">Filter</button>
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
                            <th>#</th><th>Invoice #</th><th>Customer</th><th>Branch</th><th>Total</th>
                            <th>Date</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>

    <?php require_once '../../includes/return-modal.php'; ?>
</div>

<script>
let currentPage = 1;

async function loadInvoices(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page, search: document.getElementById('searchInput').value,
        date_from: document.getElementById('dateFrom').value,
        date_to: document.getElementById('dateTo').value,
        branch_id: '<?= ($_SESSION['user_role'] ?? '') === 'cashier' ? (int)($_SESSION['user_branch'] ?? 0) : '' ?>',
    });
    const res = await apiRequest(`../../api/invoices/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('invoiceTable').innerHTML = res.data.invoices.map((iv, i) => {
        const total = parseFloat(iv.grand_total);
        const totalHtml = isFinite(total) ? '$' + total.toFixed(2) : '—';
        const dateHtml = iv.sale_date ? new Date(iv.sale_date).toLocaleString() : '—';
        const receipt = iv.sale_id ? `<a href="../sales/receipt.php?id=${iv.sale_id}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Receipt"><i class="fas fa-receipt"></i></a>
                <a href="print.php?id=${iv.sale_id}" target="_blank" class="btn btn-sm btn-primary" title="PDF Invoice"><i class="fas fa-file-pdf"></i></a>
                <button class="btn btn-sm btn-outline-info" onclick="openReturnModal(${iv.sale_id})" title="Return items"><i class="fas fa-undo-alt"></i></button>` : '';
        return `<tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><code>${iv.invoice_no ?? '—'}</code></td>
            <td>${iv.customer_name ?? 'Walk-in'}</td>
            <td>${iv.branch_name ?? '-'}</td>
            <td>${totalHtml}</td>
            <td>${dateHtml}</td>
            <td>${receipt}</td>
        </tr>`;
    }).join('');

    renderPagination(res.data.pagination, loadInvoices);
}

loadInvoices();
</script>

<?php require_once '../../includes/footer.php'; ?>
