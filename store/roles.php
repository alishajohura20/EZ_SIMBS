<?php
$pageTitle = 'Role Dashboards';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$myRole = $_SESSION['user_role'] ?? '';
$backLabel = [
    'admin'          => 'Admin Dashboard',
    'manager'        => 'Manager Dashboard',
    'branch_manager' => 'Branch Manager Dashboard',
    'cashier'        => 'Cashier Dashboard',
][$myRole] ?? 'Store Home';

$roles = [
    'admin' => [
        'label' => 'Admin', 'sub' => 'Store Owner', 'icon' => 'fa-crown', 'color' => '#f59e0b',
        'desc' => 'Full ownership of the store: manage team, branches, products, settings and every module. Can access every role dashboard.',
        'caps' => ['Manage users & roles', 'Configure store & branches', 'Full system access', 'View all role dashboards'],
        'url' => '../roles/admin.php',
        'access' => ['admin'],
    ],
    'manager' => [
        'label' => 'Manager', 'sub' => 'Operations & Analytics', 'icon' => 'fa-user-tie', 'color' => '#6366f1',
        'desc' => 'Oversees operations: sales analytics, purchases, suppliers and customers. Reports-driven decision making.',
        'caps' => ['Sales & profit reports', 'Purchase management', 'Supplier management', 'Customer management'],
        'url' => '../roles/manager.php',
        'access' => ['admin', 'manager'],
    ],
    'branch_manager' => [
        'label' => 'Branch Manager', 'sub' => 'Branch Operations', 'icon' => 'fa-store-alt', 'color' => '#0ea5e9',
        'desc' => 'Runs a single branch: branch inventory, stock movement, branch sales and low-stock alerts.',
        'caps' => ['Branch-scoped dashboard', 'Stock in/out & transfers', 'Low stock alerts', 'Branch staff'],
        'url' => '../roles/branch-manager.php',
        'access' => ['admin', 'manager', 'branch_manager'],
    ],
    'cashier' => [
        'label' => 'Branch Cashier', 'sub' => 'POS & Billing', 'icon' => 'fa-cash-register', 'color' => '#10b981',
        'desc' => 'Front-of-store billing: ring up sales, print invoices, scan barcodes and look up customers.',
        'caps' => ['Point-of-sale (POS)', 'Instant invoices & receipts', 'Customer lookup', 'Daily sales view'],
        'url' => '../roles/cashier.php',
        'access' => ['admin', 'manager', 'branch_manager', 'cashier'],
    ],
    'customer' => [
        'label' => 'Customer', 'sub' => 'Online Storefront', 'icon' => 'fa-shopping-cart', 'color' => '#ec4899',
        'desc' => 'Your online shoppers: browse the web catalog, add to cart, wishlist, checkout and track orders.',
        'caps' => ['Browse online catalog', 'Cart & checkout', 'Wishlist', 'Order history'],
        'url' => '../customer/dashboard.php',
        'access' => ['admin', 'manager', 'branch_manager', 'cashier', 'customer'],
    ],
];

$matrix = [
    'admin'           => ['admin', 'manager', 'branch_manager', 'cashier', 'customer'],
    'manager'         => ['manager', 'branch_manager', 'cashier', 'customer'],
    'branch_manager'  => ['branch_manager', 'cashier', 'customer'],
    'cashier'         => ['cashier', 'customer'],
    'customer'        => ['customer'],
];
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
        <h4 class="mb-0" style="font-weight:700"><i class="fas fa-user-shield me-2" style="color:var(--primary)"></i>Role-Based Dashboards</h4>
        <a href="index.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> <?= $backLabel ?></a>
    </div>
    <p class="text-muted mb-4">Five store roles. Each logs into a dashboard tuned to what they do. Open any dashboard you are permitted to access:</p>

    <div class="row g-4">
        <?php foreach ($roles as $key => $role):
            $color = $role['color'];
            $open  = in_array($key, $matrix[$myRole] ?? []);
            if ($key === 'admin' && $myRole === 'admin') $open = true; ?>
            <div class="col-md-6 col-xl-4">
                <div class="card role-dash-card h-100 <?= $open ? '' : 'role-locked' ?>" style="border-top:5px solid <?= $color ?>;border-radius:18px">
                    <div class="card-body d-flex flex-column" style="gap:14px">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="role-chip" style="background:<?= $color ?>22;color:<?= $color ?>"><i class="fas <?= $role['icon'] ?>"></i></span>
                                <div>
                                    <h5 class="mb-0" style="font-weight:800"><?= $role['label'] ?></h5>
                                    <small class="text-muted"><?= $role['sub'] ?></small>
                                </div>
                            </div>
                            <?php if (!$open): ?>
                                <span class="badge bg-secondary py-2 px-3"><i class="fas fa-lock me-1"></i>Locked</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-0"><?= $role['desc'] ?></p>
                        <ul class="list-unstyled mb-0 d-grid" style="gap:6px">
                            <?php foreach ($role['caps'] as $cap): ?>
                                <li><i class="fas fa-check-circle me-2" style="color:<?= $color ?>"></i><?= $cap ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-auto pt-2">
                            <?php if ($open): ?>
                                <a href="<?= $role['url'] ?>" class="btn btn-<?= $key === 'admin' ? 'warning' : 'primary' ?> w-100"><i class="fas fa-door-open me-2"></i> Open <?= $role['label'] ?> Dashboard</a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled><i class="fas fa-lock me-2"></i> Requires higher access</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card mt-4"><div class="card-header"><h5><i class="fas fa-info-circle me-2"></i>Access Rules</h5></div>
        <div class="card-body">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Your Role</th><th>Dashboards You Can Open</th></tr></thead>
                <tbody>
                    <?php foreach ($matrix as $rk => $allowed): ?>
                        <tr>
                            <td><span class="badge" style="background:<?= ['admin'=>'#f59e0b','manager'=>'#6366f1','branch_manager'=>'#0ea5e9','cashier'=>'#10b981','customer'=>'#ec4899'][$rk] ?>22;color:<?= ['admin'=>'#f59e0b','manager'=>'#6366f1','branch_manager'=>'#0ea5e9','cashier'=>'#10b981','customer'=>'#ec4899'][$rk] ?>"><?= ucwords(str_replace('_',' ', $rk)) ?></span></td>
                            <td><?= implode(' → ', array_map('ucwords', str_replace('_',' ',$allowed))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>