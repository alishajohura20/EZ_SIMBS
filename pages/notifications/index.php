<?php
$pageTitle = 'Notifications';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Notifications</h4>
        <div>
            <button class="btn btn-outline-primary btn-sm" onclick="markAllRead()">Mark All Read</button>
            <button class="btn btn-outline-danger btn-sm" onclick="deleteAll()">Delete All</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="notificationList"></div>
        </div>
    </div>
</div>

<script>
const typeIcons = { info: 'info', warning: 'warning', danger: 'danger', success: 'success' };
const typeFaIcons = { info: 'fas fa-info-circle', warning: 'fas fa-exclamation-triangle', danger: 'fas fa-times-circle', success: 'fas fa-check-circle' };

async function loadNotifications() {
    const res = await apiRequest('../../api/notifications/index.php');
    if (!res.success) return;

    const notifs = res.data.notifications;
    if (notifs.length === 0) {
        document.getElementById('notificationList').innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-3x mb-3"></i><p>No notifications</p></div>';
        return;
    }

    document.getElementById('notificationList').innerHTML = notifs.map(n => `
        <div class="d-flex align-items-start p-3 border-bottom ${!n.is_read ? 'bg-light' : ''}" style="cursor:pointer" onclick="markRead(${n.id}, '${n.link || ''}')">
            <i class="${typeFaIcons[n.type] || 'fas fa-bell'} text-${typeIcons[n.type] || 'secondary'} fa-lg me-3 mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">${n.title} ${!n.is_read ? '<span class="badge bg-primary">New</span>' : ''}</h6>
                <p class="mb-1 text-muted">${n.message}</p>
                <small class="text-muted">${new Date(n.created_at).toLocaleString()}</small>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();deleteNotif(${n.id})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
}

async function markRead(id, link) {
    await apiRequest('../../api/notifications/index.php', 'POST', { action: 'read', id });
    if (link) window.location.href = link;
    loadNotifications();
}

async function markAllRead() {
    await apiRequest('../../api/notifications/index.php', 'POST', { action: 'read_all' });
    loadNotifications();
}

async function deleteNotif(id) {
    await apiRequest('../../api/notifications/index.php', 'POST', { action: 'delete', id });
    loadNotifications();
}

async function deleteAll() {
    if (!confirm('Delete all notifications?')) return;
    await apiRequest('../../api/notifications/index.php', 'POST', { action: 'delete_all' });
    loadNotifications();
}

loadNotifications();
setInterval(loadNotifications, 30000);
</script>

<?php require_once '../../includes/footer.php'; ?>
