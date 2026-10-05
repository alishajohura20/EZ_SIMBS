<?php
require_once __DIR__ . '/db.php';

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Read a setting from the settings table with a safe default.
 * Mirrors the private SaleReturn::setting() helper so view partials
 * (return-modal, return view) can read policy values without a class instance.
 */
function setting($key, $default = '') {
    static $cache = [];
    if (!array_key_exists($key, $cache)) {
        $row = Database::getInstance()->fetch(
            'SELECT setting_value FROM settings WHERE setting_key = ?', [$key]
        );
        $cache[$key] = $row['setting_value'] ?? $default;
    }
    return $cache[$key];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: {$url}");
    exit;
}

/**
 * Validate the current session against the user_sessions table.
 * Returns false if the session is stale / expired / unknown.
 */
function validateSession() {
    if (!isLoggedIn()) return false;

    $userId = $_SESSION['user_id'];
    $token = $_SESSION['session_token'] ?? null;
    $sm = new SessionManager();

    // Legacy session (logged in before user_sessions was added) – migrate in-place
    if (!$token) {
        $created = $sm->createSession($userId);
        $_SESSION['session_token'] = $created['token'];
        return true;
    }

    if (!$sm->validateSession($userId, $token)) {
        // Session invalid – clear everything
        $_SESSION = [];
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        return false;
    }
    return true;
}

function requireLogin() {
    if (!isLoggedIn() || !validateSession()) {
        redirect(APP_URL . '/pages/auth/login.php');
    }
}

function requireRole($roles) {
    requireLogin();
    if (!is_array($roles)) $roles = [$roles];
    if (!in_array($_SESSION['user_role'], $roles)) {
        redirect(APP_URL . '/pages/store/');
    }
}

function currentUser() {
    if (!isLoggedIn()) return null;
    $db = Database::getInstance();
    return $db->fetch("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?", [$_SESSION['user_id']]);
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function paginate($totalItems, $perPage, $currentPage) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    return [
        'total' => $totalItems,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
    ];
}

function generateSKU($prefix = 'SKU') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -6));
}

function generateInvoiceNo($prefix = 'INV') {
    return $prefix . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function generatePONumber($prefix = 'PO') {
    return $prefix . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function generateReturnNo($kind = 'sale') {
    $prefix = $kind === 'purchase' ? 'PRET' : 'RET';
    for ($i = 0; $i < 10; $i++) {
        $no = $prefix . '-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $exists = Database::getInstance()->fetch(
            "SELECT id FROM " . ($kind === 'purchase' ? 'purchase_returns' : 'sale_returns') . " WHERE return_no = ?",
            [$no]
        );
        if (!$exists) return $no;
    }
    return $prefix . '-' . date('Ymd') . '-' . uniqid();
}

function formatCurrency($amount) {
    $symbol = '$';
    return $symbol . number_format($amount, 2);
}

function logActivity($action, $resource = null, $resourceId = null, $oldValue = null, $newValue = null) {
    $db = Database::getInstance();
    $db->insert('activity_logs', [
        'user_id' => $_SESSION['user_id'] ?? null,
        'action' => $action,
        'resource' => $resource,
        'resource_id' => $resourceId,
        'old_value' => $oldValue ? json_encode($oldValue) : null,
        'new_value' => $newValue ? json_encode($newValue) : null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'browser' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    ]);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function jsonError($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}

function jsonSuccess($data = null, $message = 'Success') {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

function getRequestBody() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');

    if (stripos($contentType, 'application/json') !== false) {
        $json = json_decode($raw, true);
        if (is_array($json)) return $json;
        return $_POST;
    }

    if (stripos($contentType, 'multipart/form-data') !== false) {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            return $_POST;
        }
        $parsed = parseMultipartFormData($contentType, $raw);
        foreach ($parsed['files'] as $key => $file) {
            $_FILES[$key] = $file;
        }
        return array_merge($_POST, $parsed['post']);
    }

    $json = json_decode($raw, true);
    if (is_array($json)) return $json;
    return $_POST;
}

function parseMultipartFormData($contentType, $raw) {
    $post = [];
    $files = [];
    if (!preg_match('/boundary=(.*)$/i', $contentType, $m)) {
        return ['post' => [], 'files' => []];
    }
    $boundary = trim($m[1], '"');
    $parts = explode('--' . $boundary, $raw);

    foreach ($parts as $part) {
        if (strlen($part) < 4) continue;
        if (substr($part, 0, 2) === "\r\n") $part = substr($part, 2);
        $part = rtrim($part, "\r\n");
        if (strpos($part, "\r\n\r\n") === false) continue;

        [$headers, $content] = explode("\r\n\r\n", $part, 2);
        $content = rtrim($content, "\r\n");

        $name = null;
        $filename = null;
        if (preg_match('/name="([^"]*)"/', $headers, $m2)) $name = $m2[1];
        if (preg_match('/filename="([^"]*)"/', $headers, $m2)) $filename = $m2[1];

        if ($name === null) continue;

        if ($filename !== null && $filename !== '') {
            $tmp = tempnam(sys_get_temp_dir(), 'php');
            file_put_contents($tmp, $content);
            $files[$name] = [
                'name' => $filename,
                'type' => '',
                'size' => strlen($content),
                'tmp_name' => $tmp,
                'error' => UPLOAD_ERR_OK,
            ];
        } else {
            $post[$name] = $content;
        }
    }

    return ['post' => $post, 'files' => $files];
}

// ── Session auto-boot ───────────────────────────────────────────────────────
// Runs every time functions.php is included so active sessions are
// validated and remembered sessions are silently restored.

try {
    // 1. No active PHP session but remember cookie present → restore silently
    if (!isLoggedIn() && !empty($_COOKIE['remember_token'])) {
        $sm = new SessionManager();
        $sm->restoreRememberedSession($_COOKIE['remember_token']);
    }

    // 2. Active PHP session with a session token → validate it's still live
    if (isLoggedIn() && !empty($_SESSION['session_token'])) {
        if (!validateSession()) {
            // Session expired or revoked – boot to login
            // requireLogin() will handle redirect; just clear state here
            $_SESSION = [];
        }
    }
} catch (Throwable $e) {
    // Don't let session DB errors break the page entirely
    error_log("[SessionManager] boot error: " . $e->getMessage());
}
