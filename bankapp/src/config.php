<?php

declare(strict_types=1);

// Application configuration
const APP_NAME = 'BankApp';

// Prefer environment variables when available
$envDbHost = getenv('DB_HOST') ?: '127.0.0.1';
$envDbName = getenv('DB_NAME') ?: 'bankapp';
$envDbUser = getenv('DB_USER') ?: 'root';
$envDbPass = getenv('DB_PASS') ?: '';

// Database configuration array
$GLOBALS['DB_CONFIG'] = [
    'host' => $envDbHost,
    'name' => $envDbName,
    'user' => $envDbUser,
    'pass' => $envDbPass,
    'charset' => 'utf8mb4',
];

// Paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

// Session cookie settings
$GLOBALS['SESSION_CONFIG'] = [
    'cookie_lifetime' => 0,           // session cookie only
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
];