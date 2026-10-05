<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager']);
$pageTitle = 'Login History';
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <h4 class="mb-4">Login History</h4>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>User</th><th>Action</th><th>IP Address</th><th>Browser</th><th>Time</th></tr>
                    </thead>
                    <tbody id="historyTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(async () => {
    const res = await apiRequest('../../api/activity-logs/index.php?action=login,logout&limit=100');
    if (res.success) {
        document.getElementById('historyTable').innerHTML = res.data.map(l => `
            <tr>
                <td>${l.user_name || 'Unknown'}</td>
                <td><span class="badge bg-${l.action==='login'?'success':'secondary'}">${l.action}</span></td>
                <td>${l.ip_address}</td>
                <td>${l.browser?.substring(0, 50) || '-'}</td>
                <td>${new Date(l.created_at).toLocaleString()}</td>
            </tr>
        `).join('');
    }
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
