<?php
$pageTitle = 'Branches';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Branch Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#branchModal" onclick="resetBranchForm()">
            <i class="fas fa-plus"></i> Add Branch
        </button>
    </div>

    <div class="row mb-4" id="branchCards"></div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Branch</th><th>Address</th><th>Manager</th><th>Products</th><th>Stock</th><th>Today Sales</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="branchTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="branchModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="branchModalTitle">Add Branch</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="branchForm">
            <div class="modal-body">
                <input type="hidden" id="branchId">
                <div class="mb-3"><label class="form-label">Branch Name *</label><input type="text" class="form-control" id="branchName" required></div>
                <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" id="branchAddress" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" id="branchPhone"></div>
                <div class="mb-3"><label class="form-label">Manager</label><select class="form-select" id="branchManager"><option value="">Select Manager</option></select></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div></div>
</div>

<script>
let currentPage = 1;

async function loadBranches(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({ page, search: '' });
    const res = await apiRequest(`../../api/branches/index.php?${params}`);
    if (!res.success) return;
    const branches = res.data.branches || res.data;

    document.getElementById('branchCards').innerHTML = branches.map(b => `
        <div class="col-md-3 mb-3">
            <div class="card card-stat" style="cursor:pointer" onclick="viewBranch(${b.id})">
                <h5>${b.name}</h5>
                <p class="mb-1">Products: ${b.product_count}</p>
                <p class="mb-1">Stock: ${b.total_stock}</p>
                <p class="mb-0">Today: $${parseFloat(b.today_revenue).toFixed(2)}</p>
            </div>
        </div>
    `).join('');

    document.getElementById('branchTable').innerHTML = branches.map((b, i) => `
        <tr>
            <td>${(page-1)*10 + i + 1}</td>
            <td><strong>${b.name}</strong></td>
            <td>${b.address || '-'}</td>
            <td>${b.manager_name || '-'}</td>
            <td>${b.product_count}</td>
            <td>${b.total_stock}</td>
            <td>$${parseFloat(b.today_revenue).toFixed(2)}</td>
            <td><span class="badge bg-${b.status==='active'?'success':'secondary'}">${b.status}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewBranch(${b.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-warning" onclick="editBranch(${b.id})"><i class="fas fa-edit"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadBranches);
}

function resetBranchForm() {
    document.getElementById('branchId').value = '';
    document.getElementById('branchForm').reset();
    document.getElementById('branchModalTitle').textContent = 'Add Branch';
}

async function loadManagers() {
    const usersRes = await apiRequest('../../api/users/index.php?role=branch_manager');
    if (usersRes.success) {
        document.getElementById('branchManager').innerHTML = '<option value="">Select Manager</option>' +
            usersRes.data.users.map(u => `<option value="${u.id}">${u.name}</option>`).join('');
    }
}

async function editBranch(id) {
    const res = await apiRequest(`../../api/branches/index.php?id=${id}`);
    if (!res.success) return;
    const b = res.data.branch;
    document.getElementById('branchId').value = b.id;
    document.getElementById('branchName').value = b.name;
    document.getElementById('branchAddress').value = b.address || '';
    document.getElementById('branchPhone').value = b.phone || '';
    document.getElementById('branchManager').value = b.manager_id || '';
    document.getElementById('branchModalTitle').textContent = 'Edit Branch';
    new bootstrap.Modal(document.getElementById('branchModal')).show();
}

function viewBranch(id) { window.location.href = `view.php?id=${id}`; }

document.getElementById('branchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('branchId').value;
    const data = { name: document.getElementById('branchName').value, address: document.getElementById('branchAddress').value, phone: document.getElementById('branchPhone').value, manager_id: document.getElementById('branchManager').value };
    const url = id ? `../../api/branches/index.php?id=${id}` : '../../api/branches/index.php';
    await apiRequest(url, id ? 'PUT' : 'POST', data);
    bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
    loadBranches();
});

loadBranches();
loadManagers();
</script>

<?php require_once '../../includes/footer.php'; ?>
