<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/pages/dashboard/index.php');
}

$message = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $result = $auth->forgotPassword($_POST['email']);
    $message = $result['message'];

    // Demo mode: no mail daemon is configured, so surface the reset link directly
    if (isset($_SESSION['reset_token'])) {
        $resetLink = APP_URL . '/pages/auth/reset-password.php?token=' . urlencode($_SESSION['reset_token']);
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
            <h1>Trouble Signing In?</h1>
            <p class="tagline">Enter the email associated with your account and we'll send you a link to reset your password.</p>
            <ul class="auth-points">
                <li><i class="fas fa-key"></i> Secure password reset</li>
                <li><i class="fas fa-envelope-open-text"></i> Reset link sent to your inbox</li>
                <li><i class="fas fa-shield-alt"></i> Your data stays safe</li>
            </ul>
        </div>
        <div class="auth-card">
            <a href="<?= APP_URL ?>" class="auth-back"><i class="fas fa-arrow-left me-1"></i> Back to Store</a>
            <div class="auth-card-header">
                <h4>Forgot Password</h4>
                <p>We'll email you a reset link</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-info auth-alert mb-3">
                    <i class="fas fa-envelope-open-text me-2"></i><?= sanitize($message) ?>
                </div>
                <?php if ($resetLink): ?>
                    <div class="alert alert-warning auth-alert mb-3">
                        <strong><i class="fas fa-info-circle me-1"></i> Demo mode:</strong>
                        since no mail server is configured, use this reset link directly:
                        <div class="mt-2 mb-1">
                            <a href="<?= sanitize($resetLink) ?>" class="text-break" style="font-size:0.95rem;">
                                <?= sanitize($resetLink) ?>
                            </a>
                        </div>
                        <a href="<?= sanitize($resetLink) ?>" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-key me-1"></i> Reset Password Now
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="POST">
                <div class="auth-input-group">
                    <label class="form-label">Email Address</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="yourname@example.com" required autofocus>
                    </div>
                </div>
                <button type="submit" class="btn auth-btn mb-3">
                    <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                </button>
            </form>

            <div class="auth-divider"></div>

            <div class="auth-footer-links d-flex justify-content-center gap-3 flex-wrap">
                <a href="login.php"><i class="fas fa-arrow-left me-1"></i> Back to Sign In</a>
                <span style="opacity:.5">|</span>
                <a href="register-store.php"><i class="fas fa-user-plus me-1"></i> Create Free Store</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>