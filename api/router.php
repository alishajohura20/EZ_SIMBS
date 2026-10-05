<?php
/**
 * API Router - Routes API requests to existing endpoints
 * Zero code impact - maps to existing files in api/
 */

require_once __DIR__ . '/../app/core/Bootstrap.php';

$router = \App\Core\Bootstrap::init();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim(str_replace('/api', '', $uri), '/');

if (empty($uri) || $uri === 'index.php') {
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found']);
    exit;
}

$file = $router->resolveApi($uri);

if ($file && file_exists($file)) {
    require_once $file;
} else {
    $legacyPath = __DIR__ . '/' . $uri . '.php';
    if (file_exists($legacyPath)) {
        require_once $legacyPath;
    } else {
        $legacyPathDir = __DIR__ . '/' . $uri . '/index.php';
        if (file_exists($legacyPathDir)) {
            require_once $legacyPathDir;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found: ' . $uri]);
        }
    }
}