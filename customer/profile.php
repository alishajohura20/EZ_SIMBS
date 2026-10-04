<?php
$pageTitle = 'My Profile';
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireRole(['customer', 'admin', 'manager', 'branch_manager', 'cashier']);
require_once '../../classes/Auth.php';

$user = currentUser();
$db = Database::getInstance();
$auth = new Auth();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $result = $auth->updateProfile($user['id'], $_POST, $_FILES['avatar'] ?? null);
        if ($result['success']) {
            $user = currentUser();
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    } elseif (isset($_POST['change_password'])) {
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $error = 'New password and confirmation do not match';
        } elseif (strlen($_POST['new_password']) < 6) {
            $error = 'New password must be at least 6 characters';
        } else {
            $result = $auth->changePassword(
                $user['id'],
                $_POST['current_password'],
                $_POST['new_password']
            );
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

require_once '../../includes/storefront-header.php';
?>

<div class="sf-container">
    <div class="sf-page-head">
        <h1 class="sf-page-title"><i class="fas fa-user"></i>My Profile</h1>
        <a class="sf-btn sf-btn-outline" href="<?= APP_URL ?>/pages/customer/dashboard.php"><i class="fas fa-arrow-left"></i> Back to Shop</a>
    </div>

    <?php if ($success): ?>
    <div class="sf-toast success show" style="position:relative;bottom:auto;right:auto;opacity:1;transform:none;margin-bottom:20px">
        <i class="fas fa-check-circle"></i><span><?= sanitize($success) ?></span>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="sf-toast error show" style="position:relative;bottom:auto;right:auto;opacity:1;transform:none;margin-bottom:20px">
        <i class="fas fa-exclamation-circle"></i><span><?= sanitize($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="sf-panel" style="padding:24px;text-align:center">
                <div class="profile-logo mx-auto mb-3" style="width:96px;height:96px;font-size:2.5rem;">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= APP_URL . '/' . sanitize($user['avatar']) ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                    <?php else: ?>
                        <?= strtoupper(substr(sanitize($user['name']), 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1"><?= sanitize($user['name']) ?></h4>
                <span class="sf-badge-chip"><i class="fas fa-user"></i><?= ucfirst(str_replace('_', ' ', sanitize($user['role_name']))) ?></span>
                <?php if ($user['status'] === 'active'): ?>
                    <span class="sf-badge-chip" style="background:var(--sf-green);color:#fff"><i class="fas fa-check-circle"></i>Active</span>
                <?php else: ?>
                    <span class="sf-badge-chip" style="background:var(--sf-red);color:#fff"><i class="fas fa-times-circle"></i><?= ucfirst(sanitize($user['status'])) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="sf-panel mb-4">
                <h5 class="sf-panel-title"><i class="fas fa-id-card me-2"></i>Profile Information</h5>
                <form method="POST" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="sf-label">Full Name</label>
                            <input type="text" name="name" class="sf-form" value="<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="sf-label">Email</label>
                            <input type="email" name="email" class="sf-form" value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="sf-label">Phone</label>
                            <input type="text" name="phone" class="sf-form" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="sf-label">Profile Picture</label>
                            <input type="file" name="avatar" class="sf-form" accept="image/*" onchange="previewAvatar(this)">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="sf-btn sf-btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                    </div>
                </form>
            </div>

            <div class="sf-panel">
                <h5 class="sf-panel-title"><i class="fas fa-lock me-2"></i>Change Password</h5>
                <form method="POST" id="passwordForm">
                    <input type="hidden" name="change_password" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="sf-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" class="sf-form" required>
                                <button type="button" class="sf-btn sf-btn-outline" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="sf-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" class="sf-form" required minlength="6">
                                <button type="button" class="sf-btn sf-btn-outline" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="sf-muted small mt-1">At least 6 characters.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="sf-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" class="sf-form" required minlength="6">
                                <button type="button" class="sf-btn sf-btn-outline" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="sf-btn sf-btn-gold"><i class="fas fa-key me-1"></i>Update Password</button>
                    </div>
                    <div class="sf-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Changing your password signs you out of all other devices.</div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector('.profile-logo').innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePwd(btn) {
    const input = btn.closest('.input-group').querySelector('input');
    const icon = btn.querySelector('i');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !show);
    icon.classList.toggle('fa-eye-slash', show);
}
</script>

<?php require_once '../../includes/storefront-footer.php'; ?>