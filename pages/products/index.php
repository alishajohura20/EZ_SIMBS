<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
$pageTitle = 'Products';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Products</h4>
        <div>
            <button class="btn btn-outline-secondary" onclick="document.getElementById('barcodeInput').focus()">
                <i class="fas fa-barcode"></i> Scan Barcode
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetProdForm()">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </div>
    </div>

    <!-- Barcode Scanner Input -->
    <input type="text" id="barcodeInput" class="form-control mb-3" placeholder="Scan or type barcode..." autofocus
           onkeypress="if(event.key==='Enter'){searchByBarcode(this.value);this.value='';}">

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search name, SKU...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="categoryFilter"><option value="">All Categories</option></select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="brandFilter"><option value="">All Brands</option></select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary" onclick="loadProducts()">Filter</button>
                    <button class="btn btn-outline-success" onclick="exportProducts('csv')"><i class="fas fa-download"></i> CSV</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Image</th><th>Name</th><th>SKU</th><th>Category</th><th>Brand</th><th>Sales</th>
                            <th>Cost</th><th>QTY</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="d-flex justify-content-end mt-3"></div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prodModalTitle">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="prodId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="prodName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU (auto-generated if empty)</label>
                            <input type="text" class="form-control" id="prodSku">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="prodCategory"></select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Brand</label>
                            <select class="form-select" id="prodBrand"></select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Unit</label>
                            <select class="form-select" id="prodUnit"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Selling Price *</label>
                            <input type="number" step="0.01" class="form-control" id="prodPrice" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cost Price</label>
                            <input type="number" step="0.01" class="form-control" id="prodCost">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="prodBarcode">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Min Stock</label>
                            <input type="number" class="form-control" id="prodMinStock" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" class="form-control" id="prodReorderLevel" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="prodStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="prodDescription" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="prodImage" accept="image/*" onchange="previewProductImage(this)">
                        <div id="prodImagePreview" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

function missingImage(el) {
    el.outerHTML = '<div class="bg-light rounded border" style="width:40px;height:40px"></div>';
}

function previewProductImage(input) {
    const box = document.getElementById('prodImagePreview');
    box.innerHTML = input.files && input.files[0]
        ? `<img src="${URL.createObjectURL(input.files[0])}" width="80" height="80" class="rounded border">`
        : '';
}

function setProdImagePreview(src) {
    document.getElementById('prodImagePreview').innerHTML = src
        ? `<img src="../../${src}" width="80" height="80" class="rounded border" onerror="this.remove()">`
        : '';
}

async function loadDropdowns() {
    const [cats, brands, units] = await Promise.all([
        apiRequest('../../api/categories/index.php'),
        apiRequest('../../api/brands/index.php'),
        apiRequest('../../api/units/index.php'),
    ]);

    const opts = (arr, idField = 'id', nameField = 'name', item = null) =>
        item
            ? '<option value="">Select...</option>' + arr.filter(x => x.status === 'active').map(x => `<option value="${x[idField]}" ${x[idField] == item ? 'selected' : ''}>${x[nameField]}</option>`).join('')
            : '<option value="">Select...</option>' + arr.filter(x => x.status === 'active').map(x => `<option value="${x[idField]}">${x[nameField]}</option>`).join('');

    if (cats.success) {
        document.getElementById('categoryFilter').innerHTML = '<option value="">All Categories</option>' + cats.data.filter(x => x.status === 'active').map(x => `<option value="${x.id}">${x.name}</option>`).join('');
        document.getElementById('prodCategory').innerHTML = opts(cats.data);
    }
    if (brands.success) {
        document.getElementById('brandFilter').innerHTML = '<option value="">All Brands</option>' + brands.data.filter(x => x.status === 'active').map(x => `<option value="${x.id}">${x.name}</option>`).join('');
        document.getElementById('prodBrand').innerHTML = opts(brands.data);
    }
    if (units.success) document.getElementById('prodUnit').innerHTML = '<option value="">Select...</option>' + units.data.map(x => `<option value="${x.id}">${x.name} (${x.symbol})</option>`).join('');
}

async function loadProducts(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page, search: document.getElementById('searchInput').value,
        category_id: document.getElementById('categoryFilter').value,
        brand_id: document.getElementById('brandFilter').value,
        status: document.getElementById('statusFilter').value,
    });
    const res = await apiRequest(`../../api/products/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('productsTable').innerHTML = res.data.products.map((p, i) => `
        <tr>
            <td>${(page-1)*20 + i + 1}</td>
            <td>${p.image ? `<img src="../../${p.image}" width="40" height="40" class="rounded" onerror="missingImage(this)">` : '<div class="bg-light rounded border" style="width:40px;height:40px"></div>'}</td>
            <td><strong>${p.name}</strong><br><small class="text-muted">${p.category_name || ''}</small></td>
            <td><code>${p.sku}</code></td>
            <td>${p.category_name || '-'}</td>
            <td>${p.brand_name || '-'}</td>
            <td>$${parseFloat(p.price).toFixed(2)}</td>
            <td>$${parseFloat(p.cost).toFixed(2)}</td>
            <td><span class="badge bg-${p.total_stock <= 0 ? 'danger' : p.total_stock <= p.min_stock ? 'warning' : 'success'}">${p.total_stock} ${p.unit_symbol || ''}</span></td>
            <td><span class="badge bg-${p.status==='active'?'success':'secondary'}">${p.status}</span></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editProduct(${p.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadProducts);
}

async function searchByBarcode(code) {
    if (!code) return;
    const res = await apiRequest(`../../api/products/barcode.php?code=${encodeURIComponent(code)}`);
    if (res.success) {
        editProduct(res.data.id);
    } else {
        alert('Product not found');
    }
}

function resetProdForm() {
    document.getElementById('prodId').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('prodModalTitle').textContent = 'Add Product';
    setProdImagePreview(null);
}

async function editProduct(id) {
    const res = await apiRequest(`../../api/products/index.php?id=${id}`);
    if (!res.success) return;
    const p = res.data;
    document.getElementById('prodId').value = p.id;
    document.getElementById('prodName').value = p.name;
    document.getElementById('prodSku').value = p.sku;
    document.getElementById('prodPrice').value = p.price;
    document.getElementById('prodCost').value = p.cost;
    document.getElementById('prodBarcode').value = p.barcode;
    document.getElementById('prodMinStock').value = p.min_stock;
    document.getElementById('prodReorderLevel').value = p.reorder_level;
    document.getElementById('prodDescription').value = p.description || '';
    document.getElementById('prodStatus').value = p.status;
    document.getElementById('prodModalTitle').textContent = 'Edit Product';
    setProdImagePreview(p.image);
    await loadDropdowns();
    document.getElementById('prodCategory').value = p.category_id || '';
    document.getElementById('prodBrand').value = p.brand_id || '';
    document.getElementById('prodUnit').value = p.unit_id || '';
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

async function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    const res = await apiRequest(`../../api/products/index.php?id=${id}`, 'DELETE');
    if (res.success) loadProducts(currentPage);
}

document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('prodId').value;
    const formData = new FormData();
    formData.append('name', document.getElementById('prodName').value);
    formData.append('sku', document.getElementById('prodSku').value);
    formData.append('category_id', document.getElementById('prodCategory').value);
    formData.append('brand_id', document.getElementById('prodBrand').value);
    formData.append('unit_id', document.getElementById('prodUnit').value);
    formData.append('price', document.getElementById('prodPrice').value);
    formData.append('cost', document.getElementById('prodCost').value);
    formData.append('barcode', document.getElementById('prodBarcode').value);
    formData.append('min_stock', document.getElementById('prodMinStock').value);
    formData.append('reorder_level', document.getElementById('prodReorderLevel').value);
    formData.append('status', document.getElementById('prodStatus').value);
    formData.append('description', document.getElementById('prodDescription').value);
    const fileInput = document.getElementById('prodImage');
    if (fileInput.files.length) formData.append('image', fileInput.files[0]);

    const url = id ? `../../api/products/index.php?id=${id}` : '../../api/products/index.php';
    const method = id ? 'PUT' : 'POST';

    const res = await fetch(url, { method, body: formData });
    const result = await res.json();

    if (result.success) {
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        loadProducts(currentPage);
    } else {
        alert(result.message);
    }
});

function exportProducts(format) {
    const params = new URLSearchParams({
        export: format,
        search: document.getElementById('searchInput').value,
        category_id: document.getElementById('categoryFilter').value,
        brand_id: document.getElementById('brandFilter').value,
    });
    window.open(`../../api/products/index.php?${params}`);
}

document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadProducts();
});

loadDropdowns();
loadProducts();
</script>

<?php require_once '../../includes/footer.php'; ?>
