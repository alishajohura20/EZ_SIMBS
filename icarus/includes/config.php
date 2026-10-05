<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'ez_simbs');
define('DB_USER', 'aj');
define('DB_PASS', 'root123');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'EZ_SIMBS');
define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_PATH', APP_ROOT . '/uploads');
define('EXPORT_PATH', APP_ROOT . '/exports');
define('BACKUP_PATH', APP_ROOT . '/backup');

// Session / login session behavior
define('SESSION_LIFETIME', 7 * 24 * 60 * 60);          // default expiry for a login session (7 days)
define('SESSION_REMEMBER_LIFETIME', 30 * 24 * 60 * 60); // expiry when "Remember me" is checked (30 days)

// Auto-detect base URL so it works on any server/port (php -S dev server, Apache, etc.)
$__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$__host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$__docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? APP_ROOT)), '/');
$__appRoot = str_replace('\\', '/', APP_ROOT);
$__scriptDir = '';
if ($__docRoot !== '' && strpos($__appRoot, $__docRoot) === 0) {
    $__scriptDir = substr($__appRoot, strlen($__docRoot));
}
define('APP_URL', $__scheme . '://' . $__host . $__scriptDir);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('UTC');
