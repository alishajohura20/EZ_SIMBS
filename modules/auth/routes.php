<?php
/**
 * Auth Module Routes
 * Maps to existing API endpoints and pages - zero code impact
 */

// API Routes (existing files in api/auth/)
$authApiRoutes = [
    'login'         => __DIR__ . '/../../api/auth/login.php',
    'register'      => __DIR__ . '/../../api/auth/register.php',
    'register-store'=> __DIR__ . '/../../api/auth/register-store.php',
    'logout'        => __DIR__ . '/../../api/auth/logout.php',
    // sessions handled in api/sessions/
];

// Page Routes (existing files in pages/auth/)
$authPageRoutes = [
    'login'           => __DIR__ . '/../../pages/auth/login.php',
    'register'        => __DIR__ . '/../../pages/auth/register.php',
    'register-store'  => __DIR__ . '/../../pages/auth/register-store.php',
    'forgot-password' => __DIR__ . '/../../pages/auth/forgot-password.php',
    'reset-password'  => __DIR__ . '/../../pages/auth/reset-password.php',
    'logout'          => __DIR__ . '/../../pages/auth/logout.php',
    // profile, login-history in pages/users/
];

// Session API (existing)
$sessionApiRoutes = [
    'index' => __DIR__ . '/../../api/sessions/index.php',
];

// Session Pages (existing)
$sessionPageRoutes = [
    'index' => __DIR__ . '/../../pages/sessions/index.php',
];

// User API (existing)
$userApiRoutes = [
    'index' => __DIR__ . '/../../api/users/index.php',
    'id'    => __DIR__ . '/../../api/users/[id].php',
];

// User Pages (existing)
$userPageRoutes = [
    'index'       => __DIR__ . '/../../pages/users/index.php',
    'profile'     => __DIR__ . '/../../pages/users/profile.php',
    'login-history'=> __DIR__ . '/../../pages/users/login-history.php',
];

return [
    'api'  => array_merge($authApiRoutes, $sessionApiRoutes, $userApiRoutes),
    'page' => array_merge($authPageRoutes, $sessionPageRoutes, $userPageRoutes),
];