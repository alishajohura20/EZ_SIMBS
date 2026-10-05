<?php
class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login($email, $password, $remember = false, $expectedRole = null) {
        $user = $this->db->fetch(
            "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?",
            [$email]
        );

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if ($user['status'] === 'locked') {
            return ['success' => false, 'message' => 'Account is locked. Contact administrator.'];
        }

        if ($user['lockout_until'] && strtotime($user['lockout_until']) > time()) {
            $minutes = ceil((strtotime($user['lockout_until']) - time()) / 60);
            return ['success' => false, 'message' => "Account locked. Try again in {$minutes} minutes."];
        }

        if (!password_verify($password, $user['password'])) {
            $this->handleFailedLogin($user);
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is inactive.'];
        }

        if ($expectedRole && $user['role_name'] !== $expectedRole) {
            return ['success' => false, 'message' => "This account is registered as " . ucfirst(str_replace('_', ' ', $user['role_name'])) . ", not " . ucfirst(str_replace('_', ' ', $expectedRole)) . "."];
        }

        $this->db->update('users', [
            'failed_attempts' => 0,
            'lockout_until' => null,
            'last_login' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$user['id']]);

        // Create a tracked session row (allows concurrent logins across devices)
        $sm = new SessionManager();
        $session = $sm->createSession($user['id'], $remember);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'];
        $_SESSION['user_role_id'] = $user['role_id'];
        $_SESSION['user_branch'] = $user['branch_id'];
        $_SESSION['session_token'] = $session['token'];

        if ($remember) {
            setcookie('remember_token', $session['remember_token'], time() + SESSION_REMEMBER_LIFETIME, '/', '', !empty($_SERVER['HTTPS']), true);
        }

        logActivity('login', 'users', $user['id']);

        return ['success' => true, 'role' => $user['role_name']];
    }

    public function logout() {
        $token = $_SESSION['session_token'] ?? null;
        if ($token) {
            $sm = new SessionManager();
            $sm->terminateByToken($token);
        }

        logActivity('logout', 'users', $_SESSION['user_id'] ?? null);
        session_unset();
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

    private function handleFailedLogin($user) {
        $attempts = $user['failed_attempts'] + 1;
        $updates = ['failed_attempts' => $attempts];

        if ($attempts >= 5) {
            $updates['lockout_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $this->db->update('users', $updates, 'id = ?', [$user['id']]);
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->update('users', ['password' => $hashed], 'id = ?', [$userId]);

        // Invalidate all other sessions for this user (keeps the current one)
        $sm = new SessionManager();
        $sm->terminateOthers($_SESSION['session_token'] ?? '', $userId);

        logActivity('password_changed', 'users', $userId);
        return ['success' => true, 'message' => 'Password changed successfully. All other sessions have been logged out.'];
    }

    public function updateProfile($userId, $data, $file = null) {
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $updates = [];
        $sessionSync = [];

        $name = trim($data['name'] ?? $user['name']);
        if ($name === '') {
            return ['success' => false, 'message' => 'Name cannot be empty'];
        }
        $updates['name'] = sanitize($name);
        $sessionSync['name'] = $updates['name'];

        $email = strtolower(trim($data['email'] ?? $user['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address'];
        }
        if ($this->db->fetch("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        if ($email !== $user['email']) {
            $updates['email'] = $email;
            $sessionSync['email'] = $email;
        }

        $updates['phone'] = trim($data['phone'] ?? $user['phone'] ?? '');

        if ($file && !empty($file['name']) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $avatar = $this->uploadAvatar($file, $userId);
            if ($avatar === null) {
                return ['success' => false, 'message' => 'Avatar must be a JPG, PNG, WebP or GIF image'];
            }
            $updates['avatar'] = $avatar;
        }

        $this->db->update('users', $updates, 'id = ?', [$userId]);

        foreach ($sessionSync as $key => $value) {
            $_SESSION["user_{$key}"] = $value;
        }

        logActivity('profile_updated', 'users', $userId, null, $updates);
        return ['success' => true, 'message' => 'Profile updated successfully'];
    }

    private function uploadAvatar($file, $userId) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) return null;

        $dir = UPLOAD_PATH . '/avatars/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $filename = "user_{$userId}_" . time() . ".{$ext}";
        $dest = $dir . $filename;
        if (is_uploaded_file($file['tmp_name'])) {
            $ok = move_uploaded_file($file['tmp_name'], $dest);
        } else {
            $ok = rename($file['tmp_name'], $dest);
            if (!$ok) $ok = copy($file['tmp_name'], $dest) && unlink($file['tmp_name']);
        }
        if (!$ok) return null;
        @chmod($dest, 0644);
        return "uploads/avatars/{$filename}";
    }

    public function forgotPassword($email) {
        $user = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if (!$user) {
            return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_expires'] = time() + 3600;
        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
    }

    public function resetPassword($token, $newPassword) {
        if (!isset($_SESSION['reset_token']) || $_SESSION['reset_token'] !== $token) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }
        if (time() > ($_SESSION['reset_expires'] ?? 0)) {
            return ['success' => false, 'message' => 'Token expired. Please request a new reset link.'];
        }
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $userId = $_SESSION['reset_user_id'];
        $this->db->update('users', ['password' => $hashed], 'id = ?', [$userId]);

        // Terminate all existing sessions so every other device is logged out
        $sm = new SessionManager();
        $sm->terminateAllForUser($userId);

        unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);
        logActivity('password_reset', 'users', $userId);
        return ['success' => true, 'message' => 'Password reset successfully. All other devices have been logged out.'];
    }

    public function register($data) {
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        if ($this->db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $userId = $this->db->insert('users', [
            'name' => sanitize($data['name']),
            'email' => sanitize($data['email']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $data['role_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'phone' => sanitize($data['phone'] ?? ''),
            'status' => 'active',
        ]);

        logActivity('user_created', 'users', $userId);
        return ['success' => true, 'user_id' => $userId];
    }

    public function registerPublic($data) {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';
        $roleKey = strtolower(trim($data['role'] ?? 'customer'));

        $allowedRoles = ['customer'];
        if (!in_array($roleKey, $allowedRoles)) {
            $roleKey = 'customer';
        }

        if ($name === '' || $email === '') {
            return ['success' => false, 'message' => 'Name and email are required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        if ($password !== $passwordConfirm) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }

        if ($this->db->fetch("SELECT id FROM users WHERE email = ?", [$email])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $role = $this->db->fetch("SELECT id FROM roles WHERE name = ?", [$roleKey]);
        $roleId = $role ? (int)$role['id'] : 5;

        $userId = $this->db->insert('users', [
            'name' => sanitize($name),
            'email' => sanitize($email),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => $roleId,
            'branch_id' => null,
            'phone' => sanitize($phone ?? ''),
            'status' => 'active',
        ]);

        logActivity('user_registered', 'users', $userId, null, ['role' => $roleKey]);
        return ['success' => true, 'user_id' => $userId, 'role' => $roleKey];
    }
}