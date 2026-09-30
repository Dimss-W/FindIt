<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/**
 * Vercel Serverless Entry Point for FINDIT UBSI (Laravel 10)
 */

// 1. Static asset fallback handler (ensures images/icons/css/js always load regardless of outputDirectory setting)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicFile = __DIR__ . '/../public' . $uri;

if ($uri !== '/' && $uri !== '/index.php' && is_file($publicFile) && strtolower(pathinfo($publicFile, PATHINFO_EXTENSION)) !== 'php') {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'html'  => 'text/html; charset=utf-8',
        'txt'   => 'text/plain; charset=utf-8',
        'xml'   => 'application/xml; charset=utf-8',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
        header('Cache-Control: public, max-age=86400');
        readfile($publicFile);
        exit;
    }
}

// 2. Prepare writable directories in /tmp for Vercel Serverless (read-only filesystem)
$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 3. Set safe default environment variables for Vercel if not configured in Vercel Dashboard
$defaultEnv = [
    'APP_NAME'           => 'FINDIT - UBSI Lost & Found',
    'APP_ENV'            => 'production',
    'APP_KEY'            => 'base64:7wJUwAs+BBtZeKJjJItzBNRs7+h3GUmpNCelhtScUzw=',
    'APP_DEBUG'          => 'true',
    'APP_TIMEZONE'       => 'Asia/Jakarta',
    'APP_LOCALE'         => 'id',
    'LOG_CHANNEL'        => 'stderr',
    'SESSION_DRIVER'     => 'cookie',
    'CACHE_DRIVER'       => 'array',
    'FILESYSTEM_DISK'    => 'public',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE'   => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'   => '/tmp/bootstrap/cache/routes.php',
    'APP_EVENTS_CACHE'   => '/tmp/bootstrap/cache/events.php',
];

foreach ($defaultEnv as $key => $val) {
    if (empty(getenv($key)) && empty($_ENV[$key]) && empty($_SERVER[$key])) {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

// 4. Zero-Config Database Fallback on Vercel:
// If no external cloud MySQL DB_HOST is configured (or still set to 127.0.0.1), automatically use SQLite in /tmp
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
if (empty($dbHost) || $dbHost === '127.0.0.1' || $dbHost === 'localhost') {
    $sqlitePath = '/tmp/database.sqlite';
    if (!file_exists($sqlitePath)) {
        @touch($sqlitePath);
    }
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv("DB_DATABASE={$sqlitePath}");
    $_ENV['DB_DATABASE'] = $sqlitePath;
    $_SERVER['DB_DATABASE'] = $sqlitePath;
}

// 5. Boot Laravel Application directly
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
