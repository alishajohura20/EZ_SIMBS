<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
requireLogin();
$pageTitle = 'My Profile';
$user = currentUser();
$db = Database::getInstance();
$branchName = $db->fetch("SELECT name FROM branches WHERE id = ?", [$user['branch_id']])['name'] ?? '';
$storeName = $db->fetch("SELECT name FROM stores WHERE id = ?", [$user['store_id']])['name'] ?? '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../classes/Auth.php';
    $auth = new Auth();
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

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

function profileField($label, $value, $icon) {
    echo '<div class="d-flex justify-content-between py-2 border-bottom">';
    echo '<span class="text-muted"><i class="fas fa-' . $icon . ' me-2"></i>' . $label . '</span>';
    echo '<strong class="text-end">' . ($value ?: '—') . '</strong>';
    echo '</div>';
}
?>

<div class="main-content">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="mb-4">
        <h4>My Profile</h4>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?= $error ?></div><?php endif; ?>

    <div class="card card-stat mb-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="profile-logo" style="width:72px;height:72px;font-size:1.8rem;">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= APP_URL . '/' . sanitize($user['avatar']) ?>" alt="Avatar">
                <?php else: ?>
                    <?= strtoupper(substr(sanitize($user['name']), 0, 1)) ?>
                <?php endif; ?>
            </span>
            <div class="flex-grow-1">
                <h4 class="mb-1"><?= sanitize($user['name']) ?></h4>
                <span class="badge bg-primary"><?= ucfirst(str_replace('_', ' ', sanitize($user['role_name']))) ?></span>
                <?php if ($user['status'] === 'active'): ?>
                    <span class="badge bg-success">Active</span>
                <?php else: ?>
                    <span class="badge bg-danger"><?= ucfirst(sanitize($user['status'])) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header"><h5><i class="fas fa-id-card me-2"></i>Profile Information</h5></div>
                <div class="card-body px-4 py-2">
                    <?php
                    profileField('Full Name', sanitize($user['name']), 'user');
                    profileField('Email', sanitize($user['email']), 'envelope');
                    profileField('Phone', sanitize($user['phone'] ?? ''), 'phone');
                    profileField('Role', ucfirst(str_replace('_', ' ', sanitize($user['role_name']))), 'shield-alt');
                    profileField('Branch', $branchName, 'code-branch');
                    profileField('Store', $storeName, 'store');
                    profileField('Account Created', date('M d, Y', strtotime($user['created_at'])), 'calendar-plus');
                    profileField('Last Login', $user['last_login'] ? date('M d, Y h:i A', strtotime($user['last_login'])) : 'Never', 'sign-in-alt');
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header"><h5><i class="fas fa-user-edit me-2"></i>Edit Profile Information</h5></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5><i class="fas fa-lock me-2"></i>Change Password</h5></div>
                <div class="card-body">
                    <form method="POST" id="passwordForm">
                        <input type="hidden" name="change_password" value="1">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" class="form-control" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                                </div>
                                <div class="form-text">At least 6 characters.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd(this)"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-key me-1"></i>Update Password</button>
                    </form>
                    <div class="form-text mt-3"><i class="fas fa-info-circle me-1"></i>Changing your password signs you out of all other devices.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector('.card-stat .profile-logo').innerHTML = '<img src="' + e.target.result + '" alt="Avatar">';
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

<?php require_once '../../includes/footer.php'; ?>