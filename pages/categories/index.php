<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$pageTitle = 'Categories';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Categories</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetCatForm()">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search categories...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" onclick="loadCategories()">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Parent</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="catTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="catModalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="catForm">
                <div class="modal-body">
                    <input type="hidden" id="catId">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" id="catName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" id="catParent"><option value="">None</option></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="catStatus">
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
let allCategories = [];

async function loadCategories(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const res = await apiRequest(`../../api/categories/index.php?search=${encodeURIComponent(search)}&status=${status}`);
    if (!res.success) return;

    allCategories = res.data;
    const perPage = 20;
    const totalPages = Math.ceil(allCategories.length / perPage);
    const start = (page - 1) * perPage;
    const pageData = allCategories.slice(start, start + perPage);

    document.getElementById('catTable').innerHTML = pageData.map((c, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td>${c.name}</td>
            <td>${c.parent_name || '-'}</td>
            <td>${c.product_count}</td>
            <td><span class="badge bg-${c.status==='active'?'success':'secondary'}">${c.status}</span></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editCategory(${c.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteCategory(${c.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination({ current_page: currentPage, total_pages: totalPages, per_page: perPage, total: allCategories.length }, loadCategories);
}

function resetCatForm() {
    document.getElementById('catId').value = '';
    document.getElementById('catForm').reset();
    document.getElementById('catModalTitle').textContent = 'Add Category';
    loadParentOptions();
}

async function loadParentOptions() {
    const res = await apiRequest('../../api/categories/index.php?status=active');
    if (!res.success) return;
    const current = document.getElementById('catId').value;
    const select = document.getElementById('catParent');
    select.innerHTML = '<option value="">None</option>' + res.data
        .filter(c => c.id != current)
        .map(c => `<option value="${c.id}">${c.name}</option>`).join('');
}

async function editCategory(id) {
    const res = await apiRequest(`../../api/categories/index.php?id=${id}`);
    if (!res.success) return;
    document.getElementById('catId').value = res.data.id;
    document.getElementById('catName').value = res.data.name;
    document.getElementById('catStatus').value = res.data.status;
    document.getElementById('catModalTitle').textContent = 'Edit Category';
    await loadParentOptions();
    document.getElementById('catParent').value = res.data.parent_id || '';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

async function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    const res = await apiRequest(`../../api/categories/index.php?id=${id}`, 'DELETE');
    if (res.success) loadCategories();
}

document.getElementById('catForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('catId').value;
    const data = {
        name: document.getElementById('catName').value,
        parent_id: document.getElementById('catParent').value,
        status: document.getElementById('catStatus').value
    };
    const url = id ? `../../api/categories/index.php?id=${id}` : '../../api/categories/index.php';
    const method = id ? 'PUT' : 'POST';
    const res = await apiRequest(url, method, data);
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        loadCategories();
    } else {
        alert(res.message);
    }
});

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadCategories();
});

loadCategories();
</script>

<?php require_once '../../includes/footer.php'; ?>
