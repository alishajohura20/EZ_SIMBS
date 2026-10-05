<?php
/*
 * Shared "Exchange Items" bootstrap modal + JS. A POS return and a fresh
 * sale in ONE click: restock decisions inline, replacement items at current
 * catalog prices, and the difference settled in the same atomic call.
 *
 * Requires app.js (apiRequest) and the Bootstrap bundle, both loaded by the
 * admin header. Include from pages/sales/*.php and pages/returns/*.php —
 * partials live two levels below the project root so ../../ resolves.
 *
 * Entry points:
 *   openExchangeForSale(saleId)  — sales list / receipt: resolves the most
 *                                  recent exchangeable return for the sale
 *                                  (else falls back to the Return modal)
 *   openExchangeModal(returnId)  — Returns detail: exchange a specific RMA
 */
$__role = $_SESSION['user_role'] ?? '';
$__taxRate = (float)setting('tax_rate', 0);
?>
<div class="modal fade" id="exchangeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2 text-primary"></i>Exchange Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted" id="exchSourceLabel">In-store Sale</span>
                    <span class="badge bg-info text-dark" id="exchReturnStatus"></span>
                </div>

                <h6 class="text-muted text-uppercase small mt-2"><i class="fas fa-box-open me-1"></i>Goods coming back</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle" style="min-width:420px">
                        <thead><tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Value</th><th class="text-center" style="width:100px">Restock</th><th>Condition</th></tr></thead>
                        <tbody id="exchReturnItems"></tbody>
                    </table>
                </div>

                <h6 class="text-muted text-uppercase small"><i class="fas fa-boxes me-1"></i>Replacement items <span class="fw-normal">(current catalog prices)</span></h6>
                <div class="input-group input-group-sm mb-2" style="max-width:360px">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="exchProductSearch" placeholder="Filter products...">
                </div>
                <div class="table-responsive mb-3" style="max-height:280px;overflow:auto">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Product</th><th class="text-end">Price</th><th class="text-end">In stock</th><th class="text-center" style="width:90px">Qty</th><th class="text-end">Line</th></tr></thead>
                        <tbody id="exchReplacementItems"></tbody>
                    </table>
                </div>

                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex flex-wrap gap-3">
                            <div><span class="text-muted small d-block">Exchange credit</span><strong class="text-info" id="exchCredit">$0.00</strong></div>
                            <div><span class="text-muted small d-block">New total</span><strong id="exchNewTotal">$0.00</strong></div>
                            <div><span class="text-muted small d-block">Difference</span><strong id="exchDifference">$0.00</strong></div>
                        </div>
                    </div>
                    <div class="col-md-5" id="exchPaymentWrap" hidden>
                        <label class="form-label small mb-1">Pay the difference</label>
                        <div class="row g-1">
                            <div class="col-md-5"><input type="number" step="0.01" class="form-control form-control-sm exch-pay" data-method="cash" placeholder="Cash"></div>
                            <div class="col-md-4"><input type="number" step="0.01" class="form-control form-control-sm exch-pay" data-method="card" placeholder="Card"></div>
                            <div class="col-md-3"><input type="number" step="0.01" class="form-control form-control-sm exch-pay" data-method="mobile" placeholder="Mobile"></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning py-2 mt-3 mb-0" id="exchCashoutAlert" hidden>
                    <i class="fas fa-info-circle me-2"></i>This exchange pays the customer <strong>money back</strong> — a manager or admin must complete it.
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small me-auto" id="exchHint"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="exchSubmitBtn" onclick="submitExchange()"><i class="fas fa-exchange-alt me-1"></i>Complete Exchange</button>
            </div>
        </div>
    </div>
</div>
<script>
const userRole = '<?= sanitize($__role) ?>';
const exchTaxRate = <?= (float)$__taxRate ?>;
let __exchCtx = null;
let __exchCatalog = null;

async function openExchangeForSale(saleId) {
    // Resolve the most recent exchangeable return for this sale. A return in
    // 'approved' or 'received' with no prior exchange is the entry point;
    // otherwise drop into the Return modal (an exchange needs a live RMA).
    const res = await apiRequest(`<?= APP_URL ?>/api/returns/index.php?sale_id=${saleId}&per_page=1`);
    if (res.success && res.data.returns) {
        const elig = res.data.returns.find(x =>
            ['approved', 'received'].includes(x.status) && !x.exchange_sale_id);
        if (elig) return openExchangeModal(elig.id);
    }
    openReturnModal(saleId);
}

async function openExchangeModal(returnId) {
    const res = await apiRequest(`<?= APP_URL ?>/api/returns/index.php?id=${returnId}`);
    if (!res.success) { alert(res.message || 'Could not load the return'); return; }
    const d = res.data;
    const it = d.return;

    if (d.return.exchange_sale_id) return alert('This return has already been exchanged.');
    if (['approved', 'received'].includes(it.status) === false) {
        return alert('Receive the goods before exchanging this return.');
    }

    __exchCtx = { returnId: +returnId };
    document.getElementById('exchReturnStatus').textContent = it.status;
    document.getElementById('exchSourceLabel').textContent =
        'RMA ' + it.return_no + ' · ' + (it.invoice_no ?? 'In-store sale');
    document.getElementById('exchCredit').textContent = '$' + Number(it.refund_total ?? 0).toFixed(2);

    document.getElementById('exchReturnItems').innerHTML = (d.items || []).map(i => `
        <tr>
            <td>${i.product_name}${i.sku ? ` <code class="small">${i.sku}</code>` : ''}</td>
            <td class="text-center">${i.qty}</td>
            <td class="text-end">$${Number(i.total).toFixed(2)}</td>
            <td class="text-center">
                <div class="form-check form-switch d-inline-block">
                    <input class="form-check-input exch-restock" type="checkbox" data-ri="${i.id}" checked>
                </div>
            </td>
            <td><input class="form-control form-control-sm exch-note" data-ri="${i.id}" placeholder="Damaged?"></td>
        </tr>`).join('');

    // Prefill the restock decision from the live document when already received
    (d.items || []).forEach(i => {
        if (i.restock === 0) {
            const el = document.querySelector(`.exch-restock[data-ri="${i.id}"]`);
            if (el) el.checked = false;
        }
        if (i.condition_note) {
            const el = document.querySelector(`.exch-note[data-ri="${i.id}"]`);
            if (el) el.value = i.condition_note;
        }
    });

    if (!__exchCatalog) {
        const cat = await apiRequest(`<?= APP_URL ?>/api/products/index.php?per_page=500&status=active`);
        __exchCatalog = (cat.success ? cat.data.products : []) || [];
    }
    renderExchCatalog();

    new bootstrap.Modal(document.getElementById('exchangeModal')).show();
}

function renderExchCatalog() {
    const q = (document.getElementById('exchProductSearch').value || '').toLowerCase();
    document.getElementById('exchReplacementItems').innerHTML =
        __exchCatalog.filter(p => Number(p.total_stock ?? 0) > 0)
            .filter(p => !q || p.name.toLowerCase().includes(q) || (p.sku || '').toLowerCase().includes(q))
            .map(p => `
        <tr>
            <td><strong>${esc(p.name)}</strong><br><small class="text-muted">${esc(p.sku || '')}</small></td>
            <td class="text-end">$${Number(p.price ?? 0).toFixed(2)}</td>
            <td class="text-end">${Number(p.total_stock ?? 0)}</td>
            <td class="text-center"><input type="number" class="form-control form-control-sm exch-qty" data-pid="${p.id}" data-price="${Number(p.price)}" min="0" value="0"></td>
            <td class="text-end exch-line">$0.00</td>
        </tr>`).join('') ||
        '<tr><td colspan="5" class="text-center text-muted py-3">No products in stock.</td></tr>';
    recalcExch();
}

function esc(s) { return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

function recalcExch() {
    const credit = parseFloat(document.getElementById('exchCredit').textContent.replace('$', '')) || 0;
    let subtotal = 0;
    document.querySelectorAll('.exch-qty').forEach(el => {
        const qty = Math.max(0, parseInt(el.value, 10) || 0);
        const line = qty * (parseFloat(el.dataset.price) || 0);
        el.closest('tr').querySelector('.exch-line').textContent = '$' + line.toFixed(2);
        subtotal += line;
    });
    const tax = subtotal * exchTaxRate / 100;
    const newTotal = subtotal + tax;
    const diff = newTotal - credit;

    document.getElementById('exchNewTotal').textContent = '$' + newTotal.toFixed(2);
    const diffEl = document.getElementById('exchDifference');
    diffEl.textContent = '$' + diff.toFixed(2);
    diffEl.className = diff < 0 ? 'text-danger' : 'text-success';

    // Payment inputs (positive difference only)
    const payWrap = document.getElementById('exchPaymentWrap');
    payWrap.hidden = !(diff > 0);
    const box = document.getElementById('exchCashoutAlert');
    box.hidden = !(diff < 0);
    const btn = document.getElementById('exchSubmitBtn');
    btn.disabled = diff < 0 && userRole === 'cashier';
    document.getElementById('exchHint').textContent =
        diff < 0 ? 'Negative difference — admin/manager only.'
            : (diff > 0 ? 'Collect the difference from the customer.' : 'Nothing to pay — the credit covers it.');

    if (diff > 0) {
        document.querySelectorAll('.exch-pay').forEach(el => el.value = '');
        const first = document.querySelector('.exch-pay');
        if (first) first.value = diff.toFixed(2);
    }
}

async function submitExchange() {
    const d = document.getElementById('exchDifference').textContent.replace('$', '');
    const diff = parseFloat(d);
    if (diff < 0 && userRole === 'cashier') {
        return alert('This exchange pays the customer money back — a manager or admin must do it.');
    }

    const items = Array.from(document.querySelectorAll('.exch-restock')).map(el => ({
        return_item_id: parseInt(el.dataset.ri, 10),
        restock: el.checked ? 1 : 0,
        condition_note: (document.querySelector(`.exch-note[data-ri="${el.dataset.ri}"]`).value || '').trim(),
    }));
    const repl = Array.from(document.querySelectorAll('.exch-qty'))
        .filter(el => (parseInt(el.value, 10) || 0) > 0)
        .map(el => ({ product_id: parseInt(el.dataset.pid, 10), qty: parseInt(el.value, 10) }));
    const payments = Array.from(document.querySelectorAll('.exch-pay'))
        .filter(el => (parseFloat(el.value) || 0) > 0)
        .map(el => ({ method: el.dataset.method, amount: parseFloat(el.value), reference: '' }));

    if (!repl.length) return alert('Choose at least one replacement item.');
    if (diff > 30 && !payments.length) return alert('Collect a payment for the price difference.');

    const res = await apiRequest(`<?= APP_URL ?>/api/returns/exchange.php`, 'POST', {
        return_id: __exchCtx.returnId,
        items,
        replacement_items: repl,
        payments,
    });
    if (!res.success) { alert(res.message || 'Exchange failed'); return; }

    bootstrap.Modal.getInstance(document.getElementById('exchangeModal')).hide();
    if (res.data.new_sale_id) {
        window.open(`<?= APP_URL ?>/pages/sales/receipt.php?id=${res.data.new_sale_id}`, '_blank');
    }
    setTimeout(() => location.reload(), 600);
}

document.addEventListener('input', function (e) {
    if (e.target.classList && (e.target.classList.contains('exch-qty') || e.target.classList.contains('exch-pay'))) recalcExch();
    if (e.target.id === 'exchProductSearch') renderExchCatalog();
});
</script>