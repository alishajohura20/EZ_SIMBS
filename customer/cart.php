<?php
$pageTitle = 'My Cart';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/storefront-header.php';
?>

<div class="sf-container">
    <div class="sf-page-head">
        <h1 class="sf-page-title"><i class="fas fa-shopping-cart"></i>My Cart <span class="sf-muted" id="sfCartPageCount"></span></h1>
        <a class="sf-btn sf-btn-outline" href="<?= APP_URL ?>/pages/customer/dashboard.php"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </div>

    <div class="row g-4" style="align-items:flex-start">
        <div class="col-lg-8">
            <div class="sf-panel" id="sfCartList"></div>
        </div>
        <div class="col-lg-4">
            <div class="sf-summary" id="sfCartSummary"></div>
        </div>
    </div>
</div>

<!-- Checkout modal -->
<div class="modal fade sf-modal" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-lock me-2" style="color:var(--sf-brand)"></i>Checkout</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sfCheckoutForm">
                    <div class="mb-3">
                        <label class="sf-label">Delivery Address <span class="text-danger">*</span></label>
                        <textarea class="sf-form" name="shipping_address" rows="2" placeholder="Full delivery address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="sf-label">Contact Phone <span class="text-danger">*</span></label>
                        <input type="tel" class="sf-form" name="contact_phone" placeholder="+1 (555) 000-0000" required>
                    </div>
                    <div class="mb-3">
                        <label class="sf-label">Coupon Code</label>
                        <div class="d-flex gap-2">
                            <input type="text" class="sf-form" id="sfCouponInput" placeholder="e.g. SAVE10">
                            <button type="button" class="sf-btn sf-btn-outline" onclick="SF.applyCoupon()">Apply</button>
                        </div>
                        <small id="sfCouponMsg" class="d-none" style="font-size:.8rem;font-weight:600"></small>
                    </div>
                    <div class="mb-3">
                        <label class="sf-label">Payment Method</label>
                        <select class="sf-form" name="payment_method">
                            <option value="cod">Cash on Delivery</option>
                            <option value="card">Credit / Debit Card</option>
                            <option value="mobile">Mobile Payment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="sf-label">Notes (optional)</label>
                        <input type="text" class="sf-form" name="notes" placeholder="Any special instructions?">
                    </div>
                    <div class="p-3 rounded mb-3" style="background:var(--sf-body);border:1px dashed var(--sf-border)" id="sfChkSummary"></div>
                    <button type="submit" class="sf-btn sf-btn-primary w-100" id="sfPlaceBtn" style="padding:13px"><i class="fas fa-bag-shopping"></i> Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = { items: [], count: 0, subtotal: 0 }, appliedCoupon = null, summaryDiscount = 0;

async function loadCart() {
    document.getElementById('sfCartList').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="opacity:.35"></i></div>';
    const res = await fetch(SF_API + '/cart.php');
    const json = await res.json();
    if (!json.success) return;
    cart = json.data || { items: [], count: 0, subtotal: 0 };
    renderCart();
}
function renderCart() {
    document.getElementById('sfCartPageCount').textContent = cart.items.length ? '(' + cart.count + ' items)' : '';
    const list = document.getElementById('sfCartList');
    if (!cart.items.length) {
        list.innerHTML = '<div class="sf-empty"><i class="fas fa-shopping-cart"></i><p class="mb-1" style="font-weight:700">Your cart is empty</p><p class="sf-muted small mb-3">Fill it up with tasty finds from the shop.</p><a class="sf-btn sf-btn-primary" href="dashboard.php"><i class="fas fa-store"></i> Start Shopping</a></div>';
        document.getElementById('sfCartSummary').innerHTML = '<div class="text-center sf-muted py-3">Nothing to check out yet</div>';
        return;
    }
    list.innerHTML = cart.items.map(i => `
        <div class="sf-cart-item" data-id="${i.cart_item_id || i.id}">
            <div class="sf-cart-thumb" onclick="SF.viewProduct(${i.product_id})">${SF.img(i)}</div>
            <div class="sf-cart-info" onclick="SF.viewProduct(${i.product_id})">
                <div class="nm">${i.name}</div>
                <div class="muted">${i.sku ? i.sku : ''}</div>
                <span class="sf-cart-unit">${SF.money(i.price)} each</span>
            </div>
            <div class="sf-qty">
                <button onclick="updateQty(${i.cart_item_id || i.id}, ${i.qty - 1})"><i class="fas fa-minus"></i></button>
                <span>${i.qty}</span>
                <button onclick="updateQty(${i.cart_item_id || i.id}, ${i.qty + 1})"><i class="fas fa-plus"></i></button>
            </div>
            <div class="sf-cart-total">${SF.money(i.qty * i.price)}</div>
            <button class="sf-btn sf-btn-outline btn-sm" onclick="removeItem(${i.cart_item_id || i.id})" style="padding:7px 11px"><i class="fas fa-trash-alt"></i></button>
        </div>`).join('');
    renderSummary();
}
function renderSummary() {
    const s = document.getElementById('sfCartSummary');
    const total = cart.subtotal - summaryDiscount;
    s.innerHTML = `
        <div class="sf-summary-title"><i class="fas fa-receipt"></i>Order Summary</div>
        <div class="sf-sum-row"><span class="sf-muted">Subtotal (${cart.count} items)</span><span class="val">${SF.money(cart.subtotal)}</span></div>
        <div class="sf-sum-row" id="sfDiscRow" style="${summaryDiscount ? 'display:flex' : 'display:none'}"><span class="sf-muted">Discount</span><span class="val text-success">−${SF.money(summaryDiscount)}</span></div>
        <div class="sf-sum-row total"><span>Grand Total</span><span class="val">${SF.money(Math.max(0, total))}</span></div>
        <div class="sf-muted small" style="margin-top:10px"><i class="fas fa-info-circle"></i> Taxes & shipping are added at order time.</div>
        <button class="sf-btn sf-btn-primary w-100 mt-3" onclick="openCheckout()" style="padding:13px"><i class="fas fa-lock"></i> Proceed to Checkout</button>`;
}
async function updateQty(id, qty) {
    if (qty < 1) return removeItem(id);
    const res = await fetch(SF_API + '/cart.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'update', cart_item_id: id, qty }) });
    const json = await res.json();
    if (json.success) { 
        cart = json.data.cart; 
        if (appliedCoupon) await revalidateCoupon();
        else renderCart();
        SF.refreshCounts(); 
    }
}
async function removeItem(id) {
    const res = await fetch(SF_API + '/cart.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'remove', cart_item_id: id }) });
    const json = await res.json();
    if (json.success) { 
        cart = json.data.cart; 
        if (appliedCoupon) await revalidateCoupon();
        else renderCart(); 
        SF.refreshCounts(); 
    }
}

async function revalidateCoupon() {
    if (!appliedCoupon) return;
    const res = await fetch(SF_API + '/orders.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'validate_coupon', code: appliedCoupon }) });
    const json = await res.json();
    const msg = document.getElementById('sfCouponMsg');
    if (json.success) {
        summaryDiscount = json.data.discount;
        msg.className = 'd-block text-success'; msg.textContent = '✓ Coupon applied −' + SF.money(summaryDiscount);
    } else {
        appliedCoupon = null; summaryDiscount = 0;
        msg.className = 'd-block text-danger';
        msg.textContent = '✗ Coupon no longer valid: ' + (json.message || 'Cart total changed');
    }
    renderSummary();
    renderCart();
}

/* ---------- Checkout ---------- */
function openCheckout() {
    if (!cart.items.length) { SF.toast('Cart is empty', 'error'); return; }
    document.getElementById('sfChkSummary').innerHTML = `
        <div class="sf-sum-row"><span class="sf-muted">Subtotal</span><span>${SF.money(cart.subtotal)}</span></div>
        <div class="sf-sum-row" id="sfChkDisc" style="${summaryDiscount ? 'flex' : 'none'}"><span class="sf-muted">Discount</span><span class="text-success">−${SF.money(summaryDiscount)}</span></div>
        <div class="sf-sum-row total" style="border-top:none;padding-top:0;margin-top:6px"><span>Total due</span><span class="val">${SF.money(Math.max(0, cart.subtotal - summaryDiscount))}</span></div>`;
    SF.modal('checkoutModal').show();
}
window.SF.applyCoupon = async function () {
    const code = document.getElementById('sfCouponInput').value.trim();
    if (!code) return;
    const res = await fetch(SF_API + '/orders.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'validate_coupon', code }) });
    const json = await res.json();
    const msg = document.getElementById('sfCouponMsg');
    msg.classList.remove('d-none');
    if (json.success) {
        appliedCoupon = code; summaryDiscount = json.data.discount;
        msg.className = 'd-block text-success'; msg.textContent = '✓ Coupon applied −' + SF.money(summaryDiscount);
        renderSummary();
        const chk = document.getElementById('sfChkSummary');
        if (chk) { openCheckout(); }
    } else {
        appliedCoupon = null; summaryDiscount = 0; msg.className = 'd-block text-danger';
        msg.textContent = '✗ ' + (json.message || 'Invalid coupon');
        renderSummary();
    }
};

document.getElementById('sfCheckoutForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const data = Object.fromEntries(fd.entries());
    if (appliedCoupon) data.coupon_code = appliedCoupon;
    data.action = 'create';
    const btn = document.getElementById('sfPlaceBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing order...';
    const res = await fetch(SF_API + '/orders.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
    const json = await res.json();
    btn.disabled = false; btn.innerHTML = '<i class="fas fa-bag-shopping"></i> Place Order';
    if (json.success) {
        SF.modal('checkoutModal').hide();
        SF.toast('Order placed! ' + json.data.order_no);
        setTimeout(() => location.href = '<?= APP_URL ?>/pages/customer/orders.php?highlight=' + json.data.order_id, 600);
    } else {
        SF.toast(json.message || 'Order failed', 'error');
    }
});

loadCart();
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>