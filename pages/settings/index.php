<?php
$pageTitle = 'Settings';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <h4 class="mb-4"><i class="fas fa-cog"></i> System Settings</h4>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#companyTab">Company</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#billingTab">Billing</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inventoryTab">Inventory</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#returnsTab">Returns</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#appearanceTab">Appearance</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#backupTab">Backup</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="companyTab">
            <div class="card"><div class="card-body">
                <form id="companyForm">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Company Name</label><input type="text" class="form-control" id="company_name"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" id="company_email"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" id="company_phone"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tax ID</label><input type="text" class="form-control" id="company_tax_id"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" id="company_address" rows="2"></textarea></div>
                    <button type="submit" class="btn btn-primary">Save Company Info</button>
                </form>
            </div></div>
        </div>

        <div class="tab-pane fade" id="billingTab">
            <div class="card"><div class="card-body">
                <form id="billingForm">
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Tax Rate (%)</label><input type="number" step="0.01" class="form-control" id="tax_rate"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Currency</label><input type="text" class="form-control" id="currency"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Currency Symbol</label><input type="text" class="form-control" id="currency_symbol"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Invoice Prefix</label><input type="text" class="form-control" id="invoice_prefix"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">PO Prefix</label><input type="text" class="form-control" id="po_prefix"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Billing Settings</button>
                </form>
            </div></div>
        </div>

        <div class="tab-pane fade" id="inventoryTab">
            <div class="card"><div class="card-body">
                <form id="inventoryForm">
                    <div class="mb-3"><label class="form-label">Low Stock Threshold</label><input type="number" class="form-control" id="low_stock_threshold"></div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div></div>
        </div>

        <div class="tab-pane fade" id="returnsTab">
            <div class="card"><div class="card-body">
                <form id="returnsForm">
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Return Window (days)</label>
                            <input type="number" min="1" class="form-control" id="return_window_days"><div class="form-text">Matches the storefront "Easy Returns" promise.</div></div>
                        <div class="col-md-8 mb-3"><label class="form-label">Return Reasons</label>
                            <input type="text" class="form-control" id="return_reasons" placeholder="damaged,wrong_item,..."><div class="form-text">Comma-separated; drives the staff and customer reason dropdowns.</div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="return_require_approval"><label class="form-check-label" for="return_require_approval">Require manual approval for online requests</label></div>
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="return_auto_restock"><label class="form-check-label" for="return_auto_restock">Default "restock" to on when receiving goods</label></div>
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="purchase_return_require_approval"><label class="form-check-label" for="purchase_return_require_approval">Require approval for branch-manager supplier returns</label></div>
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="purchase_return_credit_note"><label class="form-check-label" for="purchase_return_credit_note">Settle supplier credits against the PO due first</label></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="return_allow_cashier_refund"><label class="form-check-label" for="return_allow_cashier_refund">Let cashiers issue refunds</label></div>
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="return_tender_lock"><label class="form-check-label" for="return_tender_lock">Only admins/managers may refund to a different tender</label></div>
                            <label class="form-label mt-1">Supplier Return Reasons</label>
                            <input type="text" class="form-control" id="supplier_return_reasons" placeholder="defective,damaged,..."><div class="form-text">Comma-separated; drives the supplier-return reason dropdown.</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Return Settings</button>
                </form>
            </div></div>
        </div>

        <div class="tab-pane fade" id="appearanceTab">
            <div class="card"><div class="card-body">
                <h5>Theme</h5>
                <div class="btn-group">
                    <button class="btn btn-outline-primary active" onclick="setTheme('light',this)">Light</button>
                    <button class="btn btn-outline-dark" onclick="setTheme('dark',this)">Dark</button>
                </div>
            </div></div>
        </div>

        <div class="tab-pane fade" id="backupTab">
            <div class="card"><div class="card-body">
                <div class="mb-3">
                    <button class="btn btn-success" onclick="createBackup()"><i class="fas fa-download"></i> Create Backup</button>
                </div>
                <div id="backupResult"></div>
                <h5 class="mt-4">Available Backups</h5>
                <table class="table table-sm">
                    <thead><tr><th>File</th><th>Size</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody id="backupTable"></tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>

<script>
async function loadSettings() {
    const res = await apiRequest('../../api/settings/index.php');
    if (!res.success) return;
    const g = res.data;

    if (g.company) Object.keys(g.company).forEach(k => { const el = document.getElementById(k); if (el) el.value = g.company[k]; });
    if (g.billing) Object.keys(g.billing).forEach(k => { const el = document.getElementById(k); if (el) el.value = g.billing[k]; });
    if (g.inventory) Object.keys(g.inventory).forEach(k => { const el = document.getElementById(k); if (el) el.value = g.inventory[k]; });
    if (g.returns) Object.keys(g.returns).forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        if (el.type === 'checkbox') el.checked = g.returns[k] === '1' || g.returns[k] === 1;
        else el.value = g.returns[k];
    });
}

function saveGroup(group, data) {
    apiRequest('../../api/settings/index.php', 'POST', { action: 'save', group, settings: data });
    alert('Settings saved!');
}

document.getElementById('companyForm').addEventListener('submit', e => { e.preventDefault(); saveGroup('company', { company_name: document.getElementById('company_name').value, company_email: document.getElementById('company_email').value, company_phone: document.getElementById('company_phone').value, company_address: document.getElementById('company_address').value, company_tax_id: document.getElementById('company_tax_id').value }); });
document.getElementById('billingForm').addEventListener('submit', e => { e.preventDefault(); saveGroup('billing', { tax_rate: document.getElementById('tax_rate').value, currency: document.getElementById('currency').value, currency_symbol: document.getElementById('currency_symbol').value, invoice_prefix: document.getElementById('invoice_prefix').value, po_prefix: document.getElementById('po_prefix').value }); });
document.getElementById('inventoryForm').addEventListener('submit', e => { e.preventDefault(); saveGroup('inventory', { low_stock_threshold: document.getElementById('low_stock_threshold').value }); });
document.getElementById('returnsForm').addEventListener('submit', e => { e.preventDefault(); saveGroup('returns', {
    return_window_days: document.getElementById('return_window_days').value,
    return_reasons: document.getElementById('return_reasons').value,
    return_require_approval: document.getElementById('return_require_approval').checked ? '1' : '0',
    return_auto_restock: document.getElementById('return_auto_restock').checked ? '1' : '0',
    return_allow_cashier_refund: document.getElementById('return_allow_cashier_refund').checked ? '1' : '0',
    return_tender_lock: document.getElementById('return_tender_lock').checked ? '1' : '0',
    purchase_return_require_approval: document.getElementById('purchase_return_require_approval').checked ? '1' : '0',
    purchase_return_credit_note: document.getElementById('purchase_return_credit_note').checked ? '1' : '0',
    supplier_return_reasons: document.getElementById('supplier_return_reasons').value,
}); });

function setTheme(theme, btn) {
    document.documentElement.setAttribute('data-theme', theme);
    document.querySelectorAll('[onclick^="setTheme"]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    apiRequest('../../api/settings/index.php', 'POST', { action: 'set_theme', theme });
}

async function createBackup() {
    document.getElementById('backupResult').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating backup...';
    const res = await apiRequest('../../api/settings/index.php', 'POST', { action: 'backup' });
    if (res.success) {
        document.getElementById('backupResult').innerHTML = `<div class="alert alert-success">Backup created: ${res.data.file}</div>`;
        loadBackups();
    } else {
        document.getElementById('backupResult').innerHTML = `<div class="alert alert-danger">${res.message}</div>`;
    }
}

async function restoreBackup(file) {
    if (!confirm('Restore database from this backup? This will overwrite current data!')) return;
    const res = await apiRequest('../../api/settings/index.php', 'POST', { action: 'restore', file });
    alert(res.message);
}

async function loadBackups() {
    const res = await apiRequest('../../api/settings/index.php', 'POST', { action: 'get_backups' });
    if (res.success) {
        document.getElementById('backupTable').innerHTML = res.data.map(b => `
            <tr>
                <td><code>${b.filename}</code></td>
                <td>${(b.size / 1024).toFixed(1)} KB</td>
                <td>${b.date}</td>
                <td><button class="btn btn-sm btn-warning" onclick="restoreBackup('${b.filename}')">Restore</button></td>
            </tr>
        `).join('');
    }
}

loadSettings();
loadBackups();
</script>

<?php require_once '../../includes/footer.php'; ?>
