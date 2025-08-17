<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Returns a shared PDO instance.
 */
function get_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = $GLOBALS['DB_CONFIG'];
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['name'], $cfg['charset']);

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
    } catch (Throwable $e) {
        http_response_code(500);
        echo 'Database connection failed.';
        error_log('DB connection error: ' . $e->getMessage());
        exit;
    }

    return $pdo;
}