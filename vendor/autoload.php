<?php
/**
 * Minimal PSR-4 Autoloader (fallback when composer not available)
 * Maps namespaces to directories for zero-impact migration
 */

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\Core\\'           => __DIR__ . '/../app/core/',
        'App\\Config\\'         => __DIR__ . '/../app/config/',
        'App\\Middleware\\'     => __DIR__ . '/../app/middleware/',
        'App\\Exceptions\\'     => __DIR__ . '/../app/exceptions/',
        'App\\Modules\\Auth\\'  => __DIR__ . '/../modules/auth/classes/',
        'App\\Modules\\Catalog\\'=> __DIR__ . '/../modules/catalog/classes/',
        'App\\Modules\\Purchasing\\' => __DIR__ . '/../modules/purchasing/classes/',
        'App\\Modules\\Sales\\' => __DIR__ . '/../modules/sales/classes/',
        'App\\Modules\\Reporting\\' => __DIR__ . '/../modules/reporting/classes/',
        'App\\Modules\\Administration\\' => __DIR__ . '/../modules/administration/classes/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});