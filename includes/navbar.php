<nav class="navbar">
    <div class="navbar-left">
        <button class="btn btn-sm" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </div>
    <div class="navbar-right">
        <a href="<?= APP_URL ?>/pages/notifications/" class="btn btn-sm position-relative notif-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notif-badge d-none" id="notifBadge">0</span>
        </a>
        <div class="dropdown">
            <button class="btn btn-sm dropdown-toggle profile-btn" data-bs-toggle="dropdown">
                <span class="profile-logo">
                    <?php if (!empty($currentUser['avatar'])): ?>
                        <img src="<?= APP_URL . '/' . $currentUser['avatar'] ?>" alt="Avatar">
                    <?php else: ?>
                        <?= strtoupper(substr(sanitize($currentUser['name'] ?? 'U'), 0, 1)) ?>
                    <?php endif; ?>
                </span>
                <?= sanitize($currentUser['name'] ?? 'User') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= APP_URL ?>/pages/users/profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="<?= APP_URL ?>/pages/sessions/"><i class="fas fa-shield-alt me-1"></i>Active Sessions</a></li>
            </ul>
        </div>
    </div>
</nav>