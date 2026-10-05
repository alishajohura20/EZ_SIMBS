<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Suppliers';
requireLogin();
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Supplier Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="resetSupForm()">
            <i class="fas fa-plus"></i> Add Supplier
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search company, contact, email, phone...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary" onclick="loadSuppliers()">Filter</button>
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
                            <th>#</th><th>Company</th><th>Contact</th><th>Phone</th>
                            <th>Purchases</th><th>Outstanding</th><th>Balance</th><th>Rating</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supModalTitle">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="supplierForm">
                <div class="modal-body">
                    <input type="hidden" id="supId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-control" id="supCompany" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="supContact">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="supEmail">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="supPhone">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="supAddress" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tax ID</label>
                            <input type="text" class="form-control" id="supTaxId">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rating (0-5)</label>
                            <input type="number" class="form-control" id="supRating" min="0" max="5" step="0.1" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="supStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

async function loadSuppliers(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({ page, search: document.getElementById('searchInput').value, status: document.getElementById('statusFilter').value });
    const res = await apiRequest(`../../api/suppliers/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('supplierTable').innerHTML = res.data.suppliers.map((s, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><strong>${s.company_name}</strong><br><small class="text-muted">${s.email || ''}</small></td>
            <td>${s.contact_person || '-'}</td>
            <td>${s.phone || '-'}</td>
            <td>${s.purchase_count}</td>
            <td class="${s.outstanding_due > 0 ? 'text-danger fw-bold' : ''}">$${parseFloat(s.outstanding_due).toFixed(2)}</td>
            <td class="${parseFloat(s.balance||0) > 0 ? 'text-success fw-bold' : ''}">$${parseFloat(s.balance||0).toFixed(2)}</td>
            <td>${renderStars(s.rating)}</td>
            <td><span class="badge bg-${s.status==='active'?'success':'secondary'}">${s.status}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewSupplier(${s.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-warning" onclick="editSupplier(${s.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="confirmDelete('../../api/suppliers/index.php?id=${s.id}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadSuppliers);
}

function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fas fa-star" style="color:${i <= rating ? '#f59e0b' : '#ddd'}"></i>`;
    }
    return stars;
}

function resetSupForm() {
    document.getElementById('supId').value = '';
    document.getElementById('supplierForm').reset();
    document.getElementById('supModalTitle').textContent = 'Add Supplier';
}

async function editSupplier(id) {
    const res = await apiRequest(`../../api/suppliers/index.php?id=${id}`);
    if (!res.success) return;
    const s = res.data.supplier;
    document.getElementById('supId').value = s.id;
    document.getElementById('supCompany').value = s.company_name;
    document.getElementById('supContact').value = s.contact_person || '';
    document.getElementById('supEmail').value = s.email || '';
    document.getElementById('supPhone').value = s.phone || '';
    document.getElementById('supAddress').value = s.address || '';
    document.getElementById('supTaxId').value = s.tax_id || '';
    document.getElementById('supRating').value = s.rating || 0;
    document.getElementById('supStatus').value = s.status;
    document.getElementById('supModalTitle').textContent = 'Edit Supplier';
    new bootstrap.Modal(document.getElementById('supplierModal')).show();
}

async function viewSupplier(id) {
    const res = await apiRequest(`../../api/suppliers/index.php?id=${id}`);
    if (!res.success) return;
    const s = res.data;
    let details = `
        Company: ${s.supplier.company_name}
        Contact: ${s.supplier.contact_person || '-'}
        Email: ${s.supplier.email || '-'}
        Phone: ${s.supplier.phone || '-'}
        Outstanding: $${parseFloat(s.stats.total_due).toFixed(2)}
        Balance: $${parseFloat(s.supplier.balance||0).toFixed(2)}
        Total Purchases: ${s.stats.total_purchases}
        Products Supplied: ${s.products_supplied.length}
    `;
    alert(details);
}

document.getElementById('supplierForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('supId').value;
    const data = {
        company_name: document.getElementById('supCompany').value,
        contact_person: document.getElementById('supContact').value,
        email: document.getElementById('supEmail').value,
        phone: document.getElementById('supPhone').value,
        address: document.getElementById('supAddress').value,
        tax_id: document.getElementById('supTaxId').value,
        rating: document.getElementById('supRating').value,
        status: document.getElementById('supStatus').value,
    };
    const url = id ? `../../api/suppliers/index.php?id=${id}` : '../../api/suppliers/index.php';
    await apiRequest(url, id ? 'PUT' : 'POST', data);
    bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
    loadSuppliers(currentPage);
});

loadSuppliers();
</script>

<?php require_once '../../includes/footer.php'; ?>
