<?php
/**
 * Application Bootstrap
 * Initializes autoloading, config, and core services
 */

namespace App\Core;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Router;

class Bootstrap
{
    public static function init(): Router
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        date_default_timezone_set('UTC');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return new Router();
    }
}