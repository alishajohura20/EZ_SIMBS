<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/pages/dashboard/index.php');
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = '';
$success = '';

if ($token === '') {
    $error = 'Missing reset token. Please request a new password reset link.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $result = $auth->resetPassword($_POST['token'], $_POST['password'] ?? '');
    if ($result['success']) {
        $success = $result['message'];
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
            <h1>Reset Your Password</h1>
            <p class="tagline">Choose a new strong password to regain access to your account and continue managing your business seamlessly.</p>
            <ul class="auth-points">
                <li><i class="fas fa-shield-alt"></i> Secure password update</li>
                <li><i class="fas fa-key"></i> New credentials applied instantly</li>
                <li><i class="fas fa-user-check"></i> Sign in with your new password</li>
            </ul>
        </div>
        <div class="auth-card">
            <div class="auth-card-header">
                <h4>Set New Password</h4>
                <p>Enter a new password for your account</p>
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
                <a href="login.php" class="btn auth-btn">
                    <i class="fas fa-sign-in-alt me-2"></i> Proceed to Sign In
                </a>
            <?php elseif ($token !== ''): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?= sanitize($token) ?>">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="auth-input-group">
                                <label class="form-label">New Password *</label>
                                <div class="auth-input-wrap">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" name="password" class="form-control" id="resetPassword" placeholder="Min. 6 characters" required minlength="6">
                                    <button type="button" class="password-toggle" onclick="togglePassword('resetPassword', this)">
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
                                    <input type="password" name="password_confirm" class="form-control" id="resetPasswordConfirm" placeholder="Re-enter password" required minlength="6">
                                    <button type="button" class="password-toggle" onclick="togglePassword('resetPasswordConfirm', this)">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn auth-btn">
                        <i class="fas fa-check me-2"></i> Update Password
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-divider"></div>

            <div class="auth-footer-links">
                Remembered it? <a href="login.php">Back to Sign In</a>
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

document.querySelector('form')?.addEventListener('submit', (e) => {
    const password = document.getElementById('resetPassword').value;
    const confirm = document.getElementById('resetPasswordConfirm').value;
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>