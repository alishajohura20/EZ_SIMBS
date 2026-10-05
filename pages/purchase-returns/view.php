<?php
$pageTitle = 'Supplier Return Detail';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$detail = (new PurchaseReturn())->getById($id);
if (!$detail) { echo '<div class="alert alert-danger">Return not found</div>'; require_once '../../includes/footer.php'; exit; }
$canVoid = in_array($_SESSION['user_role'], ['admin', 'manager'], true);
$statusColors = ['requested' => 'warning', 'approved' => 'info', 'received' => 'primary', 'credited' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
$r = $detail['return'];
$items = $detail['items'];
$pay = $detail['payments'];
$photos = $detail['photos'];
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <code><?= htmlspecialchars($r['return_no']) ?></code>
            <span class="badge bg-<?= $statusColors[$r['status']] ?> align-middle ms-2"><?= $r['status'] ?></span>
        </h2>
        <div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back</a>
            <?php if ($r['status'] === 'requested'): ?>
            <button class="btn btn-success btn-sm" id="approveBtn"><i class="fas fa-check me-1"></i>Approve</button>
            <button class="btn btn-outline-danger btn-sm" id="rejectBtn"><i class="fas fa-times me-1"></i>Reject</button>
            <?php endif; ?>
            <?php if ($r['status'] === 'approved'): ?>
            <button class="btn btn-primary btn-sm" id="receiveBtn">
                <i class="fas fa-truck me-1"></i>Mark Shipped Back</button>
            <?php endif; ?>
            <?php if ($r['status'] === 'received'): ?>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#creditModal">
                <i class="fas fa-file-invoice-dollar me-1"></i>Issue Credit</button>
            <?php endif; ?>
            <?php if ($canVoid && in_array($r['status'], ['approved','received','credited'], true)): ?>
            <button class="btn btn-outline-danger btn-sm" id="voidBtn">
                <i class="fas fa-ban me-1"></i>Void</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header fw-bold">Returned Items</div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead><tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit Cost</th>
                            <th class="text-end">Total</th><th>Condition</th></tr></thead>
                        <tbody id="itemRows"></tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">Credit Ledger</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Amount</th><th>Method</th><th>Reference</th><th>By</th><th>When</th></tr></thead>
                        <tbody id="paymentRows"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-bold">Summary</div>
                <div class="card-body">
                    <dl class="mb-0 small">
                        <dt>Purchase</dt><dd><code><?= htmlspecialchars($r['po_number'] ?? '-') ?></code></dd>
                        <dt>Status</dt><dd><span class="badge bg-<?= $statusColors[$r['status']] ?>"><?= $r['status'] ?></span></dd>
                        <dt>Supplier</dt><dd><?= htmlspecialchars($r['supplier_name'] ?? '-') ?></dd>
                        <dt>Supplier balance</dt>
                        <dd>$<?= number_format((float)($r['supplier_balance'] ?? 0), 2) ?>
                            <?php if ($canVoid): ?>
                            <a href="#" class="small ms-1" data-bs-toggle="modal" data-bs-target="#refundBalanceModal">refund</a>
                            <?php endif; ?>
                        </dd>
                        <dt>Reason</dt><dd class="text-capitalize"><?= htmlspecialchars($r['reason_code'] ?? '-') ?></dd>
                        <dt>Branch</dt><dd><?= htmlspecialchars($r['branch_name'] ?? '-') ?></dd>
                    </dl>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Returned value</span><strong>$<?= number_format((float)$r['subtotal'], 2) ?></strong></div>
                    <div class="d-flex justify-content-between text-success"><span>+ Tax share</span><span>$<?= number_format((float)$r['tax'], 2) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5"><strong>Credit</strong>
                        <strong class="text-success">$<?= number_format((float)$r['credit_total'], 2) ?></strong></div>
                    <div class="d-flex justify-content-between small mt-2"><span>Applied to PO due</span><span>$<?= number_format((float)$r['applied_to_due'], 2) ?></span></div>
                    <div class="d-flex justify-content-between small"><span>To supplier balance</span><span>$<?= number_format((float)$r['to_balance'], 2) ?></span></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    Evidence
                    <label class="btn btn-sm btn-outline-primary mb-0">
                        <i class="fas fa-camera me-1"></i>Add
                        <input type="file" id="photoInput" accept="image/*" multiple hidden>
                        <button class="btn btn-sm btn-primary ms-1" id="photoUploadBtn" hidden><i class="fas fa-upload"></i></button>
                    </label>
                </div>
                <div class="card-body"><div class="row g-2" id="photoGrid"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Credit modal -->
<div class="modal fade" id="creditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>Issue Credit</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">The credit settles <?= htmlspecialchars($r['po_number'] ?? 'the PO') ?>'s remaining due first ($<?= number_format((float)($r['purchase_due'] ?? 0), 2) ?>); any overflow rides the supplier's balance.</p>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="creditAmount" readonly value="<?= number_format((float)$r['credit_total'], 2) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Method</label>
                    <select class="form-select" id="creditMethod">
                        <option value="credit_note">Credit note (settle due / balance)</option>
                        <option value="bank_transfer">Bank transfer (cash returned)</option>
                        <option value="cash">Cash (cash returned)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reference</label>
                    <input type="text" class="form-control" id="creditReference" placeholder="e.g. bank ref (optional)">
                </div>
                <div class="mb-2">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" id="creditNotes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="submitCredit()"><i class="fas fa-check me-1"></i>Book Credit</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund balance modal -->
<div class="modal fade" id="refundBalanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-hand-holding-usd me-2"></i>Refund Supplier Balance</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Record money the supplier actually paid back. This reduces their parked balance.</p>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="refundBalanceAmount">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Reference</label>
                    <input type="text" class="form-control" id="refundBalanceReference" placeholder="optional">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="submitBalanceRefund()">Record refund</button>
            </div>
        </div>
    </div>
</div>

<script>
const returnId = <?= (int)$id ?>;
const supplierId = <?= (int)$r['supplier_id'] ?>;
const items = <?= json_encode($items) ?>;
const payments = <?= json_encode($pay) ?>;
const photos = <?= json_encode($photos) ?>;

function renderItems() {
    document.getElementById('itemRows').innerHTML = items.map(i => `
        <tr>
            <td>
                ${i.image ? `<img src="../../${i.image}" width="34" height="34" class="rounded me-2" style="object-fit:cover" onerror="this.remove()">` : ''}
                ${i.product_name}<br><code class="small">${i.sku || ''}</code>
            </td>
            <td class="text-end">${i.qty} ${i.unit_symbol || ''}</td>
            <td class="text-end">$${Number(i.unit_cost).toFixed(2)}</td>
            <td class="text-end"><strong>$${Number(i.total).toFixed(2)}</strong></td>
            <td class="small text-muted">${i.condition_note || '-'}</td>
        </tr>`).join('') || '<tr><td colspan="5" class="text-center text-muted">No items</td></tr>';
}

function renderPayments() {
    document.getElementById('paymentRows').innerHTML = payments.map(p => `
        <tr>
            <td><strong>$${Number(p.amount).toFixed(2)}</strong></td>
            <td><span class="badge bg-info">${p.method}</span></td>
            <td class="small">${p.reference || '-'}</td>
            <td class="small">${p.added_by_name || '-'}</td>
            <td class="small text-muted">${new Date(p.created_at).toLocaleString()}</td>
        </tr>`).join('') || '<tr><td colspan="5" class="text-center text-muted">Not credited yet</td></tr>';
}

function renderPhotos() {
    const grid = document.getElementById('photoGrid');
    if (!photos.length) { grid.innerHTML = '<div class="text-muted small">No photos attached.</div>'; return; }
    grid.innerHTML = photos.map(p => `
        <div class="col-4 position-relative mb-2">
            <img src="../../${p.path}" class="w-100 rounded border" style="aspect-ratio:1;object-fit:cover">
            <button class="btn btn-sm btn-danger position-absolute top-0 end-0" style="--bs-btn-padding-y:.08rem;--bs-btn-padding-x:.3rem" onclick="deletePhoto(${p.id})"><i class="fas fa-times"></i></button>
        </div>`).join('');
}

async function deletePhoto(photoId) {
    if (!confirm('Delete this photo?')) return;
    const res = await apiRequest('../../api/returns/photos.php?id=' + photoId, 'DELETE');
    if (res.success) location.reload();
    else alert(res.message);
}

document.getElementById('approveBtn')?.addEventListener('click', async () => {
    if (!confirm('Approve this supplier return request?')) return;
    const res = await apiRequest('../../api/purchase-returns/approve.php', 'POST', { return_id: returnId, action: 'approve', note: '' });
    if (res.success) location.reload();
    else alert(res.message);
});

document.getElementById('rejectBtn')?.addEventListener('click', async () => {
    const note = prompt('Reason for rejecting this request:');
    if (note === null) return;
    if (!note.trim()) { alert('A reason is required.'); return; }
    const res = await apiRequest('../../api/purchase-returns/approve.php', 'POST', { return_id: returnId, action: 'reject', note: note.trim() });
    if (res.success) location.reload();
    else alert(res.message);
});

document.getElementById('receiveBtn')?.addEventListener('click', async () => {
    if (!confirm('Mark these goods as shipped back to the supplier? Stock leaves inventory.')) return;
    const res = await apiRequest('../../api/purchase-returns/receive.php', 'POST', { return_id: returnId, items: [] });
    if (res.success) location.reload();
    else alert(res.message);
});

async function submitCredit() {
    const res = await apiRequest('../../api/purchase-returns/credit.php', 'POST', {
        return_id: returnId,
        method: document.getElementById('creditMethod').value,
        reference: document.getElementById('creditReference').value.trim(),
        notes: document.getElementById('creditNotes').value.trim(),
    });
    if (!res.success) { alert(res.message); return; }
    bootstrap.Modal.getInstance(document.getElementById('creditModal')).hide();
    location.reload();
}

document.getElementById('voidBtn')?.addEventListener('click', async () => {
    const reason = prompt('Why is this return being voided? This reverses any stock shipment and credit.');
    if (reason === null) return;
    if (!reason.trim()) { alert('A reason is required.'); return; }
    if (!confirm('Void this return? This cannot be undone.')) return;
    const res = await apiRequest('../../api/purchase-returns/void.php', 'POST', { return_id: returnId, reason: reason.trim() });
    if (res.success) location.reload();
    else alert(res.message);
});

async function submitBalanceRefund() {
    const amount = parseFloat(document.getElementById('refundBalanceAmount').value);
    if (!amount || amount <= 0) { alert('Enter a positive amount.'); return; }
    const res = await apiRequest('../../api/suppliers/balance.php', 'POST', {
        supplier_id: supplierId, action: 'refund', amount,
        reference: document.getElementById('refundBalanceReference').value.trim(),
    });
    if (!res.success) { alert(res.message); return; }
    bootstrap.Modal.getInstance(document.getElementById('refundBalanceModal')).hide();
    location.reload();
}

document.getElementById('photoInput').addEventListener('change', async function () {
    if (!this.files.length) return;
    const fd = new FormData();
    fd.append('return_id', returnId);
    fd.append('kind', 'purchase');
    Array.from(this.files).forEach(f => fd.append('photos[]', f));
    const res = await fetch('../../api/returns/photos.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) location.reload();
    else alert(json.message || 'Upload failed');
});

renderItems();
renderPayments();
renderPhotos();
</script>

<?php require_once '../../includes/footer.php'; ?>