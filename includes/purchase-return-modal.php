<?php
/*
 * Shared "Supplier Return" bootstrap modal + JS for the Purchases pages.
 * Requires app.js (apiRequest) and the Bootstrap bundle (loaded by header).
 * Pages include it must be two levels below the project root.
 */
$__supplierReturnReasons = array_filter(array_map('trim', explode(',', setting('supplier_return_reasons', 'defective,damaged,wrong_stock,overstock,expired,other'))));
?>
<div class="modal fade" id="purchaseReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck-loading me-2"></i>Return Goods to Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted" id="pReturnSourceLabel">Purchase Order</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3" style="min-width:440px">
                        <thead>
                            <tr><th>Product</th><th class="text-center">Returned / Qty</th><th style="width:100px">Return Qty</th><th class="text-end" style="width:120px">Returnable</th></tr>
                        </thead>
                        <tbody id="pReturnItems"></tbody>
                    </table>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <select id="pReturnReason" class="form-select">
                            <option value="">-- Choose a reason --</option>
                            <?php foreach ($__supplierReturnReasons as $rr): ?>
                            <option value="<?= sanitize($rr) ?>"><?= sanitize(ucwords(str_replace('_', ' ', $rr))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <input type="text" class="form-control" id="pReturnNotes" maxlength="255" placeholder="Optional">
                    </div>
                </div>
                <div class="mt-2 small text-muted">Only quantities already received and not previously returned can be sent back.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPurchaseReturnRequest()"><i class="fas fa-check me-1"></i>Create Return</button>
            </div>
        </div>
    </div>
</div>
<script>
let __pRetCtx = null;

async function openPurchaseReturnModal(purchaseId) {
    const res = await apiRequest(`../../api/purchase-returns/returnable.php?purchase_id=${purchaseId}`);
    if (!res.success) { alert(res.message || 'Could not load purchase'); return; }
    const d = res.data;
    if (!d.returnable) { alert((d.blockers || []).join('\n') || 'This purchase has nothing to return.'); return; }

    __pRetCtx = { purchase_id: purchaseId };
    document.getElementById('pReturnSourceLabel').textContent =
        `PO ${d.po_number} &middot; ${d.supplier_name}`;
    document.getElementById('pReturnItems').innerHTML =
        (d.items || []).filter(i => i.returnable_qty > 0).map(i => `
            <tr>
                <td>${i.product_name}${i.sku ? ` <code class="small">${i.sku}</code>` : ''}</td>
                <td class="text-center">${i.already_returned_qty} / ${i.received_qty}</td>
                <td><input type="number" class="form-control form-control-sm p-return-qty" data-line="${i.id}" min="0" max="${i.returnable_qty}" value="0"></td>
                <td class="text-end">$${Number(i.returnable_subtotal).toFixed(2)}</td>
            </tr>`).join('') ||
        '<tr><td colspan="4" class="text-center text-muted py-3">Nothing returnable on this purchase.</td></tr>';

    document.getElementById('pReturnReason').selectedIndex = 0;
    document.getElementById('pReturnNotes').value = '';
    new bootstrap.Modal(document.getElementById('purchaseReturnModal')).show();
}

async function submitPurchaseReturnRequest() {
    const items = Array.from(document.querySelectorAll('.p-return-qty'))
        .map(el => ({ purchase_item_id: parseInt(el.dataset.line, 10), qty: parseInt(el.value, 10) }))
        .filter(i => i.qty > 0);
    const reason = document.getElementById('pReturnReason').value;
    if (!items.length) { alert('Select at least one item to return.'); return; }
    if (!reason) { alert('Choose a return reason.'); return; }

    const res = await apiRequest('../../api/purchase-returns/index.php', 'POST', {
        purchase_id: __pRetCtx.purchase_id,
        items,
        reason_code: reason,
        notes: document.getElementById('pReturnNotes').value,
    });
    if (!res.success) { alert(res.message || 'Could not create the return.'); return; }
    bootstrap.Modal.getInstance(document.getElementById('purchaseReturnModal')).hide();
    location.href = `../../pages/purchase-returns/view.php?id=${res.data.id}`;
}
</script>