<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Purchase Details';
requireLogin();
$id = (int)$_GET['id'];
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <div id="purchaseContent">Loading...</div>
</div>

<?php require_once '../../includes/purchase-return-modal.php'; ?>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5>Add Payment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="paymentForm">
            <div class="modal-body">
                <input type="hidden" id="payPurchaseId">
                <div class="mb-3"><label>Amount *</label><input type="number" step="0.01" class="form-control" id="payAmount" required></div>
                <div class="mb-3"><label>Method</label>
                    <select class="form-select" id="payMethod">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="mb-3"><label>Reference</label><input type="text" class="form-control" id="payRef"></div>
                <div class="mb-3"><label>Notes</label><textarea class="form-control" id="payNotes" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add Payment</button>
            </div>
        </form>
    </div></div>
</div>

<script>
const poId = <?= $id ?>;
const USER_CAN_RETURN = <?= in_array($_SESSION['user_role'] ?? '', ['admin','manager','branch_manager']) ? 'true' : 'false' ?>;

async function loadPurchase() {
    const res = await apiRequest(`../../api/purchases/index.php?id=${poId}`);
    if (!res.success) { document.getElementById('purchaseContent').innerHTML = '<p>Not found</p>'; return; }

    const p = res.data.purchase;
    const items = res.data.items;
    const payments = res.data.payments;

    const statusColors = { draft: 'secondary', pending: 'warning', approved: 'info', received: 'success', cancelled: 'danger' };

    document.getElementById('purchaseContent').innerHTML = `
        <div class="d-flex justify-content-between mb-4">
            <h4>PO: ${p.po_number}</h4>
            <div>
                ${['draft','pending'].includes(p.status) ? `<button class="btn btn-success" onclick="updateStatus('approved')">Approve</button>` : ''}
                ${p.status === 'approved' ? `<button class="btn btn-primary" onclick="updateStatus('received')">Receive Stock</button>` : ''}
                ${['draft','pending','approved'].includes(p.status) ? `<button class="btn btn-warning" onclick="updateStatus('cancelled')">Cancel</button>` : ''}
                ${p.status === 'received' && USER_CAN_RETURN ? `<button class="btn btn-warning" onclick="openPurchaseReturnModal(${p.id})">Return Goods</button>` : ''}
                ${p.status !== 'cancelled' ? `<button class="btn btn-outline-success" onclick="showPaymentModal()">Add Payment</button>` : ''}
                <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card"><div class="card-body">
                    <h6>Supplier</h6>
                    <p>${p.supplier_name}<br>${p.contact_person || ''}<br>${p.supplier_phone || ''}</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-body">
                    <h6>Status</h6>
                    <p><span class="badge bg-${statusColors[p.status]} fs-6">${p.status.toUpperCase()}</span></p>
                    <p>Created: ${new Date(p.created_at).toLocaleString()}</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card"><div class="card-body">
                    <h6>Financial Summary</h6>
                    <p>Total: <strong>$${parseFloat(p.total).toFixed(2)}</strong></p>
                    <p>Paid: <strong class="text-success">$${parseFloat(p.paid).toFixed(2)}</strong></p>
                    <p>Due: <strong class="text-danger">$${parseFloat(p.due).toFixed(2)}</strong></p>
                </div></div>
            </div>
        </div>
        <h5>Items</h5>
        <table class="table mb-4">
            <thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Unit Cost</th><th>Total</th><th>Received</th></tr></thead>
            <tbody>${items.map(i => `<tr><td>${i.product_name}</td><td><code>${i.sku}</code></td><td>${i.qty} ${i.unit_symbol||''}</td><td>$${parseFloat(i.unit_cost).toFixed(2)}</td><td>$${parseFloat(i.total).toFixed(2)}</td><td>${i.received_qty || '-'}</td></tr>`).join('')}</tbody>
        </table>
        ${payments.length ? `<h5>Payments</h5><table class="table"><thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>By</th></tr></thead><tbody>${payments.map(py => `<tr><td>${new Date(py.created_at).toLocaleString()}</td><td>$${parseFloat(py.amount).toFixed(2)}</td><td>${py.payment_method}</td><td>${py.reference||'-'}</td><td>${py.paid_by_name||'-'}</td></tr>`).join('')}</tbody></table>` : ''}
    `;
}

async function updateStatus(status) {
    const msgs = { approved: 'Approve', received: 'Receive stock', cancelled: 'Cancel' };
    if (!confirm(`${msgs[status]} this PO?`)) return;
    const res = await apiRequest('../../api/purchases/status.php', 'POST', { purchase_id: poId, status });
    if (res.success) loadPurchase();
    else alert(res.message);
}

function showPaymentModal() {
    document.getElementById('payPurchaseId').value = poId;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

document.getElementById('paymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await apiRequest('../../api/purchases/payment.php', 'POST', {
        purchase_id: poId, amount: document.getElementById('payAmount').value,
        payment_method: document.getElementById('payMethod').value,
        reference: document.getElementById('payRef').value,
        notes: document.getElementById('payNotes').value,
    });
    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
    if (res.success) loadPurchase();
    else alert(res.message);
});

loadPurchase();
</script>

<?php require_once '../../includes/footer.php'; ?>
