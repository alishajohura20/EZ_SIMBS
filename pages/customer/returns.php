<?php
$pageTitle = 'My Returns';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/storefront-header.php';
?>

<div class="sf-container">
    <div class="sf-page-head">
        <h1 class="sf-page-title"><i class="fas fa-undo-alt"></i>My Returns</h1>
        <div class="sf-flex" style="gap:10px">
            <select id="sfReturnStatusFilter" class="sf-sort">
                <option value="">All Statuses</option>
                <option value="requested">Requested</option>
                <option value="approved">Approved</option>
                <option value="received">Received</option>
                <option value="refunded">Refunded</option>
                <option value="rejected">Rejected</option>
            </select>
            <a class="sf-btn sf-btn-outline" href="orders.php"><i class="fas fa-box"></i> My Orders</a>
        </div>
    </div>

    <div class="sf-panel" id="sfReturnsList"></div>
</div>

<!-- Return detail modal -->
<div class="modal fade sf-modal" id="sfReturnDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body p-0" id="sfReturnDetailBody"></div>
        </div>
    </div>
</div>

<script>
async function loadReturns() {
    let u = SF_API + '/returns.php';
    const st = document.getElementById('sfReturnStatusFilter').value;
    if (st) u += '?status=' + encodeURIComponent(st);
    const res = await fetch(u);
    const json = await res.json();
    const rets = json.success ? (json.data.returns.returns || []) : [];
    const el = document.getElementById('sfReturnsList');
    if (!rets.length) {
        el.innerHTML = '<div class="sf-empty"><i class="fas fa-undo-alt"></i><p class="mb-1" style="font-weight:700">No return requests</p><p class="sf-muted small mb-3">Returns you raise against delivered orders will appear here.</p><a class="sf-btn sf-btn-primary" href="orders.php"><i class="fas fa-box"></i> Go to Orders</a></div>';
        return;
    }
    el.innerHTML = rets.map(r => `
        <div class="sf-order" onclick="viewReturn(${r.id})">
            <div style="min-width:170px">
                <div class="sf-order-code">${r.return_no}</div>
                <small class="sf-muted">${r.order_no || ''} · ${new Date(r.created_at).toLocaleString()}</small>
            </div>
            <div class="sf-muted" style="min-width:70px">${r.total_qty || 0} item(s)</div>
            <div style="min-width:90px;font-weight:800;color:var(--sf-brand);font-size:1.05rem">${SF.money(r.refund_total)}</div>
            <div class="ms-auto"><span class="sf-status ${r.status}">${r.status}</span><i class="fas fa-chevron-right ms-2 sf-muted" style="font-size:.75rem"></i></div>
        </div>`).join('');

    const hp = new URLSearchParams(location.search).get('highlight');
    if (hp) { viewReturn(parseInt(hp, 10)); history.replaceState({}, '', 'returns.php'); }
}

async function viewReturn(id) {
    const res = await fetch(SF_API + '/returns.php?id=' + id);
    const json = await res.json();
    if (!json.success) return;
    const r = json.data.return.return;
    const items = json.data.return.items || [];
    const payments = json.data.return.payments || [];
    const photos = json.data.return.photos || [];

    const itemsHtml = items.map(i => `
        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--sf-border)">
            ${i.image ? `<div class="sf-cart-thumb" style="width:46px;height:46px"><img src="${SF_STORE}/${i.image}" style="width:100%;height:100%;object-fit:cover;border-radius:10px" onerror="this.remove()"></div>` : ''}
            <div style="flex:1"><div style="font-weight:700;font-size:.88rem">${i.product_name}</div><small class="sf-muted">${i.qty} × ${SF.money(i.unit_price)}</small></div>
            <strong style="font-size:.9rem">${SF.money(i.total)}</strong>
        </div>`).join('');

    document.getElementById('sfReturnDetailBody').innerHTML = `
        <div class="modal-header">
            <div>
                <h5 class="modal-title fw-bold" style="color:var(--sf-maroon)">${r.return_no}</h5>
                <small class="sf-muted">Requested ${new Date(r.created_at).toLocaleString()}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="sf-status ${r.status}">${r.status}</span>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
        </div>
        <div class="modal-body p-4">
            <div class="mb-3">${itemsHtml}</div>
            <div class="p-3 rounded" style="background:var(--sf-body);max-width:380px;margin:0 0 0 auto">
                <div class="sf-sum-row"><span class="sf-muted">Refund subtotal</span><span>${SF.money(r.subtotal)}</span></div>
                <div class="sf-sum-row"><span class="sf-muted">Tax</span><span>${SF.money(r.tax)}</span></div>
                ${parseFloat(r.shipping) > 0 ? `<div class="sf-sum-row"><span class="sf-muted">Shipping</span><span>${SF.money(r.shipping)}</span></div>` : ''}
                <div class="sf-sum-row total"><span>Refund</span><span class="val">${SF.money(r.refund_total)}</span></div>
            </div>
            <div class="mt-2 small text-muted">Reason: ${r.reason_code || '—'}${r.reason ? ' · ' + r.reason : ''}</div>
            ${parseFloat(r.loyalty_reversed) > 0 ? `<div class="mt-1 small text-warning">${r.loyalty_reversed} loyalty point(s) will be reversed on refund.</div>` : ''}
            ${r.status === 'requested' ? '<div class="mt-3"><button class="sf-btn sf-btn-outline btn-sm" onclick="withdrawReturn(' + r.id + ')"><i class="fas fa-times me-1"></i>Withdraw Request</button></div>' : ''}
            ${photos.length ? '<div class="mt-3"><small class="sf-muted">Photos:</small><div class="d-flex flex-wrap gap-2 mt-1">' + photos.map(p => `<img src="${SF_STORE}/${p.path}" width="70" height="70" style="object-fit:cover;border-radius:8px" class="border">`).join('') + '</div></div>' : ''}
        </div>`;
    SF.modal('sfReturnDetailModal').show();
}

async function withdrawReturn(id) {
    if (!confirm('Withdraw this return request?')) return;
    const res = await SF.fetchJSON(SF_API + '/returns.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'cancel', return_id: id }),
    });
    if (res.success) {
        SF.modal('sfReturnDetailModal').hide();
        loadReturns();
        SF.toast('Request withdrawn', 'success');
    } else {
        SF.toast(res.message || 'Could not withdraw', 'error');
    }
}

document.getElementById('sfReturnStatusFilter').addEventListener('change', loadReturns);
loadReturns();
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>