<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf_token(?string $token): bool
{
    start_secure_session();
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}