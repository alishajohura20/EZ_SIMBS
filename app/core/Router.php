<?php
/**
 * Central Router - Loads all module routes
 * Zero code impact - maps to existing files
 */

namespace App\Core;

class Router
{
    private array $apiRoutes = [];
    private array $pageRoutes = [];
    private array $modules = [
        'auth'           => 'modules/auth/routes.php',
        'catalog'        => 'modules/catalog/routes.php',
        'purchasing'     => 'modules/purchasing/routes.php',
        'sales'          => 'modules/sales/routes.php',
        'reporting'      => 'modules/reporting/routes.php',
        'administration' => 'modules/administration/routes.php',
    ];

    public function __construct()
    {
        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        foreach ($this->modules as $module => $path) {
            $fullPath = __DIR__ . '/../../' . $path;
            if (file_exists($fullPath)) {
                $routes = require $fullPath;
                $this->apiRoutes  = array_merge($this->apiRoutes,  $routes['api']  ?? []);
                $this->pageRoutes = array_merge($this->pageRoutes, $routes['page'] ?? []);
            }
        }
    }

    public function getApiRoute(string $key): ?string
    {
        return $this->apiRoutes[$key] ?? null;
    }

    public function getPageRoute(string $key): ?string
    {
        return $this->pageRoutes[$key] ?? null;
    }

    public function getAllApiRoutes(): array
    {
        return $this->apiRoutes;
    }

    public function getAllPageRoutes(): array
    {
        return $this->pageRoutes;
    }

    public function resolveApi(string $endpoint): ?string
    {
        $endpoint = ltrim($endpoint, '/');
        $parts = explode('/', $endpoint);
        
        $key = implode('-', $parts);
        if (isset($this->apiRoutes[$key])) {
            return $this->apiRoutes[$key];
        }

        $keyWithId = implode('-', array_slice($parts, 0, -1)) . '-id';
        if (isset($this->apiRoutes[$keyWithId]) && count($parts) > 1) {
            return $this->apiRoutes[$keyWithId];
        }

        return null;
    }
}