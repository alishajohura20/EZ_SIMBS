<?php
/*
 * Shared "Return Items" bootstrap modal + JS for the Sales, Invoices and
 * Returns pages. Requires app.js (apiRequest / renderPagination) and the
 * Bootstrap bundle, both of which the admin header already loads.
 *
 * Pages that include this partial must be two levels below the project root
 * (pages/<area>/<file>.php) so the ../../ relative paths resolve.
 */
$__returnReasons = array_filter(array_map('trim', explode(',', setting('return_reasons', 'damaged,wrong_item,not_as_described,defective,changed_mind,other'))));
?>
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-undo-alt me-2"></i>Return Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted" id="returnSourceLabel">In-store Sale</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3" style="min-width:440px">
                        <thead>
                            <tr><th>Product</th><th class="text-center">Returned / Qty</th><th style="width:100px">Return Qty</th><th class="text-end" style="width:120px">Refundable</th></tr>
                        </thead>
                        <tbody id="returnItems"></tbody>
                    </table>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <select id="returnReason" class="form-select">
                            <option value="">-- Choose a reason --</option>
                            <?php foreach ($__returnReasons as $rr): ?>
                            <option value="<?= sanitize($rr) ?>"><?= sanitize(ucwords(str_replace('_', ' ', $rr))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-text">Items listed are still within their return window and were not already requested.</div>
                    </div>
                </div>
                <div class="mt-3" id="overrideWrap" hidden>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="overrideWindow">
                        <label class="form-check-label fw-semibold" for="overrideWindow" id="overrideLabel"></label>
                    </div>
                </div>
                <div class="mt-2 small" id="returnAuthHint"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReturnRequest()"><i class="fas fa-check me-1"></i>Create Return</button>
            </div>
        </div>
    </div>
</div>
<script>
let __retCtx = null;

async function openReturnModal(saleId) {
    const res = await apiRequest(`../../api/returns/returnable.php?sale_id=${saleId}`);
    if (!res.success) { alert(res.message || 'Could not load sale'); return; }
    const d = res.data;
    if (!d.returnable) { alert((d.blockers || []).join('\n') || 'This sale is not returnable.'); return; }

    __retCtx = { type: 'pos', sale_id: saleId };
    document.getElementById('returnSourceLabel').textContent = d.source_label || 'In-store Sale';
    document.getElementById('returnItems').innerHTML =
        (d.items || []).filter(i => i.returnable_qty > 0).map(i => `
            <tr>
                <td>${i.product_name}${i.sku ? ` <code class="small">${i.sku}</code>` : ''}</td>
                <td class="text-center">${i.already_returned_qty} / ${i.qty}</td>
                <td><input type="number" class="form-control form-control-sm return-qty" data-line="${i.id}" min="0" max="${i.returnable_qty}" value="0"></td>
                <td class="text-end">$${Number(i.returnable_amount).toFixed(2)}</td>
            </tr>`).join('') ||
        '<tr><td colspan="4" class="text-center text-muted py-3">No returnable items on this sale.</td></tr>';

    document.getElementById('returnReason').selectedIndex = 0;
    const wrap = document.getElementById('overrideWrap');
    wrap.hidden = !(d.can_override_window && !d.within_window);
    if (!wrap.hidden) {
        document.getElementById('overrideLabel').textContent =
            `This sale is outside the ${d.window_days}-day return window (${d.age_days} day(s) old) — check to override.`;
    }
    document.getElementById('returnAuthHint').innerHTML =
        (d.can_override_window ? '' : '<i class="fas fa-info-circle me-1"></i>Only a manager or admin can accept returns outside the window.');
    new bootstrap.Modal(document.getElementById('returnModal')).show();
}

async function submitReturnRequest() {
    const items = Array.from(document.querySelectorAll('.return-qty'))
        .map(el => ({ sale_item_id: parseInt(el.dataset.line, 10), qty: parseInt(el.value, 10) }))
        .filter(i => i.qty > 0);
    const reason = document.getElementById('returnReason').value;
    if (!items.length) { alert('Select at least one item to return.'); return; }
    if (!reason) { alert('Choose a return reason.'); return; }

    const res = await apiRequest('../../api/returns/index.php', 'POST', {
        type: __retCtx.type,
        sale_id: __retCtx.sale_id,
        items,
        reason_code: reason,
        override_window: document.getElementById('overrideWindow').checked || undefined,
    });
    if (!res.success) { alert(res.message || 'Could not create the return.'); return; }
    bootstrap.Modal.getInstance(document.getElementById('returnModal')).hide();
    location.href = `../../pages/returns/view.php?id=${res.data.id}`;
}
</script>