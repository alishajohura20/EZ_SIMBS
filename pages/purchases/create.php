<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Create Purchase Order';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <h4 class="mb-4">New Purchase Order</h4>

    <div class="card">
        <div class="card-body">
            <form id="poForm">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Supplier *</label>
                        <select class="form-select" id="poSupplier" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="poNotes" rows="1"></textarea>
                    </div>
                </div>

                <h5>Items</h5>
                <div class="table-responsive mb-3">
                    <table class="table" id="itemsTable">
                        <thead>
                            <tr><th>Product</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th>Action</th></tr>
                        </thead>
                        <tbody id="poItems"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td id="poSubtotal">$0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tax (10%):</strong></td>
                                <td id="poTax">$0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td id="poTotal" class="fw-bold">$0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary mb-3" onclick="addItemRow()">
                    <i class="fas fa-plus"></i> Add Item
                </button>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create PO</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let productOptions = '';

async function init() {
    const [supRes, prodRes] = await Promise.all([
        apiRequest('../../api/suppliers/index.php?status=active'),
        apiRequest('../../api/products/index.php?per_page=500'),
    ]);

    if (supRes.success) {
        document.getElementById('poSupplier').innerHTML = '<option value="">Select Supplier</option>' +
            supRes.data.suppliers.map(s => `<option value="${s.id}">${s.company_name}</option>`).join('');
    }

    if (prodRes.success) {
        productOptions = '<option value="">Select Product</option>' +
            prodRes.data.products.map(p => `<option value="${p.id}" data-cost="${p.cost}">${p.name} (${p.sku})</option>`).join('');
    }

    addItemRow();
}

let itemIndex = 0;

function addItemRow() {
    const tbody = document.getElementById('poItems');
    const row = document.createElement('tr');
    row.id = `item-${itemIndex}`;
    row.innerHTML = `
        <td><select class="form-select item-product" onchange="setCost(${itemIndex})">${productOptions}</select></td>
        <td><input type="number" class="form-control item-qty" min="1" value="1" oninput="calcTotal(${itemIndex})"></td>
        <td><input type="number" step="0.01" class="form-control item-cost" value="0" oninput="calcTotal(${itemIndex})"></td>
        <td class="item-total">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemIndex})"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    itemIndex++;
}

function setCost(idx) {
    const sel = document.querySelector(`#item-${idx} .item-product`);
    const cost = sel.options[sel.selectedIndex]?.dataset.cost || 0;
    document.querySelector(`#item-${idx} .item-cost`).value = cost;
    calcTotal(idx);
}

function calcTotal(idx) {
    const qty = parseInt(document.querySelector(`#item-${idx} .item-qty`).value) || 0;
    const cost = parseFloat(document.querySelector(`#item-${idx} .item-cost`).value) || 0;
    document.querySelector(`#item-${idx} .item-total`).textContent = '$' + (qty * cost).toFixed(2);
    calcGrandTotal();
}

function calcGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(td => {
        subtotal += parseFloat(td.textContent.replace('$', '')) || 0;
    });
    const tax = subtotal * 0.10;
    document.getElementById('poSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('poTax').textContent = '$' + tax.toFixed(2);
    document.getElementById('poTotal').textContent = '$' + (subtotal + tax).toFixed(2);
}

function removeItem(idx) {
    document.getElementById(`item-${idx}`)?.remove();
    calcGrandTotal();
}

document.getElementById('poForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const items = [];
    document.querySelectorAll('#poItems tr').forEach(row => {
        const productId = row.querySelector('.item-product')?.value;
        const qty = parseInt(row.querySelector('.item-qty')?.value) || 0;
        const cost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
        if (productId && qty > 0) items.push({ product_id: productId, qty, unit_cost: cost });
    });

    if (!items.length) { alert('Add at least one item'); return; }

    const data = {
        supplier_id: document.getElementById('poSupplier').value,
        notes: document.getElementById('poNotes').value,
        items,
    };

    const res = await apiRequest('../../api/purchases/index.php', 'POST', data);
    if (res.success) {
        window.location.href = `view.php?id=${res.data.id}`;
    } else {
        alert(res.message);
    }
});

init();
</script>

<?php require_once '../../includes/footer.php'; ?>
