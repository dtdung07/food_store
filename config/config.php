<?php
declare(strict_types=1);

define('APP_NAME', 'Food Store Management');
define('APP_ROOT', dirname(__DIR__));
define('APP_VERSION', '1.0.0');

$baseUrl = 'http://localhost/food_store';

if (PHP_SAPI !== 'cli' && isset($_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $basePath = rtrim($basePath, '/.');
    $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . ($basePath === '' ? '' : $basePath);
}

define('BASE_URL', rtrim($baseUrl, '/'));
define('ASSET_URL', BASE_URL . '/assets');
