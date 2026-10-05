<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
$pageTitle = 'Return Details';
requireLogin();
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);

$id = (int)$_GET['id'];
$sa = new SaleReturn();
$data = $sa->getById($id);
if (!$data) die('Not found');

$r     = $data['return'];
$items = $data['items'];
$pay   = $data['payments'];
$photos = $data['photos'];

$role     = $_SESSION['user_role'] ?? '';
$isManager = in_array($role, ['admin', 'manager'], true);
$isApprover = in_array($role, ['admin', 'manager', 'branch_manager'], true);
$cashierRefundAllowed = $role !== 'cashier' || (int)setting('return_allow_cashier_refund', 1) === 1;

$statusColors = ['requested' => 'warning', 'approved' => 'info', 'received' => 'primary', 'refunded' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
$srcLabel = $r['type'] === 'online' ? ($r['order_no'] ?? 'Online Order') : ($r['invoice_no'] ?? 'In-store Sale');
$canUploadPhotos = in_array($r['status'], ['requested', 'approved', 'received'], true);
$canExchange = $r['type'] === 'pos' && in_array($r['status'], ['approved', 'received'], true)
    && empty($r['exchange_sale_id']) && (float)$r['refund_total'] > 0;
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><code><?= sanitize($r['return_no']) ?></code>
            <span class="badge bg-<?= $statusColors[$r['status']] ?> align-middle ms-2"><?= $r['status'] ?></span>
        </h4>
        <div class="d-flex gap-2">
            <?php if ($r['status'] === 'requested' && $isApprover): ?>
                <button class="btn btn-success" onclick="approveReturn()"><i class="fas fa-check me-1"></i>Approve</button>
                <button class="btn btn-outline-danger" onclick="rejectReturn()"><i class="fas fa-times me-1"></i>Reject</button>
            <?php endif; ?>
            <?php if ($r['status'] === 'approved'): ?>
                <button class="btn btn-primary" onclick="openReceive()" data-bs-toggle="modal" data-bs-target="#receiveModal"><i class="fas fa-box-open me-1"></i>Receive Goods</button>
            <?php endif; ?>
            <?php if ($canExchange): ?>
                <button class="btn btn-outline-warning" onclick="openExchangeModal(<?= (int)$r['id'] ?>)"><i class="fas fa-exchange-alt me-1"></i>Exchange</button>
            <?php endif; ?>
            <?php if ($r['status'] === 'received' && $cashierRefundAllowed): ?>
                <button class="btn btn-success" onclick="openRefund()" data-bs-toggle="modal" data-bs-target="#refundModal"><i class="fas fa-money-bill-wave me-1"></i>Refund</button>
            <?php endif; ?>
            <?php if (in_array($r['status'], ['approved', 'received', 'refunded'], true) && $isManager): ?>
                <button class="btn btn-outline-danger" onclick="voidReturn()"><i class="fas fa-ban me-1"></i>Void</button>
            <?php endif; ?>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
        </div>
    </div>

    <?php if (!$cashierRefundAllowed && $r['status'] === 'received'): ?>
    <div class="alert alert-warning py-2" style="border-radius:12px">
        <i class="fas fa-user-lock me-2"></i>Cashiers cannot process refunds — ask a manager or admin to complete it.
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Source</h6>
                    <p class="mb-1"><i class="fas fa-<?= $r['type'] === 'online' ? 'shopping-cart' : 'receipt' ?> me-2"></i><?= $srcLabel ?></p>
                    <p class="small text-muted mb-0"><?= $r['type'] === 'online' ? 'Online order' : 'In-store sale' ?> · Branch:
                        <?= sanitize($r['branch_name'] ?? '-') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Customer</h6>
                    <p class="mb-1 fw-semibold"><?= sanitize($r['customer_name'] ?? 'Walk-in') ?></p>
                    <p class="small text-muted mb-0"><?= sanitize($r['customer_phone'] ?? '') ?> · <?= sanitize($r['customer_email'] ?? '') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Return Value</h6>
                    <h3 class="mb-1">$<?= number_format((float)$r['refund_total'], 2) ?></h3>
                    <p class="small text-muted mb-0">subtotal $<?= number_format((float)$r['subtotal'], 2) ?> ·
                        tax $<?= number_format((float)$r['tax'], 2) ?> · ship $<?= number_format((float)$r['shipping'], 2) ?>
                        <?php if ((float)$r['discount'] > 0): ?> · disc −$<?= number_format((float)$r['discount'], 2) ?><?php endif; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-boxes me-2"></i>Returned Items</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th class="text-end">Unit</th><th class="text-end">Total</th><th>Restock</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <?php if (!empty($it['image'])): ?>
                                    <img src="../../<?= sanitize($it['image']) ?>" width="34" height="34" class="rounded me-2" style="object-fit:cover" onerror="this.remove()">
                                <?php endif; ?>
                                <?= sanitize($it['product_name']) ?><br>
                                <code class="small"><?= sanitize($it['sku'] ?? '') ?></code>
                            </td>
                            <td><?= (int)$it['qty'] ?> <?= sanitize($it['unit_symbol'] ?? '') ?></td>
                            <td class="text-end">$<?= number_format((float)$it['unit_price'], 2) ?></td>
                            <td class="text-end"><strong>$<?= number_format((float)$it['total'], 2) ?></strong></td>
                            <td>
                                <?php if ($it['restock']): ?>
                                    <span class="badge bg-success">Restocked</span>
                                <?php else: ?>
                                    <span class="badge bg-<?= $r['status'] === 'received' || $r['status'] === 'refunded' ? 'secondary' : 'light border' ?>">Don't restock</span>
                                <?php endif; ?>
                                <?php if (!empty($it['condition_note'])): ?>
                                    <div class="small text-muted mt-1"><?= sanitize($it['condition_note']) ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-camera me-2"></i>Photos</h5>
                    <?php if ($canUploadPhotos): ?>
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" id="photoInput" accept="image/*" multiple hidden>
                        <button class="btn btn-outline-primary" onclick="document.getElementById('photoInput').click()"><i class="fas fa-upload me-1"></i>Upload photos</button>
                        <button class="btn btn-primary" id="photoUploadBtn" onclick="uploadPhotos()" hidden><i class="fas fa-cloud-upload-alt"></i> Upload</button>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-2" id="photoGrid">
                        <?php if (!$photos): ?>
                        <div class="text-muted small py-2">No photos attached.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fas fa-history me-2"></i>Processing</h5>
                    <table class="table table-sm">
                        <tbody>
                            <tr><th class="text-muted">Requested</th><td><?= date('M d, Y h:i A', strtotime($r['created_at'])) ?> by <?= sanitize($r['requested_by_name'] ?? ($r['requested_for_name'] ?? '—')) ?></td></tr>
                            <?php if ($r['status'] !== 'requested' && $r['approved_at']): ?>
                            <tr><th class="text-muted"><?= $r['status'] === 'rejected' ? 'Rejected' : 'Approved' ?></th><td><?= date('M d, Y h:i A', strtotime($r['approved_at'])) ?> by <?= sanitize($r['approved_by_name'] ?? '—') ?></td></tr>
                            <?php endif; ?>
                            <?php if ($r['received_at']): ?>
                            <tr><th class="text-muted">Received</th><td><?= date('M d, Y h:i A', strtotime($r['received_at'])) ?> by <?= sanitize($r['received_by_name'] ?? '—') ?></td></tr>
                            <?php endif; ?>
                            <?php if ($r['refunded_at']): ?>
                            <tr><th class="text-muted">Refunded</th><td><?= date('M d, Y h:i A', strtotime($r['refunded_at'])) ?> by <?= sanitize($r['refunded_by_name'] ?? '—') ?></td></tr>
                            <?php endif; ?>
                            <?php if ((int)$r['exchange_sale_id'] > 0): ?>
                            <tr><th class="text-muted">Exchanged</th><td>Replacement sale <a href="../sales/receipt.php?id=<?= (int)$r['exchange_sale_id'] ?>" target="_blank">#<?= (int)$r['exchange_sale_id'] ?></a></td></tr>
                            <?php endif; ?>
                            <?php if ((int)$r['loyalty_reversed'] > 0): ?>
                            <tr><th class="text-muted">Loyalty</th><td><span class="badge bg-warning text-dark">−<?= (int)$r['loyalty_reversed'] ?> pts reversed</span></td></tr>
                            <?php endif; ?>
                            <?php if (!empty($r['admin_note']) && strpos($r['admin_note'], 'VOIDED') !== 0): ?>
                            <tr><th class="text-muted">Note</th><td><?= sanitize($r['admin_note']) ?></td></tr>
                            <?php endif; ?>
                            <?php if (!empty($r['admin_note']) && strpos($r['admin_note'], 'VOIDED') === 0): ?>
                            <tr><th class="text-muted text-danger">Voided</th><td class="text-danger"><?= sanitize(substr($r['admin_note'], 7)) ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pay): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-money-bill-wave me-2"></i>Refund Payments</h5>
            <table class="table table-sm">
                <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>By</th></tr></thead>
                <tbody>
                    <?php foreach ($pay as $p): ?>
                    <tr>
                        <td><?= date('M d, Y h:i A', strtotime($p['created_at'])) ?></td>
                        <td><strong>$<?= number_format((float)$p['amount'], 2) ?></strong></td>
                        <td><span class="badge bg-<?= in_array($p['method'], ['customer_balance', 'exchange'], true) ? 'info' : 'light border' ?>"><?= sanitize($p['method']) ?></span></td>
                        <td><?= sanitize($p['reference'] ?? '-') ?></td>
                        <td><?= sanitize($p['processed_by_name'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Receive modal -->
<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box-open me-2"></i>Receive Goods</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Confirm what came back. Restock puts items back into sellable stock; otherwise they are recorded as damaged / unsellable.</p>
                <table class="table table-sm align-middle">
                    <thead><tr><th>Product</th><th>Qty</th><th class="text-center" style="width:90px">Restock</th><th>Condition note</th></tr></thead>
                    <tbody id="receiveItems"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="submitReceive()"><i class="fas fa-check me-1"></i>Confirm Receipt</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Process Refund</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control" id="refundAmount" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Refund Method</label>
                    <select class="form-select" id="refundMethod">
                        <option value="original">Original payment method</option>
                        <?php if ($isManager): ?>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile">Mobile</option>
                        <option value="customer_balance">Customer balance</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reference</label>
                    <input type="text" class="form-control" id="refundReference" placeholder="e.g. transaction id (optional)">
                </div>
                <div class="mb-2">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" id="refundNotes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="submitRefund()"><i class="fas fa-check me-1"></i>Confirm Refund</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/exchange-modal.php'; ?>

<script>
const returnId = <?= (int)$id ?>;
const statusColors = <?= json_encode($statusColors) ?>;
const photos = <?= json_encode($photos) ?>;
const items = <?= json_encode($items) ?>;
const autoRestock = <?= (int)setting('return_auto_restock', 1) === 1 ? 'true' : 'false' ?>;

function renderPhotos() {
    const grid = document.getElementById('photoGrid');
    if (!photos.length) return;
    grid.innerHTML = photos.map(p => `
        <div class="position-relative" style="width:86px;height:86px">
            <img src="<?= APP_URL ?>/" + p.path + "" width="86" height="86" class="rounded border" style="object-fit:cover">
            <button class="btn btn-sm btn-danger position-absolute top-0 end-0" style="--bs-btn-padding-y:.08rem;--bs-btn-padding-x:.3rem" onclick="deletePhoto(${p.id})" title="Delete"><i class="fas fa-times"></i></button>
        </div>`).join('');
}

function deletePhoto(photoId) {
    if (!confirm('Delete this photo?')) return;
    apiRequest(`<?= APP_URL ?>/api/returns/photos.php?id=${photoId}`, 'DELETE').then(res => {
        if (res.success) location.reload();
        else alert(res.message);
    });
}

function openReceive() {
    document.getElementById('receiveItems').innerHTML = items.map(i => `
        <tr>
            <td>${i.product_name}</td>
            <td>${i.qty}</td>
            <td class="text-center"><input type="checkbox" class="form-check-input receive-restock" data-item="${i.id}" ${autoRestock ? 'checked' : ''}></td>
            <td><input type="text" class="form-control form-control-sm receive-note" data-item="${i.id}" placeholder="condition..."></td>
        </tr>`).join('');
    new bootstrap.Modal(document.getElementById('receiveModal')).show();
}

async function submitReceive() {
    const decisions = items.map(i => ({
        return_item_id: i.id,
        restock: document.querySelector(`.receive-restock[data-item="${i.id}"]`).checked ? 1 : 0,
        condition_note: document.querySelector(`.receive-note[data-item="${i.id}"]`).value.trim(),
    }));
    const res = await apiRequest('<?= APP_URL ?>/api/returns/receive.php', 'POST', { return_id: returnId, items: decisions });
    if (!res.success) { alert(res.message); return; }
    bootstrap.Modal.getInstance(document.getElementById('receiveModal')).hide();
    location.reload();
}

function openRefund() {
    document.getElementById('refundAmount').value = '<?= number_format((float)$r['refund_total'], 2) ?>';
    document.getElementById('refundReference').value = '';
    document.getElementById('refundNotes').value = '';
    new bootstrap.Modal(document.getElementById('refundModal')).show();
}

async function submitRefund() {
    const res = await apiRequest('<?= APP_URL ?>/api/returns/refund.php', 'POST', {
        return_id: returnId,
        method: document.getElementById('refundMethod').value,
        reference: document.getElementById('refundReference').value.trim(),
        notes: document.getElementById('refundNotes').value.trim(),
    });
    if (!res.success) { alert(res.message); return; }
    bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
    location.reload();
}

async function approveReturn() {
    if (!confirm('Approve this return request?')) return;
    const res = await apiRequest('<?= APP_URL ?>/api/returns/approve.php', 'POST', { return_id: returnId, action: 'approve', note: '' });
    if (res.success) location.reload();
    else alert(res.message);
}

async function rejectReturn() {
    const note = prompt('Reason for rejecting this request:');
    if (note === null) return;
    if (!note.trim()) { alert('A reason is required.'); return; }
    const res = await apiRequest('<?= APP_URL ?>/api/returns/approve.php', 'POST', { return_id: returnId, action: 'reject', note: note.trim() });
    if (res.success) location.reload();
    else alert(res.message);
}

async function voidReturn() {
    const reason = prompt('Why is this return being voided? This reverses any restock and refund.');
    if (reason === null) return;
    if (!reason.trim()) { alert('A reason is required.'); return; }
    if (!confirm('Void this return? This cannot be undone.')) return;
    const res = await apiRequest('<?= APP_URL ?>/api/returns/void.php', 'POST', { return_id: returnId, reason: reason.trim() });
    if (res.success) location.reload();
    else alert(res.message);
}

document.getElementById('photoInput').addEventListener('change', function () {
    document.getElementById('photoUploadBtn').hidden = !this.files.length;
});

async function uploadPhotos() {
    const input = document.getElementById('photoInput');
    if (!input.files.length) return;
    const fd = new FormData();
    fd.append('return_id', returnId);
    Array.from(input.files).forEach(f => fd.append('files[]', f));
    const res = await fetch('<?= APP_URL ?>/api/returns/photos.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) location.reload();
    else alert(json.message || 'Upload failed');
}

renderPhotos();
</script>

<?php require_once '../../includes/footer.php'; ?>