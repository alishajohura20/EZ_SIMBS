<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$pageTitle = 'Active Sessions';
$isAdmin = in_array($_SESSION['user_role'] ?? '', ['admin', 'manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Active Sessions</h4>
        <button class="btn btn-warning" onclick="terminateOthers()">
            <i class="fas fa-arrows-rotate me-1"></i> End all other sessions
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-stat">
                <h5>My Active Sessions</h5>
                <p class="mb-1 fs-3 fw-bold" id="myCount">–</p>
                <p class="mb-0 small text-muted">Devices signed into your account</p>
            </div>
        </div>
        <?php if ($isAdmin): ?>
        <div class="col-md-3 mb-3">
            <div class="card card-stat">
                <h5>System Sessions</h5>
                <p class="mb-1 fs-3 fw-bold" id="sysCount">–</p>
                <p class="mb-0 small text-muted">Total sessions across all users</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stat">
                <h5>Active Users</h5>
                <p class="mb-1 fs-3 fw-bold" id="activeUsers">–</p>
                <p class="mb-0 small text-muted">Users currently signed in</p>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-md-3 mb-3">
            <div class="card card-stat">
                <h5>Session Lifetime</h5>
                <p class="mb-1 fs-3 fw-bold">7 days</p>
                <p class="mb-0 small text-muted">30 days with "Remember me"</p>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Sessions on your account</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Device</th><th>IP Address</th><th>Signed in</th><th>Last activity</th><th>Expires</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody id="mySessions"></tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">All active sessions (system-wide)</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search user..." style="max-width:220px">
                <select class="form-select form-select-sm" id="roleFilter" style="max-width:170px">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="branch_manager">Branch Manager</option>
                    <option value="cashier">Cashier</option>
                    <option value="customer">Customer</option>
                </select>
                <button class="btn btn-sm btn-outline-primary" onclick="loadAllSessions()">Filter</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>User</th><th>Role</th><th>Device</th><th>IP</th><th>Last activity</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="allSessions"></tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;

function fmtDate(d) {
    if (!d) return '-';
    const dt = new Date(d);
    const now = new Date();
    const diff = (now - dt) / 1000;
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' h ago';
    return dt.toLocaleString();
}

function renderMySessions(list) {
    document.getElementById('myCount').textContent = list.length;
    document.getElementById('mySessions').innerHTML = list.map(s => `
        <tr>
            <td>
                <i class="fas fa-${s.device.includes('Mobile') ? 'mobile-alt' : s.device.includes('Tablet') ? 'tablet-alt' : 'laptop'} me-2 text-muted"></i>
                <strong>${s.device || 'Unknown device'}</strong>
            </td>
            <td><code>${s.ip_address || '-'}</code></td>
            <td>${new Date(s.created_at).toLocaleString()}</td>
            <td>${fmtDate(s.last_activity)}</td>
            <td>${new Date(s.expires_at).toLocaleString()}</td>
            <td>
                ${s.is_current
                    ? '<span class="badge bg-success">This device</span>'
                    : '<span class="badge bg-secondary">Other device</span>'}
            </td>
            <td class="text-end">
                ${s.is_current
                    ? '<span class="text-muted small">(sign out via menu)</span>'
                    : `<button class="btn btn-sm btn-outline-danger" onclick="terminateSession(${s.id})"><i class="fas fa-sign-out-alt"></i> End</button>`}
            </td>
        </tr>
    `).join('') || '<tr><td colspan="7" class="text-center text-muted py-4">No active sessions</td></tr>';
}

function renderAllSessions(list) {
    document.getElementById('sysCount').textContent = list.length;
    const users = new Set(list.map(s => s.user_id));
    document.getElementById('activeUsers').textContent = users.size;

    document.getElementById('allSessions').innerHTML = list.map(s => `
        <tr>
            <td><strong>${s.user_name}</strong><br><small class="text-muted">${s.email}</small></td>
            <td><span class="badge bg-primary">${s.role_name}</span></td>
            <td>${s.device || 'Unknown device'}</td>
            <td><code>${s.ip_address || '-'}</code></td>
            <td>${fmtDate(s.last_activity)}</td>
            <td>${s.is_current ? '<span class="badge bg-success">You</span>' : '<span class="badge bg-secondary">Active</span>'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger me-1" title="End this session" onclick="terminateSession(${s.id})"><i class="fas fa-sign-out-alt"></i></button>
                <button class="btn btn-sm btn-outline-dark" title="End all sessions for this user" onclick="terminateAllUser(${s.user_id})"><i class="fas fa-user-slash"></i></button>
            </td>
        </tr>
    `).join('') || '<tr><td colspan="7" class="text-center text-muted py-4">No active sessions</td></tr>';
}

async function loadMySessions() {
    const res = await apiRequest('../../api/sessions/index.php');
    if (res.success) renderMySessions(res.data.sessions);
}

async function loadAllSessions() {
    if (!isAdmin) return;
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const res = await apiRequest(`../../api/sessions/index.php?all=1&search=${encodeURIComponent(search)}&role=${encodeURIComponent(role)}`);
    if (res.success) renderAllSessions(res.data.sessions);
}

async function terminateSession(id) {
    if (!confirm('End this login session? The device will be signed out on its next action.')) return;
    const res = await apiRequest('../../api/sessions/index.php', 'POST', { action: 'terminate', id });
    if (res.success) {
        if (res.data.logged_out) {
            window.location.href = '../../pages/auth/login.php';
            return;
        }
        loadMySessions();
        if (isAdmin) loadAllSessions();
    }
}

async function terminateOthers() {
    if (!confirm('End all sessions other than this device?')) return;
    const res = await apiRequest('../../api/sessions/index.php', 'POST', { action: 'terminate_others' });
    if (res.success) {
        loadMySessions();
        if (isAdmin) loadAllSessions();
    }
}

async function terminateAllUser(userId) {
    if (!confirm('End ALL sessions for this user? They will be signed out of every device.')) return;
    const res = await apiRequest('../../api/sessions/index.php', 'POST', { action: 'terminate_all', user_id: userId });
    if (res.success) {
        loadAllSessions();
    }
}

loadMySessions();
if (isAdmin) loadAllSessions();
</script>

<?php require_once '../../includes/footer.php'; ?>