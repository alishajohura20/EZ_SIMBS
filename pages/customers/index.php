<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Customers';
requireLogin();
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Customer Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal" onclick="resetCustForm()">
            <i class="fas fa-plus"></i> Add Customer
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search name, phone, email, membership...">
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="vipFilter">
                        <label class="form-check-label">VIP Only</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary" onclick="loadCustomers()">Filter</button>
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
                            <th>#</th><th>Name</th><th>Phone</th><th>Membership</th>
                            <th>Points</th><th>Total Spent</th><th>VIP</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customerTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="custModalTitle">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customerForm">
                <div class="modal-body">
                    <input type="hidden" id="custId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="custName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="custPhone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="custEmail">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Membership ID (auto)</label>
                            <input type="text" class="form-control" id="custMembership" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="custAddress" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Loyalty Points</label>
                            <input type="number" class="form-control" id="custPoints" value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="custVip">
                                <label class="form-check-label">VIP Customer</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="custStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="custNotes" rows="2"></textarea>
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

async function loadCustomers(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page, search: document.getElementById('searchInput').value,
        is_vip: document.getElementById('vipFilter').checked ? '1' : '',
    });
    const res = await apiRequest(`../../api/customers/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('customerTable').innerHTML = res.data.customers.map((c, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td><strong>${c.name}</strong><br><small class="text-muted">${c.email || ''}</small></td>
            <td>${c.phone || '-'}</td>
            <td><code>${c.membership_id}</code></td>
            <td><span class="badge bg-info">${c.loyalty_points} pts</span></td>
            <td>$${parseFloat(c.total_spent).toFixed(2)}</td>
            <td>${c.is_vip ? '<i class="fas fa-crown text-warning"></i>' : '-'}</td>
            <td><span class="badge bg-${c.status==='active'?'success':'secondary'}">${c.status}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewCustomer(${c.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-sm btn-warning" onclick="editCustomer(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="confirmDelete('../../api/customers/index.php?id=${c.id}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadCustomers);
}

function resetCustForm() {
    document.getElementById('custId').value = '';
    document.getElementById('customerForm').reset();
    document.getElementById('custMembership').value = 'MEM-' + Date.now().toString(36).toUpperCase();
    document.getElementById('custModalTitle').textContent = 'Add Customer';
}

async function editCustomer(id) {
    const res = await apiRequest(`../../api/customers/index.php?id=${id}`);
    if (!res.success) return;
    const c = res.data.customer;
    document.getElementById('custId').value = c.id;
    document.getElementById('custName').value = c.name;
    document.getElementById('custPhone').value = c.phone || '';
    document.getElementById('custEmail').value = c.email || '';
    document.getElementById('custMembership').value = c.membership_id;
    document.getElementById('custAddress').value = c.address || '';
    document.getElementById('custPoints').value = c.loyalty_points;
    document.getElementById('custVip').checked = c.is_vip;
    document.getElementById('custStatus').value = c.status;
    document.getElementById('custNotes').value = c.notes || '';
    document.getElementById('custModalTitle').textContent = 'Edit Customer';
    new bootstrap.Modal(document.getElementById('customerModal')).show();
}

async function viewCustomer(id) {
    const res = await apiRequest(`../../api/customers/index.php?id=${id}`);
    if (!res.success) return;
    const c = res.data;
    const info = `
        Name: ${c.customer.name}
        Phone: ${c.customer.phone || '-'}
        Membership: ${c.customer.membership_id}
        Loyalty Points: ${c.customer.loyalty_points}
        Total Orders: ${c.stats.total_orders}
        Total Spent: $${parseFloat(c.stats.total_spent).toFixed(2)}
        Avg Order: $${parseFloat(c.stats.avg_order).toFixed(2)}
    `;
    alert(info);
}

document.getElementById('customerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('custId').value;
    const data = {
        name: document.getElementById('custName').value,
        phone: document.getElementById('custPhone').value,
        email: document.getElementById('custEmail').value,
        membership_id: document.getElementById('custMembership').value,
        address: document.getElementById('custAddress').value,
        loyalty_points: document.getElementById('custPoints').value,
        is_vip: document.getElementById('custVip').checked ? 1 : 0,
        status: document.getElementById('custStatus').value,
        notes: document.getElementById('custNotes').value,
    };
    const url = id ? `../../api/customers/index.php?id=${id}` : '../../api/customers/index.php';
    await apiRequest(url, id ? 'PUT' : 'POST', data);
    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
    loadCustomers(currentPage);
});

loadCustomers();
</script>

<?php require_once '../../includes/footer.php'; ?>
