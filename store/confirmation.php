<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (isLoggedIn()) {
    $role = $_SESSION['user_role'] ?? '';
    if (in_array($role, ['admin','manager','branch_manager','cashier'])) {
        redirect(APP_URL . '/pages/store/');
    } else {
        redirect(APP_URL . '/pages/customer/dashboard.php');
    }
}

require_once '../../classes/Store.php';
$storeId = (int)($_GET['store_id'] ?? 0);
$store = $storeId ? (new Store())->getById($storeId) : null;

if (!$store) {
    setFlash('error', 'Store not found. Please register again.');
    redirect(APP_URL . '/pages/auth/register-store.php');
}

$flash = getFlash();
$flashMessage = is_array($flash) ? ($flash['message'] ?? '') : '';
$flashType = is_array($flash) ? ($flash['type'] ?? 'success') : 'success';

require_once '../../includes/header.php';
?>
<div class="auth-container">
    <div class="auth-wrap" style="max-width: 640px; margin: 0 auto;">
        <div class="auth-card" style="border-top: 4px solid var(--success, #198754); text-align: center; padding: 3rem 2rem;">
            <div style="width:80px; height:80px; border-radius:50%; background:rgba(25,135,84,0.1); display:inline-flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                <i class="fas fa-check-circle" style="font-size:42px; color:var(--success, #198754);"></i>
            </div>
            <h2 style="font-weight:700; margin-bottom:0.5rem;">Store Created Successfully!</h2>
            <p style="color:var(--text-secondary, #6c757d); font-size:1.05rem; margin-bottom:0.3rem;">
                Welcome to <strong><?= sanitize($store['name']) ?></strong>
            </p>
            <p style="color:var(--text-secondary, #6c757d); font-size:0.95rem; margin-bottom:1.8rem;">
                Your store is now active on the <strong><?= ucfirst(sanitize($store['plan'])) ?> Plan</strong>.
                Sign in with the email and password you just created to access your dashboard.
            </p>

            <?php if ($flashMessage && $flashType === 'success'): ?>
                <div class="alert alert-success mb-3" style="text-align:left;">
                    <i class="fas fa-check-circle me-2"></i><?= sanitize($flashMessage) ?>
                </div>
            <?php endif; ?>

            <a href="<?= APP_URL ?>/pages/auth/login.php?store_id=<?= $store['id'] ?>" class="btn auth-btn mb-3" style="width:100%; font-size:1.05rem; padding:0.75rem;">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In to Your Store
            </a>

            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #e9ecef;">
                <div class="d-flex justify-content-center gap-3 flex-wrap" style="font-size:0.9rem;">
                    <a href="<?= APP_URL ?>/pages/auth/register.php?store_id=<?= $store['id'] ?>" style="color:var(--primary); font-weight:500;">
                        <i class="fas fa-user-plus me-1"></i> Register Another User
                    </a>
                    <span style="opacity:.3">|</span>
                    <a href="<?= APP_URL ?>/pages/auth/forgot-password.php?store_id=<?= $store['id'] ?>" style="color:var(--primary); font-weight:500;">
                        <i class="fas fa-key me-1"></i> Forgot Password?
                    </a>
                    <span style="opacity:.3">|</span>
                    <a href="<?= APP_URL ?>" style="color:var(--text-secondary, #6c757d); font-weight:500;">
                        <i class="fas fa-home me-1"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>
