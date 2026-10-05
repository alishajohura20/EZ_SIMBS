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

$success = '';
$error   = '';
$form    = ['store_name'=>'','name'=>'','email'=>'','phone'=>'','address'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../classes/Store.php';
    $store = new Store();
    $result = $store->register([
        'store_name'      => $_POST['store_name'] ?? '',
        'name'            => $_POST['name'] ?? '',
        'email'           => $_POST['email'] ?? '',
        'phone'           => $_POST['phone'] ?? '',
        'address'         => $_POST['address'] ?? '',
        'password'        => $_POST['password'] ?? '',
        'password_confirm'=> $_POST['password_confirm'] ?? '',
    ]);

    if ($result['success']) {
        require_once '../../classes/Auth.php';
        $auth = new Auth();
        $auth->login($result['email'], $_POST['password'], true);
        setFlash('success', 'Store created! Welcome to EZ SIMBS.');
        redirect(APP_URL . '/pages/store/');
    } else {
        $error = $result['message'];
        $form = [
            'store_name' => sanitize($_POST['store_name'] ?? ''),
            'name'       => sanitize($_POST['name'] ?? ''),
            'email'      => sanitize($_POST['email'] ?? ''),
            'phone'      => sanitize($_POST['phone'] ?? ''),
            'address'    => sanitize($_POST['address'] ?? ''),
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
            <h1>Launch Your Store</h1>
            <p class="tagline">Set up your inventory, billing and POS system in minutes. Free forever plan included.</p>
            <ul class="auth-points">
                <li><i class="fas fa-store"></i> Instant store setup</li>
                <li><i class="fas fa-boxes"></i> Unlimited products & stock tracking</li>
                <li><i class="fas fa-cash-register"></i> Built-in POS & billing</li>
                <li><i class="fas fa-user-shield"></i> Role-based team access</li>
                <li><i class="fas fa-headset"></i> Ongoing support</li>
            </ul>
        </div>
        <div class="auth-card">
            <a href="<?= APP_URL ?>" class="auth-back"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
            <div class="auth-card-header">
                <h4>Create Free Store</h4>
                <p>Fill in your store details to get started</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger auth-alert mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i><?= sanitize($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="storeRegForm" novalidate>
                <div class="auth-input-group">
                    <label class="form-label">Store Name *</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-store"></i>
                        <input type="text" name="store_name" class="form-control" placeholder="e.g. My Grocery Store" required value="<?= $form['store_name'] ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Your Full Name *</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required value="<?= $form['name'] ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Email Address *</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required value="<?= $form['email'] ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Phone Number</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" class="form-control" placeholder="+1 (555) 000-0000" value="<?= $form['phone'] ?>">
                    </div>
                </div>
                <div class="auth-input-group">
                    <label class="form-label">Address</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="address" class="form-control" placeholder="123 Business St, City" value="<?= $form['address'] ?>">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="auth-input-group">
                            <label class="form-label">Password *</label>
                            <div class="auth-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" class="form-control" id="storeRegPassword" placeholder="Min. 6 characters" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('storeRegPassword', this)"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="auth-input-group">
                            <label class="form-label">Confirm Password *</label>
                            <div class="auth-input-wrap">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password_confirm" class="form-control" id="storeRegPassConfirm" placeholder="Re-enter" required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('storeRegPassConfirm', this)"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-check mb-4 mt-2">
                    <input type="checkbox" class="form-check-input" id="agreeStore" required>
                    <label class="form-check-label small" for="agreeStore">I agree to the <a href="#" style="color:var(--primary)">Terms &amp; Conditions</a></label>
                </div>
                <button type="submit" class="btn auth-btn">
                    <i class="fas fa-rocket me-2"></i> Create My Store
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
document.getElementById('storeRegForm').addEventListener('submit', (e) => {
    const p = document.getElementById('storeRegPassword').value;
    const c = document.getElementById('storeRegPassConfirm').value;
    if (p !== c) { e.preventDefault(); alert('Passwords do not match.'); }
});
</script>
<?php require_once '../../includes/footer.php'; ?>
