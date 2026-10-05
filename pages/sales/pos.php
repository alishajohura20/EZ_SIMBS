<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Point of Sale';
requireLogin();
requireRole(['admin', 'manager', 'cashier', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="row">
        <!-- Product Selection -->
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <input type="text" class="form-control form-control-lg" id="barcodeSearch"
                           placeholder="Scan barcode or search product..." autofocus
                           onkeypress="if(event.key==='Enter'){lookupProduct(this.value);this.value='';}">
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="productSearch" placeholder="Search products..."
                                   oninput="searchProducts(this.value)">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="categoryFilter" onchange="filterByCategory(this.value)">
                                <option value="">All Categories</option>
                            </select>
                        </div>
                    </div>
                    <div id="productGrid" class="row g-2" style="max-height:400px;overflow-y:auto;"></div>
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="col-md-5">
            <div class="card" style="position:sticky;top:20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Current Sale</h5>
                </div>
                <div class="card-body">
                    <!-- Customer -->
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customerPhone" placeholder="Phone lookup...">
                            <button class="btn btn-outline-primary" onclick="lookupCustomer()"><i class="fas fa-search"></i></button>
                            <button class="btn btn-outline-secondary" onclick="clearCustomer()">Clear</button>
                        </div>
                        <small id="customerName" class="text-success"></small>
                    </div>

                    <!-- Cart Items -->
                    <div class="table-responsive" style="max-height:250px;overflow-y:auto;">
                        <table class="table table-sm">
                            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead>
                            <tbody id="cartItems"></tbody>
                        </table>
                    </div>

                    <div id="emptyCart" class="text-center text-muted py-4">
                        <i class="fas fa-cart-plus fa-3x mb-2"></i>
                        <p>Scan or select products</p>
                    </div>

                    <hr>
                    <!-- Totals -->
                    <div class="d-flex justify-content-between"><span>Subtotal:</span><span id="subtotal">$0.00</span></div>
                    <div class="d-flex justify-content-between">
                        <span>Discount:</span>
                        <input type="number" step="0.01" class="form-control form-control-sm w-25 text-end" id="discount" value="0" oninput="recalcTotal()">
                    </div>
                    <div class="d-flex justify-content-between"><span>Tax (10%):</span><span id="tax">$0.00</span></div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping:</span>
                        <input type="number" step="0.01" class="form-control form-control-sm w-25 text-end" id="shipping" value="0" oninput="recalcTotal()">
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Grand Total:</span><span id="grandTotal">$0.00</span></div>

                    <!-- Payment -->
                    <div class="mt-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Banking</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success btn-lg" onclick="completeSale()">
                            <i class="fas fa-check"></i> Complete Sale
                        </button>
                        <div class="row g-2">
                            <div class="col"><button class="btn btn-outline-warning w-100" onclick="holdSale()"><i class="fas fa-pause"></i> Hold</button></div>
                            <div class="col"><button class="btn btn-outline-danger w-100" onclick="clearCart()"><i class="fas fa-trash"></i> Clear</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let customerId = null;
let allProducts = [];
let categories = [];

async function initPOS() {
    const [prodRes, catRes] = await Promise.all([
        apiRequest('../../api/products/index.php?per_page=500&status=active'),
        apiRequest('../../api/categories/index.php'),
    ]);
    if (prodRes.success) { allProducts = prodRes.data.products; renderProducts(allProducts); }
    if (catRes.success) {
        categories = catRes.data;
        document.getElementById('categoryFilter').innerHTML = '<option value="">All Categories</option>' +
            categories.filter(c => c.status === 'active').map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }
}

function renderProducts(products) {
    document.getElementById('productGrid').innerHTML = products.map(p => `
        <div class="col-md-3 col-6">
            <div class="card h-100" style="cursor:pointer" onclick="addToCart(${p.id})">
                ${p.image ? `<img src="../../${p.image}" class="card-img-top" style="height:80px;object-fit:cover">` :
                    `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:80px"><i class="fas fa-box fa-2x text-muted"></i></div>`}
                <div class="card-body p-2 text-center">
                    <small class="card-title fw-bold d-block text-truncate">${p.name}</small>
                    <small class="text-muted">${p.sku}</small><br>
                    <strong class="text-primary">$${parseFloat(p.price).toFixed(2)}</strong><br>
                    <small class="text-${p.total_stock > 0 ? 'success' : 'danger'}">${p.total_stock} in stock</small>
                </div>
            </div>
        </div>
    `).join('');
}

function searchProducts(query) {
    const q = query.toLowerCase();
    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
    renderProducts(filtered);
}

function filterByCategory(catId) {
    if (!catId) { renderProducts(allProducts); return; }
    renderProducts(allProducts.filter(p => p.category_id == catId));
}

async function lookupProduct(code) {
    if (!code) return;
    const res = await apiRequest(`../../api/products/barcode.php?code=${code}`);
    if (res.success) addToCart(res.data.id);
    else alert('Product not found');
}

function addToCart(productId) {
    const product = allProducts.find(p => p.id == productId);
    if (!product) return;
    if (product.total_stock <= 0) { alert('Out of stock!'); return; }

    const existing = cart.find(i => i.product_id == productId);
    if (existing) {
        if (existing.qty >= product.total_stock) { alert('Insufficient stock'); return; }
        existing.qty++;
    } else {
        cart.push({ product_id: productId, name: product.name, sku: product.sku, unit_price: parseFloat(product.price), qty: 1, max_stock: product.total_stock });
    }
    renderCart();
}

function updateQty(productId, qty) {
    const item = cart.find(i => i.product_id == productId);
    if (item) {
        item.qty = Math.max(1, Math.min(qty, item.max_stock));
        renderCart();
    }
}

function removeFromCart(productId) {
    cart = cart.filter(i => i.product_id != productId);
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('cartItems');
    const empty = document.getElementById('emptyCart');

    if (cart.length === 0) {
        tbody.innerHTML = '';
        empty.style.display = 'block';
        recalcTotal();
        return;
    }

    empty.style.display = 'none';
    tbody.innerHTML = cart.map(i => `
        <tr>
            <td><small>${i.name}</small></td>
            <td><input type="number" min="1" value="${i.qty}" class="form-control form-control-sm" style="width:60px" onchange="updateQty(${i.product_id}, +this.value)"></td>
            <td><small>$${i.unit_price.toFixed(2)}</small></td>
            <td><small>$${(i.qty * i.unit_price).toFixed(2)}</small></td>
            <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${i.product_id})"><i class="fas fa-times"></i></button></td>
        </tr>
    `).join('');
    recalcTotal();
}

function recalcTotal() {
    let subtotal = cart.reduce((sum, i) => sum + (i.qty * i.unit_price), 0);
    let discount = parseFloat(document.getElementById('discount').value) || 0;
    let shipping = parseFloat(document.getElementById('shipping').value) || 0;
    let tax = (subtotal - discount) * 0.10;
    let grandTotal = subtotal - discount + tax + shipping;

    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('grandTotal').textContent = '$' + grandTotal.toFixed(2);
}

async function lookupCustomer() {
    const phone = document.getElementById('customerPhone').value;
    if (!phone) return;
    const res = await apiRequest(`../../api/customers/lookup.php?phone=${phone}`);
    if (res.success && res.data) {
        customerId = res.data.id;
        document.getElementById('customerName').textContent = `${res.data.name} (${res.data.membership_id})`;
    } else {
        if (confirm('Customer not found. Add new?')) {
            window.open('../../pages/customers/', '_blank');
        }
    }
}

function clearCustomer() {
    customerId = null;
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerName').textContent = '';
}

function clearCart() {
    cart = [];
    customerId = null;
    document.getElementById('discount').value = 0;
    document.getElementById('shipping').value = 0;
    clearCustomer();
    renderCart();
}

async function completeSale() {
    if (cart.length === 0) { alert('Cart is empty'); return; }

    const paymentMethod = document.getElementById('paymentMethod').value;
    const data = {
        customer_id: customerId,
        items: cart.map(i => ({ product_id: i.product_id, qty: i.qty, unit_price: i.unit_price })),
        discount: document.getElementById('discount').value || 0,
        shipping: document.getElementById('shipping').value || 0,
        payment_method: paymentMethod,
    };

    const res = await apiRequest('../../api/sales/index.php', 'POST', data);
    if (res.success) {
        const print = confirm(`Sale completed!\nInvoice: ${res.data.invoice_no}\nTotal: $${parseFloat(res.data.grand_total).toFixed(2)}\n\nPrint receipt?`);
        if (print) window.open(`receipt.php?id=${res.data.id}`, '_blank');
        clearCart();
    } else {
        alert(res.message);
    }
}

async function holdSale() {
    if (cart.length === 0) return;
    localStorage.setItem('held_cart', JSON.stringify({ cart, customerId, discount: document.getElementById('discount').value, shipping: document.getElementById('shipping').value }));
    clearCart();
    alert('Sale held');
}

initPOS();
</script>

<?php require_once '../../includes/footer.php'; ?>
