<?php
$pageTitle = 'Store Home';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// ── Guest → role-based staff auth gate ────────────────────────────
if (!isLoggedIn()) {
    require_once '../../includes/header.php';
    ?>
<style>
.store-auth{width:100%;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:clamp(20px,4vw,48px);background:var(--body-bg);position:relative}
.store-auth::before{content:'';position:absolute;inset:0;background:radial-gradient(900px 420px at 12% -10%,rgba(99,102,241,.16),transparent 60%),radial-gradient(820px 440px at 108% 18%,rgba(217,70,239,.13),transparent 60%);pointer-events:none}
.store-auth-card{position:relative;width:100%;max-width:1000px;background:var(--card-bg);border:1px solid var(--border-color);border-radius:28px;box-shadow:0 24px 70px -30px rgba(15,23,42,.35);padding:clamp(24px,3.6vw,52px)}
.store-auth-top{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:32px}
.store-auth-brand{display:inline-flex;align-items:center;gap:12px;text-decoration:none;color:var(--text-color)}
.store-auth-brand .brand-chip{width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.18rem;box-shadow:0 8px 20px -8px rgba(99,102,241,.7)}
.store-auth-brand strong{display:block;font-size:1.12rem;font-weight:800;line-height:1.1}
.store-auth-brand small{display:block;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:var(--text-muted,#64748b);font-weight:600;margin-top:2px}
.store-auth-back{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:50px;border:1.5px solid var(--border-color);background:var(--body-bg);color:var(--text-color);font-size:.9rem;font-weight:700;text-decoration:none;transition:all .2s}
.store-auth-back:hover{border-color:var(--primary);color:var(--primary);transform:translateX(-3px)}
.store-auth-body{text-align:center;max-width:680px;margin:0 auto}
.store-auth-eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--primary);font-size:.72rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;margin-bottom:12px}
.store-auth-body h1{font-weight:800;font-size:clamp(1.6rem,3vw,2.25rem);color:var(--text-color);margin-bottom:10px}
.store-auth-sub{color:var(--text-muted,#64748b);font-size:.97rem;line-height:1.7;margin-bottom:26px}
.role-pick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.role-pick{background:var(--body-bg);border:1.5px solid var(--border-color);border-radius:18px;padding:20px 16px;text-align:center;cursor:pointer;transition:all .2s;position:relative;color:var(--text-color)}
.role-pick:hover{transform:translateY(-3px);box-shadow:0 14px 30px -16px rgba(79,70,229,.38);border-color:var(--primary)}
.role-pick.active{border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.14),0 14px 30px -16px rgba(79,70,229,.38)}
.role-pick-icon{width:52px;height:52px;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;margin-bottom:12px;box-shadow:0 8px 18px -8px rgba(0,0,0,.35)}
.role-pick strong{display:block;font-size:.95rem;font-weight:800;color:var(--text-color);margin-bottom:5px}
.role-pick small{display:block;font-size:.74rem;color:var(--text-muted,#64748b);line-height:1.5;min-height:2.4em}
.role-pick.active::after{content:'✓';position:absolute;top:10px;right:12px;width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;font-size:.72rem;display:flex;align-items:center;justify-content:center;font-weight:800}
.role-hint{min-height:24px;font-size:.9rem;color:var(--text-muted,#64748b);margin-bottom:18px}
.role-hint strong{font-weight:800}
.store-auth-form{max-width:460px;margin:0 auto;text-align:left}
.store-auth-form .auth-input-group{margin-bottom:22px}
.store-auth-form .auth-input-group .form-label{font-size:.92rem;margin-bottom:8px;opacity:.85}
.store-auth-form .auth-input-group .form-control{height:52px;border-radius:14px;font-size:1rem;padding-left:44px}
.store-auth-form .auth-input-group .auth-input-wrap > i{left:16px;top:50%;transform:translateY(-50%);font-size:1rem;opacity:.6}
.store-auth-form .auth-input-wrap .password-toggle{font-size:1rem;right:12px}
.store-auth-form .auth-btn{min-height:52px;border-radius:14px;font-size:1.02rem;font-weight:700}
.store-auth .auth-alert{font-size:.92rem;padding:14px 18px;border-radius:14px}
.store-auth-form .auth-divider{font-size:1rem;margin:26px 0 18px}
.store-auth-foot{margin-top:4px;text-align:center;color:var(--text-muted,#64748b);font-size:.9rem}
.store-auth-foot a{color:var(--primary);font-weight:700;text-decoration:none}
@media(max-width:991.98px){.role-pick-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:575.98px){.role-pick-grid{grid-template-columns:1fr}.role-pick small{min-height:0}}
</style>

<div class="store-auth">
    <div class="store-auth-card">
        <div class="store-auth-top">
            <a href="<?= APP_URL ?>" class="store-auth-brand">
                <span class="brand-chip"><i class="fas fa-store"></i></span>
                <span><strong><?= APP_NAME ?></strong><small>Inventory · Billing · POS</small></span>
            </a>
            <a href="<?= APP_URL ?>" class="store-auth-back"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
        </div>

        <div class="store-auth-body">
            <span class="store-auth-eyebrow"><i class="fas fa-user-shield"></i> Staff Access · Role-Based Sign In</span>
            <h1>Sign in to manage your store</h1>
            <p class="store-auth-sub">Pick your role to unlock the right workspace — Admin, Manager, Branch Manager or Cashier. Customers sign in through the storefront instead.</p>

            <div class="role-pick-grid">
                <button type="button" class="role-pick" data-role="admin" data-label="Admin / Owner">
                    <span class="role-pick-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="fas fa-crown"></i></span>
                    <strong>Admin / Owner</strong>
                    <small>Store ownership, settings &amp; full control</small>
                </button>
                <button type="button" class="role-pick" data-role="manager" data-label="Manager">
                    <span class="role-pick-icon" style="background:linear-gradient(135deg,#6366f1,#4338ca)"><i class="fas fa-user-tie"></i></span>
                    <strong>Manager</strong>
                    <small>Purchases, suppliers, reports &amp; analytics</small>
                </button>
                <button type="button" class="role-pick" data-role="branch_manager" data-label="Branch Manager">
                    <span class="role-pick-icon" style="background:linear-gradient(135deg,#0ea5e9,#0369a1)"><i class="fas fa-store-alt"></i></span>
                    <strong>Branch Manager</strong>
                    <small>Branch inventory, sales &amp; staff</small>
                </button>
                <button type="button" class="role-pick" data-role="cashier" data-label="Cashier">
                    <span class="role-pick-icon" style="background:linear-gradient(135deg,#10b981,#047857)"><i class="fas fa-cash-register"></i></span>
                    <strong>Cashier</strong>
                    <small>POS, billing &amp; invoicing</small>
                </button>
            </div>

            <div class="role-hint" id="roleHint"><i class="fas fa-info-circle me-1"></i>Select your role to continue — or just sign in and we'll detect it automatically.</div>
            <div id="authAlert"></div>
        </div>

        <form class="store-auth-form" id="staffLoginForm" autocomplete="off" onsubmit="return staffLogin(event)">
            <div class="auth-input-group">
                <label class="form-label">Work Email</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" id="staffEmail" placeholder="you@yourstore.com" required autocomplete="email">
                </div>
            </div>
            <div class="auth-input-group">
                <label class="form-label">Password</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" id="staffPassword" placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="toggleStaffPassword(this)"><i class="far fa-eye"></i></button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="staffRemember">
                    <label class="form-check-label small" for="staffRemember">Remember me</label>
                </div>
                <a href="<?= APP_URL ?>/pages/auth/forgot-password.php" class="small" style="color:var(--primary);font-weight:600">Forgot password?</a>
            </div>
            <button type="submit" class="btn auth-btn" id="submitBtn"><i class="fas fa-sign-in-alt me-2"></i><span id="submitLabel">Sign In</span></button>
        </form>

        <div class="auth-divider">Not part of the management team?</div>
        <div class="store-auth-foot">
            <a href="<?= APP_URL ?>/pages/auth/login.php" class="me-2"><i class="fas fa-shopping-cart me-1"></i>Customer Sign In</a>
            <span class="mx-1">·</span>
            <a href="<?= APP_URL ?>/pages/auth/register-store.php" class="mx-2"><i class="fas fa-store me-1"></i>Create a Free Store</a>
        </div>
    </div>
</div>

<script>
function toggleStaffPassword(btn) {
    const input = document.getElementById('staffPassword');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('i').className = isPassword ? 'far fa-eye-slash' : 'far fa-eye';
}
let selectedRole = null;
document.querySelectorAll('.role-pick').forEach(function (el) {
    el.addEventListener('click', function () {
        document.querySelectorAll('.role-pick').forEach(function (x) { x.classList.remove('active'); });
        el.classList.add('active');
        selectedRole = el.dataset.role;
        document.getElementById('roleHint').innerHTML = '<i class="fas fa-check-circle me-1" style="color:var(--primary)"></i>Signing in as <strong>' + el.dataset.label + '</strong>';
        document.getElementById('submitLabel').textContent = 'Sign in as ' + el.dataset.label;
    });
});
async function staffLogin(e) {
    e.preventDefault();
    const alertBox = document.getElementById('authAlert');
    alertBox.innerHTML = '';
    const email = document.getElementById('staffEmail').value.trim();
    const password = document.getElementById('staffPassword').value;
    if (!email || !password) {
        alertBox.innerHTML = '<div class="alert alert-danger auth-alert mb-3"><i class="fas fa-exclamation-circle me-2"></i>Please enter your email and password.</div>';
        return false;
    }
    const btn = document.getElementById('submitBtn');
    const oldHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';
    try {
        const res = await fetch('<?= APP_URL ?>/api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, password: password, remember: document.getElementById('staffRemember').checked, role: selectedRole })
        });
        const json = await res.json();
        if (json.success) {
            window.location.href = '<?= APP_URL ?>/pages/store/';
        } else {
            alertBox.innerHTML = '<div class="alert alert-danger auth-alert mb-3"><i class="fas fa-exclamation-circle me-2"></i>' + (json.message || 'Sign in failed.') + '</div>';
        }
    } catch (err) {
        alertBox.innerHTML = '<div class="alert alert-danger auth-alert mb-3"><i class="fas fa-exclamation-circle me-2"></i>Network error. Please try again.</div>';
    }
    btn.disabled = false;
    btn.innerHTML = oldHtml;
    return false;
}
</script>
    <?php
    require_once '../../includes/footer.php';
    exit;
}

// Customers don't belong on the staff hub
if (($_SESSION['user_role'] ?? '') === 'customer') {
    redirect(APP_URL . '/pages/customer/dashboard.php');
}

requireRole(['admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$store = new Store();
$storeInfo = $store->getForUser($_SESSION['user_id']) ?? [];
$stats     = $storeInfo ? $store->getStats($storeInfo['id']) : [];
$branchPerformance = $storeInfo ? $store->getBranchPerformance($storeInfo['id']) : [];
$topProduct = $storeInfo ? $store->getTopProduct($storeInfo['id']) : null;
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <?php if (!empty($storeInfo)): ?>
    <div class="row mb-4 align-items-stretch">
        <div class="col-lg-12 mb-3">
            <div class="card h-100 border-0" style="background:linear-gradient(135deg,#1e1b4b,#4f46e5 55%,#9333ea);border-radius:18px;color:#fff;overflow:hidden;position:relative">
                <div style="position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.08)"></div>
                <div style="position:absolute;right:40px;bottom:-70px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.07)"></div>
                <div class="card-body" style="position:relative;z-index:1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <span class="badge mb-2" style="background:rgba(255,255,255,.22)"><i class="fas fa-store me-1"></i> <?= strtoupper($storeInfo['plan'] ?? 'free') ?> PLAN</span>
                            <h2 class="mb-1" style="font-weight:800"><?= sanitize($storeInfo['name']) ?></h2>
                            <p class="mb-0" style="opacity:.9"><i class="fas fa-map-marker-alt me-1"></i><?= sanitize($storeInfo['address'] ?: 'Address not set') ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i><?= ucfirst($storeInfo['status'] ?? 'active') ?></span>
                        </div>
                    </div>
                    <div class="row g-2" style="max-width:560px">
                        <div class="col-sm-4"><div class="p-2 rounded-3" style="background:rgba(255,255,255,.12)"><small style="opacity:.85">Owner</small><br><strong><?= sanitize($storeInfo['owner_name']) ?></strong></div></div>
                        <div class="col-sm-4"><div class="p-2 rounded-3" style="background:rgba(255,255,255,.12)"><small style="opacity:.85">Email</small><br><strong style="font-size:.9rem"><?= sanitize($storeInfo['email']) ?></strong></div></div>
                        <div class="col-sm-4"><div class="p-2 rounded-3" style="background:rgba(255,255,255,.12)"><small style="opacity:.85">Member Since</small><br><strong><?= date('M Y', strtotime($storeInfo['created_at'])) ?></strong></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)($stats['today_sales'] ?? 0), 0) ?></h3><p>Today's Sales</p></div><div class="text-primary"><i class="fas fa-dollar-sign fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3>$<?= number_format((float)($stats['monthly_sales'] ?? 0), 0) ?></h3><p>Monthly Sales</p></div><div class="text-success"><i class="fas fa-chart-line fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)($stats['branch_count'] ?? 0) ?></h3><p>Branches</p></div><div class="text-info"><i class="fas fa-code-branch fa-3x opacity-25"></i></div></div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="card card-stat"><div class="d-flex justify-content-between"><div><h3><?= (int)($stats['total_staff'] ?? 0) ?></h3><p>Staff</p></div><div class="text-secondary"><i class="fas fa-users fa-3x opacity-25"></i></div></div></div></div>
    </div>

    <div class="card mb-4"><div class="card-header"><h5><i class="fas fa-chart-line me-2"></i>Store Performance</h5></div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-xl-5">
                    <h6 class="text-muted mb-3" style="font-size:.75rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase"><i class="fas fa-code-branch me-1"></i> Branch Sales</h6>
                    <?php if (empty($branchPerformance)): ?>
                        <p class="text-muted mb-0">No branches with sales yet.</p>
                    <?php else:
                        $totalBranchSales = array_sum(array_map(fn($b) => (float)$b['sales_total'], $branchPerformance)); ?>
                        <div class="d-flex flex-column gap-3">
                        <?php foreach ($branchPerformance as $bp):
                            $pct = $totalBranchSales > 0 ? round((float)$bp['sales_total'] / $totalBranchSales * 100) : 0; ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong><?= sanitize($bp['name']) ?></strong>
                                    <small class="text-muted"><?= (int)$bp['sales_count'] ?> invoices &bull; $<?= number_format((float)$bp['today_sales'], 0) ?> today</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;background:var(--border-color)">
                                        <div class="progress-bar" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#6366f1,#8b5cf6)"></div>
                                    </div>
                                    <strong style="min-width:104px;text-align:right">$<?= number_format((float)$bp['sales_total'], 0) ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-xl-7">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="border-radius:16px;box-shadow:0 10px 30px -18px rgba(15,23,42,.22)">
                                <div class="card-body text-center">
                                    <div class="mb-2" style="font-size:1.9rem;color:var(--primary)"><i class="fas fa-fire"></i></div>
                                    <h6 class="text-muted mb-2" style="font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase">Most Sold Product</h6>
                                    <?php if (!empty($topProduct)): ?>
                                        <?php if (!empty($topProduct['image'])): ?>
                                            <img src="../../<?= sanitize($topProduct['image']) ?>" onerror="this.style.display='none'" style="width:64px;height:64px;object-fit:cover;border-radius:12px;margin-bottom:10px">
                                        <?php endif; ?>
                                        <h5 class="mb-1" style="font-weight:800"><?= sanitize($topProduct['name']) ?></h5>
                                        <small class="text-muted d-block mb-2">SKU: <?= sanitize($topProduct['sku']) ?></small>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <span class="badge" style="background:var(--primary-soft, #eef2ff);color:var(--primary, #4f46e5)"><strong><?= (int)$topProduct['total_sold'] ?></strong> sold</span>
                                            <span class="badge bg-success"><i class="fas fa-dollar-sign me-1"></i><?= number_format((float)$topProduct['revenue'], 0) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No completed sales yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="border-radius:16px;box-shadow:0 10px 30px -18px rgba(15,23,42,.22)">
                                <div class="card-body text-center">
                                    <div class="mb-2" style="font-size:1.9rem;color:var(--warning)"><i class="fas fa-trophy"></i></div>
                                    <h6 class="text-muted mb-2" style="font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase">Most Profitable Branch</h6>
                                    <?php
                                    $bestBranch = null; $bestProfit = null;
                                    foreach ($branchPerformance as $bp) {
                                        if ($bestBranch === null || (float)$bp['profit'] > (float)$bestProfit) {
                                            $bestBranch = $bp; $bestProfit = $bp['profit'];
                                        }
                                    } ?>
                                    <?php if ($bestBranch !== null): ?>
                                        <h5 class="mb-1" style="font-weight:800"><?= sanitize($bestBranch['name']) ?></h5>
                                        <div class="my-2 fw-bold" style="font-size:1.3rem;color:<?= (float)$bestProfit >= 0 ? 'var(--success)' : 'var(--danger)' ?>"><?= (float)$bestProfit >= 0 ? '+' : '-' ?>$<?= number_format(abs((float)$bestProfit), 0) ?></div>
                                        <span class="badge <?= (float)$bestProfit >= 0 ? 'bg-success' : 'bg-danger' ?>"><?= (float)$bestProfit >= 0 ? 'Profitable' : 'In Loss' ?></span>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No branches found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Branch</th><th class="text-end">Revenue</th><th class="text-end">Cost of Goods</th><th class="text-end">Profit</th><th class="text-end">Status</th></tr></thead>
                            <tbody>
                            <?php if (empty($branchPerformance)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No branches with sales yet.</td></tr>
                            <?php else: foreach ($branchPerformance as $bp):
                                $profit = (float)$bp['profit']; ?>
                                <tr>
                                    <td><strong><?= sanitize($bp['name']) ?></strong></td>
                                    <td class="text-end">$<?= number_format((float)$bp['sales_total'], 2) ?></td>
                                    <td class="text-end">$<?= number_format((float)$bp['cogs'], 2) ?></td>
                                    <td class="text-end fw-bold" style="color:<?= $profit >= 0 ? 'var(--success)' : 'var(--danger)' ?>">$<?= number_format($profit, 2) ?></td>
                                    <td class="text-end"><span class="badge <?= $profit >= 0 ? 'bg-success' : 'bg-danger' ?>"><?= $profit >= 0 ? 'Profitable' : 'Loss' ?></span></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>