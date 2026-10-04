<?php
$pageTitle = 'Activity Log';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
requireRole(['admin', 'manager']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>
    <h4 class="mb-4"><i class="fas fa-history"></i> Activity Log</h4>

    <div class="card mb-4"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-2"><input type="text" class="form-control" id="searchAction" placeholder="Action..."></div>
            <div class="col-md-2"><input type="text" class="form-control" id="searchResource" placeholder="Resource..."></div>
            <div class="col-md-2"><input type="date" class="form-control" id="dateFrom"></div>
            <div class="col-md-2"><input type="date" class="form-control" id="dateTo"></div>
            <div class="col-md-2"><button class="btn btn-primary" onclick="loadLogs()">Filter</button></div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Resource</th><th>ID</th><th>IP</th></tr></thead>
                <tbody id="logTable"></tbody>
            </table>
        </div>
        <div id="pagination"></div>
    </div></div>
</div>

<script>
async function loadLogs(page=1) {
    const params = new URLSearchParams({ page, action: document.getElementById('searchAction').value, resource: document.getElementById('searchResource').value, date_from: document.getElementById('dateFrom').value, date_to: document.getElementById('dateTo').value });
    const res = await apiRequest(`../../api/activity-logs/index.php?${params}`);
    if (!res.success) return;

    document.getElementById('logTable').innerHTML = res.data.logs.map(l => `
        <tr>
            <td><small>${new Date(l.created_at).toLocaleString()}</small></td>
            <td>${l.user_name || 'System'}</td>
            <td><span class="badge bg-secondary">${l.action}</span></td>
            <td>${l.resource || '-'}</td>
            <td>${l.resource_id || '-'}</td>
            <td><small>${l.ip_address || '-'}</small></td>
        </tr>
    `).join('');

    renderPagination(res.data.pagination, loadLogs);
}
loadLogs();
</script>

<?php require_once '../../includes/footer.php'; ?>
