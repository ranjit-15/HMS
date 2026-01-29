<?php

/**
 * HMS - Root Index File for Shared Hosting
 * 
 * This file serves as the entry point when the project is deployed
 * directly to the web root (e.g., public_html/).
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Set the public path to project root for shared hosting
$_ENV['LARAVEL_PUBLIC_PATH'] = __DIR__;

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

// Override the public path to project root
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
