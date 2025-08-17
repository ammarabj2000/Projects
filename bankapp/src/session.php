<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $cfg = $GLOBALS['SESSION_CONFIG'];

    session_set_cookie_params([
        'lifetime' => $cfg['cookie_lifetime'],
        'path' => '/',
        'domain' => '',
        'secure' => $cfg['cookie_secure'],
        'httponly' => $cfg['cookie_httponly'],
        'samesite' => $cfg['cookie_samesite'],
    ]);

    ini_set('session.use_strict_mode', $cfg['use_strict_mode'] ? '1' : '0');

    session_start();
}

function regenerate_session_id(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}