<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$pageTitle = 'Brands';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Brands</h4>
            <small class="text-muted" id="resultCount"></small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="resetBrandForm()">
            <i class="fas fa-plus"></i> Add Brand
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5 col-lg-4">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search brands...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-lg-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary" title="Reset filters" onclick="resetFilters()"><i class="fas fa-undo"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="brandTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalTitle">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="brandForm">
                <div class="modal-body">
                    <input type="hidden" id="brandId">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" id="brandName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="brandStatus">
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
let allBrands = [];

async function loadBrands(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const res = await apiRequest(`../../api/brands/index.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`);
    if (!res.success) return;

    allBrands = res.data;
    const perPage = 20;
    const totalPages = Math.ceil(allBrands.length / perPage);
    const start = (page - 1) * perPage;
    const pageData = allBrands.slice(start, start + perPage);

    document.getElementById('brandTable').innerHTML = pageData.map((b, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td>${b.name}</td>
            <td>${b.product_count}</td>
            <td><span class="badge bg-${b.status==='active'?'success':'secondary'}">${b.status}</span></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editBrand(${b.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteBrand(${b.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination({ current_page: currentPage, total_pages: totalPages, per_page: perPage, total: allBrands.length }, loadBrands);

    const countEl = document.getElementById('resultCount');
    if (countEl) countEl.textContent = `${allBrands.length} brand${allBrands.length === 1 ? '' : 's'} found`;
}

function resetBrandForm() {
    document.getElementById('brandId').value = '';
    document.getElementById('brandForm').reset();
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
}

async function editBrand(id) {
    const res = await apiRequest(`../../api/brands/index.php?id=${id}`);
    if (!res.success) return;
    document.getElementById('brandId').value = res.data.id;
    document.getElementById('brandName').value = res.data.name;
    document.getElementById('brandStatus').value = res.data.status;
    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    new bootstrap.Modal(document.getElementById('brandModal')).show();
}

async function deleteBrand(id) {
    if (!confirm('Delete this brand?')) return;
    const res = await apiRequest(`../../api/brands/index.php?id=${id}`, 'DELETE');
    if (res.success) loadBrands();
}

document.getElementById('brandForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('brandId').value;
    const data = {
        name: document.getElementById('brandName').value,
        status: document.getElementById('brandStatus').value
    };
    const url = id ? `../../api/brands/index.php?id=${id}` : '../../api/brands/index.php';
    const method = id ? 'PUT' : 'POST';
    const res = await apiRequest(url, method, data);
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('brandModal')).hide();
        loadBrands();
    } else {
        alert(res.message);
    }
});

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadBrands();
});

let searchTimer;
document.getElementById('searchInput').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadBrands(), 300);
});

document.getElementById('statusFilter').addEventListener('change', () => loadBrands());

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    loadBrands();
}

loadBrands();
</script>

<?php require_once '../../includes/footer.php'; ?>
