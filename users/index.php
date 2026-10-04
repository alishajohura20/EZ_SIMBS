<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin']);
$pageTitle = 'User Management';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>User Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search name or email...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="branch_manager">Branch Manager</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="locked">Locked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" onclick="loadUsers()">Filter</button>
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
                            <th>#</th><th>Name</th><th>Email</th><th>Role</th>
                            <th>Branch</th><th>Status</th><th>Last Login</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="userName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" id="userEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="userPhone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span id="passwordNote">*</span></label>
                        <input type="password" class="form-control" id="userPassword">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select class="form-select" id="userRole" required>
                            <option value="">Select Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Manager</option>
                            <option value="3">Branch Manager</option>
                            <option value="4">Cashier</option>
                            <option value="5">Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="userStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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

async function loadUsers(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;

    const res = await apiRequest(`../../api/users/index.php?page=${page}&search=${search}&role=${role}&status=${status}`);
    if (!res.success) return;

    const tbody = document.getElementById('usersTable');
    tbody.innerHTML = res.data.users.map((u, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td><span class="badge bg-primary">${u.role_name}</span></td>
            <td>${u.branch_name || '-'}</td>
            <td><span class="badge bg-${u.status==='active'?'success':u.status==='locked'?'danger':'secondary'}">${u.status}</span></td>
            <td>${u.last_login ? new Date(u.last_login).toLocaleString() : 'Never'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editUser(${u.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadUsers);
}

function resetForm() {
    document.getElementById('userId').value = '';
    document.getElementById('userForm').reset();
    document.getElementById('modalTitle').textContent = 'Add User';
    document.getElementById('passwordNote').textContent = '*';
}

async function editUser(id) {
    const res = await apiRequest(`../../api/users/${id}.php`);
    if (!res.success) return;
    const u = res.data;
    document.getElementById('userId').value = u.id;
    document.getElementById('userName').value = u.name;
    document.getElementById('userEmail').value = u.email;
    document.getElementById('userPhone').value = u.phone || '';
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('passwordNote').textContent = '(leave blank to keep)';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

document.getElementById('userForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('userId').value;
    const data = {
        name: document.getElementById('userName').value,
        email: document.getElementById('userEmail').value,
        phone: document.getElementById('userPhone').value,
        role_id: document.getElementById('userRole').value,
        status: document.getElementById('userStatus').value,
        password: document.getElementById('userPassword').value,
    };

    const url = id ? `../../api/users/${id}.php` : '../../api/users/index.php';
    const method = id ? 'PUT' : 'POST';
    const res = await apiRequest(url, method, data);

    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        loadUsers(currentPage);
    } else {
        alert(res.message);
    }
});

async function deleteUser(id) {
    if (!confirm('Delete this user?')) return;
    const res = await apiRequest(`../../api/users/${id}.php`, 'DELETE');
    if (res.success) loadUsers(currentPage);
}

loadUsers();
</script>

<?php require_once '../../includes/footer.php'; ?>
