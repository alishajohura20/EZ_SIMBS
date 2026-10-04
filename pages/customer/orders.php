<?php
$pageTitle = 'My Orders';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/storefront-header.php';
?>

<div class="sf-container">
    <div class="sf-page-head">
        <h1 class="sf-page-title"><i class="fas fa-box"></i>Order History</h1>
        <div class="sf-flex" style="gap:10px">
            <select id="sfStatusFilter" class="sf-sort">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
                <option value="returned">Returned</option>
            </select>
            <a class="sf-btn sf-btn-outline" href="<?= APP_URL ?>/pages/customer/dashboard.php"><i class="fas fa-store"></i> Shop More</a>
        </div>
    </div>

    <div class="sf-panel" id="sfOrdersList"></div>
</div>

<!-- Order detail modal -->
<div class="modal fade sf-modal" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body p-0" id="sfOrderModalBody"></div>
        </div>
    </div>
</div>

<!-- Return request modal -->
<div class="modal fade sf-modal" id="sfReturnModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" style="color:var(--sf-maroon)">Request a Return</h5>
                    <small class="sf-muted" id="sfReturnOrderNo"></small>
                </div>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="sf-muted small mb-3">Select the quantities to return. The store will review your request before issuing a refund to your original payment method.</p>
                <div class="mb-3" id="sfReturnItems"></div>
                <label class="form-label small fw-bold">Reason</label>
                <select class="sf-form" id="sfReturnReason">
                    <option value="">-- Choose a reason --</option>
                    <option value="damaged">Damaged on arrival</option>
                    <option value="wrong_item">Wrong item received</option>
                    <option value="not_as_described">Not as described</option>
                    <option value="defective">Defective / not working</option>
                    <option value="changed_mind">Changed my mind</option>
                    <option value="other">Other</option>
                </select>
                <div class="mt-4 d-flex gap-2">
                    <button class="sf-btn sf-btn-outline flex-fill" data-bs-dismiss="modal">Cancel</button>
                    <button class="sf-btn sf-btn-primary flex-fill" onclick="submitReturnRequest()"><i class="fas fa-paper-plane"></i> Submit Request</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function loadOrders() {
    let u = SF_API + '/orders.php';
    const st = document.getElementById('sfStatusFilter').value;
    if (st) u += '?status=' + encodeURIComponent(st);
    const res = await fetch(u);
    const json = await res.json();
    const orders = json.success ? (json.data.orders || []) : [];
    const el = document.getElementById('sfOrdersList');
    if (!orders.length) {
        el.innerHTML = '<div class="sf-empty"><i class="fas fa-box-open"></i><p class="mb-1" style="font-weight:700">No orders found</p><p class="sf-muted small mb-3">Your placed orders will appear here.</p><a class="sf-btn sf-btn-primary" href="dashboard.php"><i class="fas fa-store"></i> Start Shopping</a></div>';
        return;
    }
    el.innerHTML = orders.map(o => `
        <div class="sf-order" onclick="viewOrder(${o.id})">
            <div style="min-width:170px">
                <div class="sf-order-code">${o.order_no}</div>
                <small class="sf-muted">${new Date(o.created_at).toLocaleString()}</small>
            </div>
            <div class="sf-muted" style="min-width:70px">${o.item_count || 0} item(s)</div>
            <div style="min-width:90px;font-weight:800;color:var(--sf-brand);font-size:1.05rem">${SF.money(o.grand_total)}</div>
            <div class="ms-auto"><span class="sf-status ${o.status}">${o.status}</span><i class="fas fa-chevron-right ms-2 sf-muted" style="font-size:.75rem"></i></div>
        </div>`).join('');

    const hp = new URLSearchParams(location.search).get('highlight');
    if (hp) { viewOrder(parseInt(hp, 10)); history.replaceState({}, '', 'orders.php'); }
}

async function viewOrder(id) {
    const res = await fetch(SF_API + '/orders.php?id=' + id + '&returnable=1');
    const json = await res.json();
    if (!json.success) return;
    const o = json.data.order;
    const ret = json.data.returnable || {};
    const items = (o.items || []).map(i => `
        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--sf-border)">
            <div class="sf-cart-thumb" style="width:52px;height:52px">${SF.img(i)}</div>
            <div style="flex:1"><div style="font-weight:700;font-size:.88rem">${i.name}</div><small class="sf-muted">${i.qty} × ${SF.money(i.unit_price)}</small></div>
            <strong style="font-size:.9rem">${SF.money(i.total)}</strong>
        </div>`).join('');

    document.getElementById('sfOrderModalBody').innerHTML = `
        <div class="modal-header">
            <div>
                <h5 class="modal-title fw-bold" style="color:var(--sf-maroon)">${o.order_no}</h5>
                <small class="sf-muted">Placed ${new Date(o.created_at).toLocaleString()}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="sf-status ${o.status}">${o.status}</span>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
        </div>
        <div class="modal-body p-4">
            <p class="mb-3"><small class="sf-muted"><i class="fas fa-map-marker-alt me-1" style="color:var(--sf-brand)"></i>${o.shipping_address || 'No address provided'}</small></p>
            <div class="mb-3">${items}</div>
            <div class="p-3 rounded" style="background:var(--sf-body);max-width:380px;margin:0 0 0 auto">
                <div class="sf-sum-row"><span class="sf-muted">Subtotal</span><span>${SF.money(o.subtotal)}</span></div>
                ${parseFloat(o.discount) > 0 ? `<div class="sf-sum-row"><span class="sf-muted">Discount</span><span class="text-success">−${SF.money(o.discount)}</span></div>` : ''}
                ${parseFloat(o.tax) > 0 ? `<div class="sf-sum-row"><span class="sf-muted">Tax</span><span>${SF.money(o.tax)}</span></div>` : ''}
                ${parseFloat(o.shipping) > 0 ? `<div class="sf-sum-row"><span class="sf-muted">Shipping</span><span>${SF.money(o.shipping)}</span></div>` : ''}
                <div class="sf-sum-row total"><span>Total</span><span class="val">${SF.money(o.grand_total)}</span></div>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <span class="sf-badge-chip"><i class="fas fa-money-bill-wave"></i>${(o.payment_method || 'cod').toUpperCase()}</span>
                ${o.coupon_code ? `<span class="sf-badge-chip"><i class="fas fa-ticket-alt"></i>Coupon: ${o.coupon_code}</span>` : ''}
                <span class="sf-badge-chip"><i class="fas fa-user"></i>${o.contact_phone || '—'}</span>
            </div>
            ${ret.returnable ? `<div class="mt-3"><button class="sf-btn sf-btn-primary" onclick="openReturnForm(${o.id})"><i class="fas fa-undo-alt me-1"></i>Request a Return</button>
                <small class="sf-muted ms-2">Return requests must be raised within 14 days of delivery.</small></div>` : ''}
        </div>`;
    window.__lastOrderRet = ret;
    SF.modal('orderModal').show();
}

async function openReturnForm(orderId) {
    const r = window.__lastOrderRet;
    if (!r || !r.returnable) {
        const res = await fetch(SF_API + '/orders.php?id=' + orderId + '&returnable=1');
        const json = await res.json();
        if (!json.success || !json.data.returnable) return;
    }
    window.__returnOrderId = orderId; // Store orderId for submit
    document.getElementById('sfReturnOrderNo').textContent = 'Order #' + orderId;
    document.getElementById('sfReturnReason').selectedIndex = 0;
    document.getElementById('sfReturnItems').innerHTML = (r.items || [])
        .filter(i => (i.returnable_qty || 0) > 0)
        .map(i => `
            <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--sf-border)">
                <div class="sf-cart-thumb" style="width:46px;height:46px">${SF.img(i)}</div>
                <div style="flex:1">
                    <div style="font-weight:700;font-size:.88rem">${i.name}</div>
                    <small class="sf-muted">Max ${i.returnable_qty} of ${i.qty} returnable · ${SF.money(i.unit_price)} each</small>
                </div>
                <div style="width:88px">
                    <div class="sf-qty" style="justify-content:space-between">
                        <button type="button" onclick="bumpReturnQty(this,-1)">−</button>
                        <input type="number" class="sf-return-qty" data-oi="${i.id}" value="0" min="0" max="${i.returnable_qty}" onchange="clampReturnQty(this)">
                        <button type="button" onclick="bumpReturnQty(this,1)">+</button>
                    </div>
                </div>
            </div>`).join('');
    SF.modal('sfReturnModal').show();
}

function clampReturnQty(inp) {
    const max = parseInt(inp.max, 10) || 1;
    if (parseInt(inp.value, 10) > max) inp.value = max;
    if (parseInt(inp.value, 10) < 0) inp.value = 0;
}

function bumpReturnQty(btn, d) {
    const inp = btn.parentElement.querySelector('.sf-return-qty');
    inp.value = Math.max(0, Math.min(parseInt(inp.max, 10) || 1, (parseInt(inp.value, 10) || 0) + d));
}

async function submitReturnRequest() {
    const items = Array.from(document.querySelectorAll('.sf-return-qty'))
        .map(el => ({ order_item_id: parseInt(el.dataset.oi, 10), qty: parseInt(el.value, 10) }))
        .filter(i => i.qty > 0);
    const reason = document.getElementById('sfReturnReason').value;
    if (!items.length) { SF.toast('Select at least one item to return', 'error'); return; }
    if (!reason) { SF.toast('Choose a return reason', 'error'); return; }
    const orderId = window.__returnOrderId;
    if (!orderId) { SF.toast('Order ID not found', 'error'); return; }
    const res = await SF.fetchJSON(SF_API + '/returns.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'create', order_id: orderId, items, reason_code: reason }),
    });
    if (!res.success) { SF.toast(res.message || 'Request failed', 'error'); return; }
    SF.modal('sfReturnModal').hide();
    SF.toast('Return request submitted', 'success');
    setTimeout(() => location.href = 'returns.php?highlight=' + (res.data.id || ''), 700);
}

document.getElementById('sfStatusFilter').addEventListener('change', loadOrders);
loadOrders();
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>