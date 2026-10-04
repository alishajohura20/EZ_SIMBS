<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if (isLoggedIn()) {
    $role = $_SESSION['user_role'] ?? '';
    if (in_array($role, ['admin', 'manager', 'branch_manager', 'cashier'])) {
        redirect(APP_URL . '/pages/store/');
    }
    redirect(APP_URL . '/pages/customer/dashboard.php');
}

$success = '';
$error = '';
$form = ['name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../classes/Auth.php';
    $auth = new Auth();
    $result = $auth->registerPublic([
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'role' => 'customer',
    ]);
    if ($result['success']) {
        setFlash('success', "Customer account created successfully! You can now sign in.");
        redirect(APP_URL . '/pages/auth/login.php');
    } else {
        $error = $result['message'];
        $form = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
        ];
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
            <h1>Create Customer Account</h1>
            <p class="tagline">Join <?= APP_NAME ?> to shop the online storefront, track your orders and earn loyalty rewards.</p>
            <ul class="auth-points">
                <li><i class="fas fa-shopping-cart"></i> Shop the live storefront</li>
                <li><i class="fas fa-box"></i> Track your orders anytime</li>
                <li><i class="fas fa-gift"></i> Earn loyalty rewards</li>
                <li><i class="fas fa-shield-alt"></i> Secure &amp; private account</li>
            </ul>
        </div>
        <div class="auth-card">
            <a href="<?= APP_URL ?>" class="auth-back"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
            <div class="auth-card-header">
                <h4>Join the Store</h4>
                <p>Create your customer account to start shopping</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger auth-alert mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i><?= sanitize($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>
                <div class="auth-input-group">
                    <label class="form-label">Full Name *</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" class="form-control" id="regName" placeholder="John Doe" required
                               value="<?= sanitize($form['name']) ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Email Address *</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" id="regEmail" placeholder="yourname@example.com" required
                               value="<?= sanitize($form['email']) ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Phone Number</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" class="form-control" id="regPhone" placeholder="+1 (555) 000-0000"
                               value="<?= sanitize($form['phone']) ?>">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="auth-input-group">
                            <label class="form-label">Password *</label>
                            <div class="auth-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control" id="regPassword" placeholder="Min. 6 characters" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('regPassword', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="auth-input-group">
                            <label class="form-label">Confirm Password *</label>
                            <div class="auth-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password_confirm" class="form-control" id="regPasswordConfirm" placeholder="Re-enter password" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('regPasswordConfirm', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-check mb-4 mt-2">
                    <input type="checkbox" class="form-check-input" id="agree" required>
                    <label class="form-check-label small" for="agree">
                        I agree to the <a href="#" style="color: var(--primary);">Terms &amp; Conditions</a>
                    </label>
                </div>
                <button type="submit" class="btn auth-btn">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </form>

            <div class="auth-divider">Already have an account?</div>

            <div class="auth-footer-links">
                Already registered? <a href="login.php">Sign In</a>
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

document.getElementById('registerForm').addEventListener('submit', (e) => {
    const password = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regPasswordConfirm').value;
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>