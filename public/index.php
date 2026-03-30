<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-detect app root:
// - On server: app lives in ../firstmediator.com/ (relative to public_html/)
// - Locally:   app lives in ../            (relative to public/)
$appRoot = is_dir(__DIR__ . '/../firstmediator.com')
    ? __DIR__ . '/../firstmediator.com'
    : __DIR__ . '/..';

    


// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appRoot . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appRoot . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appRoot . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
