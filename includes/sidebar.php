<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-store"></i></div>
            <div class="brand-text">
                <h4>EZ SIMBS</h4>
                <span class="brand-sub">Inventory & Billing</span>
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'manager', 'branch_manager', 'cashier'])): ?>
        <div class="nav-section">Store</div>
        <a href="<?= APP_URL ?>/pages/store/" class="nav-link"><i class="fas fa-store"></i><span><?= [
            'admin'          => 'Admin Dashboard',
            'manager'        => 'Manager Dashboard',
            'branch_manager' => 'Branch Manager Dashboard',
            'cashier'        => 'Cashier Dashboard',
        ][$_SESSION['user_role'] ?? ''] ?? 'Store Home' ?></span></a>
        <a href="<?= APP_URL ?>/pages/store/roles.php" class="nav-link"><i class="fas fa-user-shield"></i><span>Role Dashboards</span></a>

        <div class="nav-section">Catalog</div>
        <a href="<?= APP_URL ?>/pages/products/" class="nav-link"><i class="fas fa-box"></i><span>Products</span></a>
        <a href="<?= APP_URL ?>/pages/inventory/" class="nav-link"><i class="fas fa-boxes"></i><span>Inventory</span></a>
        <a href="<?= APP_URL ?>/pages/categories/" class="nav-link"><i class="fas fa-tags"></i><span>Categories</span></a>
        <a href="<?= APP_URL ?>/pages/brands/" class="nav-link"><i class="fas fa-trademark"></i><span>Brands</span></a>
        <a href="<?= APP_URL ?>/pages/units/" class="nav-link"><i class="fas fa-ruler"></i><span>Units</span></a>

        <div class="nav-section">People</div>
        <a href="<?= APP_URL ?>/pages/suppliers/" class="nav-link"><i class="fas fa-truck"></i><span>Suppliers</span></a>
        <a href="<?= APP_URL ?>/pages/customers/" class="nav-link"><i class="fas fa-users"></i><span>Customers</span></a>

        <div class="nav-section">Operations</div>
        <a href="<?= APP_URL ?>/pages/purchases/" class="nav-link"><i class="fas fa-shopping-cart"></i><span>Purchases</span></a>
        <?php if (($_SESSION['user_role'] ?? '') !== 'cashier'): ?>
        <a href="<?= APP_URL ?>/pages/purchase-returns/" class="nav-link"><i class="fas fa-truck-loading"></i><span>Supplier Returns</span></a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/pages/sales/" class="nav-link"><i class="fas fa-cash-register"></i><span>Sales / POS</span></a>
        <a href="<?= APP_URL ?>/pages/invoices/" class="nav-link"><i class="fas fa-file-invoice"></i><span>Invoices</span></a>
        <a href="<?= APP_URL ?>/pages/returns/" class="nav-link"><i class="fas fa-undo"></i><span>Returns</span></a>

        <?php if (($_SESSION['user_role'] ?? '') !== 'cashier'): ?>
        <div class="nav-section">Insights</div>
        <a href="<?= APP_URL ?>/pages/reports/" class="nav-link"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
        <?php endif; ?>

        <div class="nav-section">Security</div>
        <a href="<?= APP_URL ?>/pages/sessions/" class="nav-link"><i class="fas fa-shield-alt"></i><span>Active Sessions</span></a>

        <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'manager'])): ?>
        <div class="nav-section">Admin</div>
        <a href="<?= APP_URL ?>/pages/branches/" class="nav-link"><i class="fas fa-code-branch"></i><span>Branches</span></a>
        <a href="<?= APP_URL ?>/pages/users/" class="nav-link"><i class="fas fa-user-cog"></i><span>Users</span></a>
        <a href="<?= APP_URL ?>/pages/settings/" class="nav-link"><i class="fas fa-cog"></i><span>Settings</span></a>
        <?php endif; ?>

        <?php else: ?>
        <div class="nav-section">Shop</div>
        <a href="<?= APP_URL ?>/pages/customer/dashboard.php" class="nav-link"><i class="fas fa-store"></i><span>Browse Products</span></a>
        <a href="<?= APP_URL ?>/pages/customer/wishlist.php" class="nav-link"><i class="fas fa-heart"></i><span>My Wishlist</span></a>
        <a href="<?= APP_URL ?>/pages/customer/cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i><span>My Cart</span></a>

        <div class="nav-section">Orders</div>
        <a href="<?= APP_URL ?>/pages/customer/orders.php" class="nav-link"><i class="fas fa-box"></i><span>Order History</span></a>
        <a href="<?= APP_URL ?>/pages/customer/returns.php" class="nav-link"><i class="fas fa-undo"></i><span>My Returns</span></a>

        <div class="nav-section">Account</div>
        <a href="<?= APP_URL ?>/pages/users/profile.php" class="nav-link"><i class="fas fa-user"></i><span>My Profile</span></a>
        <a href="<?= APP_URL ?>/pages/sessions/" class="nav-link"><i class="fas fa-shield-alt"></i><span>Active Sessions</span></a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="side-user">
            <span class="profile-logo">
                <?php if (!empty($currentUser['avatar'])): ?>
                    <img src="<?= APP_URL . '/' . $currentUser['avatar'] ?>" alt="Avatar">
                <?php else: ?>
                    <?= strtoupper(substr(sanitize($currentUser['name'] ?? 'U'), 0, 1)) ?>
                <?php endif; ?>
            </span>
            <div class="side-user-meta">
                <strong><?= sanitize($currentUser['name'] ?? 'User') ?></strong>
                <small><?= ucfirst(sanitize($currentUser['role_name'] ?? '')) ?></small>
            </div>
        </div>
        <a href="<?= APP_URL ?>/pages/auth/logout.php" class="btn btn-sm btn-danger w-100 mt-2" title="Logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>