<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$pageTitle = 'Units';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Units</h4>
            <small class="text-muted" id="resultCount"></small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitModal" onclick="resetUnitForm()">
            <i class="fas fa-plus"></i> Add Unit
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5 col-lg-4">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search by name or symbol...">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small text-muted mb-1">Products</label>
                    <select class="form-select" id="usedFilter">
                        <option value="">All Units</option>
                        <option value="used">Used by products</option>
                        <option value="unused">Not used</option>
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
                    <thead><tr><th>#</th><th>Name</th><th>Symbol</th><th>Products</th><th>Actions</th></tr></thead>
                    <tbody id="unitTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitModalTitle">Add Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="unitForm">
                <div class="modal-body">
                    <input type="hidden" id="unitId">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" id="unitName" placeholder="e.g. Kilogram" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Symbol *</label>
                        <input type="text" class="form-control" id="unitSymbol" placeholder="e.g. kg" required>
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
let allUnits = [];

async function loadUnits(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const used = document.getElementById('usedFilter').value;
    const res = await apiRequest(`../../api/units/index.php?search=${encodeURIComponent(search)}&used=${encodeURIComponent(used)}`);
    if (!res.success) return;

    allUnits = res.data;
    const perPage = 20;
    const totalPages = Math.ceil(allUnits.length / perPage);
    const start = (page - 1) * perPage;
    const pageData = allUnits.slice(start, start + perPage);

    document.getElementById('unitTable').innerHTML = pageData.map((u, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td>${u.name}</td>
            <td><span class="badge bg-secondary">${u.symbol}</span></td>
            <td><span class="badge ${u.product_count > 0 ? 'bg-info' : 'bg-light text-muted'}">${u.product_count}</span></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editUnit(${u.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteUnit(${u.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination({ current_page: currentPage, total_pages: totalPages, per_page: perPage, total: allUnits.length }, loadUnits);

    const countEl = document.getElementById('resultCount');
    if (countEl) countEl.textContent = `${allUnits.length} unit${allUnits.length === 1 ? '' : 's'} found`;
}

function resetUnitForm() {
    document.getElementById('unitId').value = '';
    document.getElementById('unitForm').reset();
    document.getElementById('unitModalTitle').textContent = 'Add Unit';
}

async function editUnit(id) {
    const res = await apiRequest(`../../api/units/index.php?id=${id}`);
    if (!res.success) return;
    document.getElementById('unitId').value = res.data.id;
    document.getElementById('unitName').value = res.data.name;
    document.getElementById('unitSymbol').value = res.data.symbol;
    document.getElementById('unitModalTitle').textContent = 'Edit Unit';
    new bootstrap.Modal(document.getElementById('unitModal')).show();
}

async function deleteUnit(id) {
    if (!confirm('Delete this unit?')) return;
    const res = await apiRequest(`../../api/units/index.php?id=${id}`, 'DELETE');
    if (res.success) loadUnits();
}

document.getElementById('unitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('unitId').value;
    const data = {
        name: document.getElementById('unitName').value,
        symbol: document.getElementById('unitSymbol').value
    };
    const url = id ? `../../api/units/index.php?id=${id}` : '../../api/units/index.php';
    const method = id ? 'PUT' : 'POST';
    const res = await apiRequest(url, method, data);
    if (res.success) {
        bootstrap.Modal.getInstance(document.getElementById('unitModal')).hide();
        loadUnits();
    } else {
        alert(res.message);
    }
});

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadUnits();
});

let searchTimer;
document.getElementById('searchInput').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadUnits(), 300);
});

document.getElementById('usedFilter').addEventListener('change', () => loadUnits());

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('usedFilter').value = '';
    loadUnits();
}

loadUnits();
</script>

<?php require_once '../../includes/footer.php'; ?>
