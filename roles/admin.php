<?php
$pageTitle = 'Admin Dashboard';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['admin']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$store   = new Store();
$storeInfo = $store->getForUser($_SESSION['user_id']) ?? [];
$stats   = $storeInfo ? $store->getStats($storeInfo['id']) : [];
$team    = $storeInfo ? $store->getTeamMembers($storeInfo['id']) : [];
$roleMap = ['admin'=>'Admin','manager'=>'Manager','branch_manager'=>'Branch Manager','cashier'=>'Cashier','customer'=>'Customer'];
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h4 class="mb-0" style="font-weight:700"><i class="fas fa-crown me-2 text-warning"></i>Store Owner Dashboard</h4>
        <div class="d-flex gap-2">
            <a href="../store/" class="btn btn-sm btn-outline-primary"><i class="fas fa-home me-1"></i> Store Home</a>
        </div>
    </div>

    <?php if (!empty($storeInfo)): ?>
    <div class="row mb-4 align-items-stretch">
        <div class="col-md-8 mb-3">
            <div class="card h-100 border-0" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:18px;color:#fff">
                <div class="card-body d-flex flex-column justify-content-between" style="gap:14px">
                    <div>
                        <span class="badge mb-2" style="background:rgba(255,255,255,.2)"><i class="fas fa-store me-1"></i> <?= strtoupper($storeInfo['plan'] ?? 'free') ?> PLAN</span>
                        <h2 class="mb-1" style="font-weight:800"><?= sanitize($storeInfo['name']) ?></h2>
                        <p class="mb-0" style="opacity:.9"><?= sanitize($storeInfo['address']) ?> &nbsp;•&nbsp; <?= sanitize($storeInfo['phone']) ?></p>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <div><small style="opacity:.85">Owner</small><br><strong><?= sanitize($storeInfo['owner_name']) ?></strong></div>
                        <div><small style="opacity:.85">Email</small><br><strong><?= sanitize($storeInfo['email']) ?></strong></div>
                        <div><small style="opacity:.85">Since</small><br><strong><?= date('M Y', strtotime($storeInfo['created_at'])) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0" style="border-radius:18px">
                <div class="card-body d-flex flex-column justify-content-center text-center" style="gap:6px">
                    <div style="font-size:2.6rem"><i class="fas fa-tachometer-alt" style="color:var(--primary)"></i></div>
                    <h5 class="mb-0" style="font-weight:800"><?= (int)($stats['total_staff'] ?? 0) ?></h5>
                    <p class="text-muted mb-0">Team Members</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <?php foreach (array_slice($roleMap, 0, 4) as $rk => $rl): ?>
                            <?php if (!empty($stats['team_counts'][$rk])): ?>
                                <span class="badge rounded-pill" style="background:var(--primary-soft, #eef2ff);color:var(--primary,#4f46e5)"><?= $rl ?>: <?= (int)$stats['team_counts'][$rk] ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)($stats['today_sales'] ?? 0), 0) ?></h3><p>Today's Sales</p></div><div class="text-primary"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)($stats['monthly_sales'] ?? 0), 0) ?></h3><p>Monthly Sales</p></div><div class="text-success"><i class="fas fa-chart-line fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)($stats['total_products'] ?? 0) ?></h3><p>Products</p></div><div class="text-secondary"><i class="fas fa-box fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)($stats['branch_count'] ?? 0) ?></h3><p>Branches</p></div><div class="text-info"><i class="fas fa-code-branch fa-3x opacity-25"></i></div></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card"><div class="card-header"><h5>Quick Actions</h5></div>
                <div class="card-body d-grid gap-2">
                    <a href="../store/roles.php" class="btn btn-primary"><i class="fas fa-user-shield me-2"></i> Manage Role Dashboards</a>
                    <a href="../users/" class="btn btn-outline-primary"><i class="fas fa-user-cog me-2"></i> Team &amp; Users</a>
                    <a href="../branches/" class="btn btn-outline-primary"><i class="fas fa-code-branch me-2"></i> Branches</a>
                    <a href="../reports/" class="btn btn-outline-primary"><i class="fas fa-chart-bar me-2"></i> Reports</a>
                    <a href="../settings/" class="btn btn-outline-primary"><i class="fas fa-cog me-2"></i> Settings</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card"><div class="card-header"><h5><i class="fas fa-users me-2"></i>Store Team</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead><tr><th>Member</th><th>Role</th><th>Branch</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if (empty($team)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No team members yet.</td></tr>
                        <?php else: foreach ($team as $m): ?>
                            <tr>
                                <td>
                                    <span class="profile-logo" style="width:32px;height:32px;font-size:.8rem;display:inline-flex;align-items:center;justify-content:center;margin-right:8px">
                                        <?= strtoupper(substr($m['name'],0,1)) ?>
                                    </span>
                                    <strong><?= sanitize($m['name']) ?></strong><br><small class="text-muted"><?= sanitize($m['email']) ?></small>
                                </td>
                                <td><span class="badge" style="background:var(--primary-soft,#eef2ff);color:var(--primary,#4f46e5)"><?= $roleMap[$m['role_name']] ?? ucfirst($m['role_name']) ?></span></td>
                                <td><?= sanitize($m['branch_name'] ?? '—') ?></td>
                                <td><span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'secondary' ?>"><?= $m['status'] ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>