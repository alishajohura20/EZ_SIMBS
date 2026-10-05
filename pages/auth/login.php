<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';

if (isLoggedIn()) {
    $role = $_SESSION['user_role'] ?? '';
    if (in_array($role, ['admin', 'manager', 'branch_manager', 'cashier'])) {
        redirect(APP_URL . '/pages/store/');
    }
    redirect(APP_URL . '/pages/customer/dashboard.php');
}

$error = '';
$flash = getFlash();
$success = is_array($flash) ? ($flash['message'] ?? '') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $result = $auth->login($_POST['email'], $_POST['password'], isset($_POST['remember']));
    if ($result['success']) {
        $role = $result['role'] ?? $_SESSION['user_role'] ?? '';
        if (in_array($role, ['admin', 'manager', 'branch_manager', 'cashier'])) {
            redirect(APP_URL . '/pages/store/');
        }
        redirect(APP_URL . '/pages/customer/dashboard.php');
    } else {
        $error = $result['message'];
    }
}

require_once '../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-wrap">
        <div class="auth-panel">
            <div class="auth-logo">
                <span class="auth-logo-icon"><i class="fas fa-store"></i></span>
                <h3>EZ SIMBS</h3>
            </div>
            <h1>Smart Inventory &amp; Billing System</h1>
            <p class="tagline">Manage your products, track stock levels, process sales, and grow your business - all from one powerful dashboard.</p>
            <ul class="auth-points">
                <li><i class="fas fa-boxes"></i> Real-time inventory tracking</li>
                <li><i class="fas fa-cash-register"></i> Fast &amp; easy point-of-sale</li>
                <li><i class="fas fa-chart-line"></i> Sales reports &amp; analytics</li>
                <li><i class="fas fa-bell"></i> Low-stock notifications</li>
            </ul>
        </div>
        <div class="auth-card">
            <a href="<?= APP_URL ?>" class="auth-back"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
            <div class="auth-card-header">
                <h4>Welcome Back!</h4>
                <p>Sign in to access your account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger auth-alert mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i><?= sanitize($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success auth-alert mb-3">
                    <i class="fas fa-check-circle me-2"></i><?= sanitize($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="auth-input-group">
                    <label class="form-label">Email Address</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="yourname@example.com" required autocomplete="email"
                               value="<?= sanitize($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Password</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="small" style="color: var(--primary); font-weight: 600;">Forgot password?</a>
                </div>
                <button type="submit" class="btn auth-btn">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>

            <div class="auth-divider">New to EZ SIMBS?</div>

            <div class="auth-footer-links">
                Don't have an account? <a href="register-store.php">Create Free Store</a> or <a href="register.php">Register as Customer</a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('i').className = isPassword ? 'far fa-eye-slash' : 'far fa-eye';
}
</script>

<?php require_once '../../includes/footer.php'; ?>