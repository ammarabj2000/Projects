<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/db.php';

function password_hash_str(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function find_user_by_email(string $email): ?array
{
    $db = get_db();
    $stmt = $db->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function authenticate(string $email, string $password): bool
{
    start_secure_session();
    $user = find_user_by_email($email);
    if (!$user) {
        return false;
    }
    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    // Mitigate session fixation
    regenerate_session_id();

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'login_time' => time(),
    ];

    return true;
}

function require_login(): void
{
    start_secure_session();
    if (empty($_SESSION['user'])) {
        header('Location: /login.php');
        exit;
    }
}

function current_user(): ?array
{
    start_secure_session();
    return $_SESSION['user'] ?? null;
}

function logout(): void
{
    start_secure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}